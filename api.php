<?php
require 'db.php';
header('Content-Type: application/json');

// আনলিমিটেড মেইল অ্যাড করার জন্য টাইম লিমিট অফ করা হলো
set_time_limit(0);
ini_set('memory_limit', '-1');

$action = $_POST['action'] ?? '';

// ১. Unlimited Bulk Email Import
if ($action == 'bulk_import') {
    $raw_emails = $_POST['emails'];
    // নিউ লাইন বা কমা দিয়ে আলাদা করা
    $email_list = preg_split("/[\r\n,]+/", $raw_emails);
    $count = 0;

    // Prepared Statement ব্যবহার করে ফাস্ট ইনসার্ট
    $stmt_check = $conn->prepare("SELECT id FROM clients WHERE email = ?");
    $stmt_insert = $conn->prepare("INSERT INTO clients (email, status) VALUES (?, 'Pending')");

    foreach ($email_list as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // ডুপ্লিকেট চেক
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows == 0) {
                $stmt_insert->bind_param("s", $email);
                $stmt_insert->execute();
                $count++;
            }
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count Emails Added Successfully!"]);
    exit;
}

// ২. Template Save করা
if ($action == 'save_template') {
    $subject = $conn->real_escape_string($_POST['subject']);
    $body = $conn->real_escape_string($_POST['body']);
    
    // আগের টেমপ্লেট আছে কি না চেক
    $check = $conn->query("SELECT * FROM email_campaign WHERE id=1");
    if($check->num_rows == 0){
        $conn->query("INSERT INTO email_campaign (id, subject, body) VALUES (1, '$subject', '$body')");
    } else {
        $conn->query("UPDATE email_campaign SET subject='$subject', body='$body' WHERE id=1");
    }
    echo json_encode(['status' => 'success', 'message' => 'Email Template Saved!']);
    exit;
}

// ৩. ক্লায়েন্ট লিস্ট ফেচ করা
if ($action == 'get_clients_list') {
    $filter = $_POST['filter'] ?? 'all'; 
    
    $sql = "SELECT * FROM clients";
    if ($filter != 'all') {
        $sql .= " WHERE status = '$filter'";
    }
    $sql .= " ORDER BY id DESC LIMIT 1000"; // পারফরমেন্সের জন্য ১০০০ লিমিট

    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
    exit;
}

// ৪. Resend করা (FIXED)
if ($action == 'resend_email') {
    $id = $_POST['id'];
    $conn->query("UPDATE clients SET status='Pending' WHERE id=$id");
    echo json_encode(['status' => 'success']);
    exit;
}

// ৫. Stats & Last Template Load
if ($action == 'get_stats') {
    $total = $conn->query("SELECT COUNT(*) as c FROM clients")->fetch_assoc()['c'];
    $sent = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Sent'")->fetch_assoc()['c'];
    $failed = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Failed'")->fetch_assoc()['c'];
    $pending = $total - ($sent + $failed);
    $percent = ($total > 0) ? round(($sent / $total) * 100) : 0;
    
    // লাস্ট সেভ করা টেমপ্লেট লোড
    $template = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();
    
    // যদি ডাটাবেসে না থাকে তবে ডিফল্ট ভ্যালু
    $subj = $template ? $template['subject'] : '';
    $body = $template ? $template['body'] : '';

    echo json_encode([
        'total' => $total, 'sent' => $sent, 'failed' => $failed, 
        'pending' => $pending, 'percent' => $percent,
        'subject' => $subj, 'body' => $body
    ]);
    exit;
}
?>