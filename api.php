<?php
require 'db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ১. Bulk Email Import (Copy Paste Feature)
if ($action == 'bulk_import') {
    $raw_emails = $_POST['emails'];
    // নিউ লাইন বা কমা দিয়ে আলাদা করা
    $email_list = preg_split("/[\r\n,]+/", $raw_emails);
    $count = 0;

    foreach ($email_list as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // ডুপ্লিকেট চেক করে ইনসার্ট
            $check = $conn->query("SELECT id FROM clients WHERE email='$email'");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO clients (email, status) VALUES ('$email', 'Pending')");
                $count++;
            }
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count Emails Added Successfully!"]);
    exit;
}

// ২. Template Save করা (Custom Design)
if ($action == 'save_template') {
    $subject = $conn->real_escape_string($_POST['subject']);
    $body = $conn->real_escape_string($_POST['body']);
    
    $conn->query("UPDATE email_campaign SET subject='$subject', body='$body' WHERE id=1");
    echo json_encode(['status' => 'success', 'message' => 'Email Template Saved!']);
    exit;
}

// ৩. ক্লায়েন্ট লিস্ট ফেচ করা (Client List & History)
if ($action == 'get_clients_list') {
    $filter = $_POST['filter'] ?? 'all'; // 'all', 'Sent', 'Pending'
    
    $sql = "SELECT * FROM clients";
    if ($filter != 'all') {
        $sql .= " WHERE status = '$filter'";
    }
    $sql .= " ORDER BY id DESC LIMIT 500"; // লাস্ট ৫০০ দেখাবে (লোড কমানোর জন্য)

    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
    exit;
}

// ৪. Resend করা (Status Reset)
if ($action == 'resend_email') {
    $id = $_POST['id'];
    $conn->query("UPDATE clients SET status='Pending' WHERE id=$id");
    echo json_encode(['status' => 'success']);
    exit;
}

// ৫. Stats
if ($action == 'get_stats') {
    $total = $conn->query("SELECT COUNT(*) as c FROM clients")->fetch_assoc()['c'];
    $sent = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Sent'")->fetch_assoc()['c'];
    $failed = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Failed'")->fetch_assoc()['c'];
    $pending = $total - ($sent + $failed);
    $percent = ($total > 0) ? round(($sent / $total) * 100) : 0;
    
    // টেম্পলেট লোড
    $template = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();

    echo json_encode([
        'total' => $total, 'sent' => $sent, 'failed' => $failed, 
        'pending' => $pending, 'percent' => $percent,
        'subject' => $template['subject'], 'body' => $template['body']
    ]);
    exit;
}
?>