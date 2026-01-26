<?php
require 'db.php';
header('Content-Type: application/json');

// আনলিমিটেড টাইম এবং মেমোরি
set_time_limit(0);
ini_set('memory_limit', '-1');

$action = $_POST['action'] ?? '';

// ১. আনলিমিটেড বাল্ক ইমেইল ইম্পোর্ট (Duplicate Allowed)
if ($action == 'bulk_import') {
    $raw_emails = $_POST['emails'];
    $email_list = preg_split("/[\r\n,]+/", $raw_emails);
    $count = 0;

    // সরাসরি ইনসার্ট (কোনো ডুপ্লিকেট চেক নেই)
    $stmt = $conn->prepare("INSERT INTO clients (email, status) VALUES (?, 'Pending')");

    foreach ($email_list as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $count++;
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count Emails Added Successfully!"]);
    exit;
}

// ২. টেমপ্লেট সেভ
if ($action == 'save_template') {
    $subject = $conn->real_escape_string($_POST['subject']);
    $body = $conn->real_escape_string($_POST['body']);
    $sender_name = $conn->real_escape_string($_POST['sender_name']);
    
    $check = $conn->query("SELECT id FROM email_campaign WHERE id=1");
    if($check->num_rows == 0){
        $conn->query("INSERT INTO email_campaign (id, subject, body, sender_name) VALUES (1, '$subject', '$body', '$sender_name')");
    } else {
        $conn->query("UPDATE email_campaign SET subject='$subject', body='$body', sender_name='$sender_name' WHERE id=1");
    }
    echo json_encode(['status' => 'success', 'message' => 'Campaign Saved!']);
    exit;
}

// ৩. রিসেন্ড ফিক্স
if ($action == 'resend_email') {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE clients SET status='Pending' WHERE id=$id");
    echo json_encode(['status' => 'success']);
    exit;
}

// ৪. কোটা রিসেট (নতুন ফিচার)
if ($action == 'reset_quota') {
    $conn->query("UPDATE smtp_accounts SET today_sent = 0");
    echo json_encode(['status' => 'success', 'message' => 'Daily Limit Reset Successfully!']);
    exit;
}

// ৫. স্ট্যাটাস লোড
if ($action == 'get_stats') {
    $total = $conn->query("SELECT COUNT(*) as c FROM clients")->fetch_assoc()['c'];
    $sent = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Sent'")->fetch_assoc()['c'];
    $failed = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Failed'")->fetch_assoc()['c'];
    $pending = $total - ($sent + $failed);
    $percent = ($total > 0) ? round(($sent / $total) * 100) : 0;
    
    $tpl = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();
    
    echo json_encode([
        'total' => $total, 'sent' => $sent, 'failed' => $failed, 
        'pending' => $pending, 'percent' => $percent,
        'subject' => $tpl['subject'] ?? '', 
        'body' => $tpl['body'] ?? '', 
        'sender_name' => $tpl['sender_name'] ?? 'Support Team'
    ]);
    exit;
}

// ৬. ক্লায়েন্ট লিস্ট
if ($action == 'get_clients_list') {
    $filter = $_POST['filter'] ?? 'all';
    $sql = "SELECT * FROM clients";
    if ($filter != 'all') $sql .= " WHERE status = '$filter'";
    $sql .= " ORDER BY id DESC LIMIT 500";
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode(['data' => $data]);
    exit;
}
?>