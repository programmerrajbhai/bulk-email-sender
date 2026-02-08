<?php
require 'db.php';
header('Content-Type: application/json');
error_reporting(0);

$action = $_POST['action'] ?? '';

// ১. বাল্ক ইম্পোর্ট (ক্লায়েন্ট ইমেইল)
if ($action == 'bulk_import') {
    $raw = $_POST['emails'];
    $list = preg_split("/[\r\n,]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;
    
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

// ২. SMTP অ্যাকাউন্ট অ্যাড (ফিক্সড ভার্সন)
if ($action == 'add_smtp') {
    $raw = $_POST['accounts'];
    $lines = preg_split("/[\r\n]+/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $count = 0;
    
    // SQL Query Prepared Statement
    $stmt = $conn->prepare("INSERT INTO smtp_accounts (host, email, password, port, daily_limit, today_sent) VALUES (?, ?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE password=?, host=?, port=?");

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // ট্যাব (\t), স্পেস (\s) বা পাইপ (|) দিয়ে আলাদা করা ডাটা ধরবে
        $p = preg_split("/[\s|\t]+/", $line, -1, PREG_SPLIT_NO_EMPTY);
        
        if (count($p) >= 2) {
            $email = trim($p[0]);
            $pass  = trim($p[count($p)-1]); // শেষেরটা পাসওয়ার্ড হিসেবে ধরবে
            
            // অটোমেটিক হোস্ট এবং পোর্ট ডিটেকশন লজিক
            $host = 'smtp.gmail.com'; 
            $port = 587; 
            $limit = 500;

            if (stripos($email, 'yahoo') !== false) { 
                $host = 'smtp.mail.yahoo.com'; 
                $port = 465; 
            }
            elseif (stripos($email, 'outlook') !== false || stripos($email, 'hotmail') !== false || stripos($email, 'live') !== false) { 
                $host = 'smtp.office365.com'; 
                $port = 587; 
            }
            elseif (stripos($email, 'aol') !== false) { 
                $host = 'smtp.aol.com'; 
                $port = 465; 
            }
            
            // ম্যানুয়াল হোস্ট থাকলে (যদি ৩টা অংশ থাকে)
            if (count($p) >= 3 && strpos($p[1], '.') !== false) {
                 $host = trim($p[1]);
            }

            // ⚠️ FIX: আগের কোডে এখানে ভুল ছিল (Parameter Count Mismatch)
            // এখন ৮টি টাইপ (sssiissi) এবং ৮টি ভেরিয়েবল মিল আছে
            $stmt->bind_param("sssiissi", $host, $email, $pass, $port, $limit, $pass, $host, $port);
            
            $stmt->execute();
            if ($stmt->affected_rows > 0) $count++;
        }
    }
    echo json_encode(['status' => 'success', 'message' => "$count SMTP Accounts Configured!"]);
    exit;
}

// টেমপ্লেট সেভ
if ($action == 'save_template') {
    $stmt = $conn->prepare("UPDATE email_campaign SET subject=?, body=?, sender_name=?, logo_url=? WHERE id=1");
    $stmt->bind_param("ssss", $_POST['subject'], $_POST['body'], $_POST['sender_name'], $_POST['logo_url']);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

// স্ট্যাটাস লোড
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