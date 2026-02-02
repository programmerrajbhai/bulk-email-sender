<?php
require 'db.php';
header('Content-Type: application/json');
error_reporting(0);

$action = $_POST['action'] ?? '';

// ১. বাল্ক ইম্পোর্ট (Duplicate Allowed)
if ($action == 'bulk_import') {
    $raw = $_POST['emails'];
    $list = preg_split("/[\r\n, ]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;
    
    // Change: INSERT IGNORE -> INSERT INTO
    $stmt = $conn->prepare("INSERT INTO clients (email, status) VALUES (?, 'Pending')");
    
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

// ... baki code same thakbe ...
// (Nicher code gulo apnar ager file er motoi rakhun, just bulk_import part ta uporer moto change korun)

// ২. SMTP অ্যাড
if ($action == 'add_smtp') {
    $raw = $_POST['accounts'];
    $lines = preg_split("/[\r\n]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;
    $stmt = $conn->prepare("INSERT INTO smtp_accounts (host, email, password, port, daily_limit, today_sent) VALUES (?, ?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE password=?, host=?, port=?");

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $p = explode('|', $line);
        $size = count($p);
        if ($size >= 2) {
            $pass = trim($p[$size-1]);
            $email = trim($p[$size-2]);
            $host = 'smtp.gmail.com'; 
            $port = 587;
            $limit = 500;
            if ($size >= 3) $host = trim($p[0]);
            else {
                if (stripos($email, 'yahoo') !== false) { $host = 'smtp.mail.yahoo.com'; $port = 465; }
                elseif (stripos($email, 'outlook') !== false || stripos($email, 'hotmail') !== false) { $host = 'smtp.office365.com'; $port = 587; }
                elseif (stripos($email, 'aol') !== false) { $host = 'smtp.aol.com'; $port = 465; }
            }
            $stmt->bind_param("sssiisssi", $host, $email, $pass, $port, $limit, $pass, $host, $port);
            $stmt->execute();
            if ($stmt->affected_rows > 0) $count++;
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count SMTP Accounts Added!"]);
    exit;
}

if ($action == 'save_template') {
    $stmt = $conn->prepare("UPDATE email_campaign SET subject=?, body=?, sender_name=?, logo_url=? WHERE id=1");
    $sub = $_POST['subject']; $body = $_POST['body']; $send = $_POST['sender_name']; $logo = $_POST['logo_url'];
    $stmt->bind_param("ssss", $sub, $body, $send, $logo);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

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

if ($action == 'reset_quota') { $conn->query("UPDATE smtp_accounts SET today_sent = 0"); exit; }
if ($action == 'resend_email') { $id = (int)$_POST['id']; $conn->query("UPDATE clients SET status='Pending' WHERE id=$id"); exit; }
if ($action == 'get_clients_list') {
    $filter = $_POST['filter'] ?? 'all';
    $filter_safe = $conn->real_escape_string($filter);
    $sql = "SELECT * FROM clients " . ($filter != 'all' ? "WHERE status='$filter_safe'" : "") . " ORDER BY id DESC LIMIT 100";
    $res = $conn->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode(['data' => $data]);
    exit;
}
?>