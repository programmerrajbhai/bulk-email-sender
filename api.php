<?php
require 'db.php';
header('Content-Type: application/json');
error_reporting(0);

$action = $_POST['action'] ?? '';

// ১. বাল্ক ইম্পোর্ট
if ($action == 'bulk_import') {
    $raw = $_POST['emails'];
    $list = preg_split("/[\r\n, ]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;
    $stmt = $conn->prepare("INSERT IGNORE INTO clients (email, status) VALUES (?, 'Pending')");
    
    foreach ($list as $email) {
        $email = strtolower(trim($email));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->affected_rows > 0) $count++;
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count Emails Imported!"]);
    exit;
}

// ২. SMTP অ্যাড (Smart Parser)
if ($action == 'add_smtp') {
    $raw = $_POST['accounts'];
    $lines = preg_split("/[\r\n]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        $p = explode('|', $line);
        $size = count($p);

        if ($size >= 2) {
            $pass = trim($p[$size-1]);
            $email = trim($p[$size-2]);
            $host = 'smtp.gmail.com'; 

            if ($size == 3) {
                $host = trim($p[0]);
            } else {
                if (stripos($email, 'yahoo') !== false) $host = 'smtp.mail.yahoo.com';
                elseif (stripos($email, 'outlook') !== false || stripos($email, 'hotmail') !== false) $host = 'smtp.office365.com';
            }

            $conn->query("INSERT INTO smtp_accounts (host, email, password, port, daily_limit, today_sent) 
                          VALUES ('$host', '$email', '$pass', 587, 500, 0)
                          ON DUPLICATE KEY UPDATE password='$pass', host='$host'");
            $count++;
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count SMTP Accounts Added!"]);
    exit;
}

// ৩. টেমপ্লেট সেভ
if ($action == 'save_template') {
    $sub = $conn->real_escape_string($_POST['subject']);
    $body = $conn->real_escape_string($_POST['body']);
    $send = $conn->real_escape_string($_POST['sender_name']);
    $logo = $conn->real_escape_string($_POST['logo_url']);
    $conn->query("UPDATE email_campaign SET subject='$sub', body='$body', sender_name='$send', logo_url='$logo' WHERE id=1");
    echo json_encode(['status' => 'success']);
    exit;
}

// ৪. স্ট্যাটাস
if ($action == 'get_stats') {
    $total = $conn->query("SELECT COUNT(*) as c FROM clients")->fetch_assoc()['c'];
    $sent = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Sent'")->fetch_assoc()['c'];
    $failed = $conn->query("SELECT COUNT(*) as c FROM clients WHERE status='Failed'")->fetch_assoc()['c'];
    $pending = $total - ($sent + $failed);
    $quota = $conn->query("SELECT SUM(daily_limit - today_sent) as rem FROM smtp_accounts")->fetch_assoc()['rem'] ?? 0;
    $tpl = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();

    echo json_encode([
        'total' => $total, 'sent' => $sent, 'failed' => $failed, 
        'pending' => $pending, 'quota' => $quota,
        'subject' => $tpl['subject'], 'body' => $tpl['body'], 
        'sender_name' => $tpl['sender_name'], 'logo_url' => $tpl['logo_url']
    ]);
    exit;
}

// ৫. রিসেট
if ($action == 'reset_quota') {
    $conn->query("UPDATE smtp_accounts SET today_sent = 0");
    exit;
}
if ($action == 'resend_email') {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE clients SET status='Pending' WHERE id=$id");
    exit;
}
if ($action == 'get_clients_list') {
    $filter = $_POST['filter'] ?? 'all';
    $sql = "SELECT * FROM clients " . ($filter != 'all' ? "WHERE status='$filter'" : "") . " ORDER BY id DESC LIMIT 100";
    $res = $conn->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode(['data' => $data]);
    exit;
}
?>