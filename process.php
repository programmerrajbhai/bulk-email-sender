<?php
// Strict Error Reporting Off
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

if (!file_exists('PHPMailer/src/PHPMailer.php')) {
    echo json_encode(['status' => 'error', 'log' => "<span style='color:red'>PHPMailer Missing!</span>"]);
    exit;
}

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// ১. পেন্ডিং ক্লায়েন্ট সিলেক্ট
$client_res = $conn->query("SELECT * FROM clients WHERE status = 'Pending' LIMIT 1");
if ($client_res->num_rows == 0) {
    echo json_encode(['status' => 'finished']);
    exit;
}
$client = $client_res->fetch_assoc();
$to_email = strtolower($client['email']);

// ২. SMTP সিলেকশন (Smart Routing Logic)
$smtp = null;
$domain_match = false;

// ডোমেইন ডিটেকশন ও রাউটিং
if (strpos($to_email, 'yahoo') !== false) {
    // Yahoo রিসিভারের জন্য Yahoo সেন্ডার খুঁজবে
    $sql = "SELECT * FROM smtp_accounts WHERE today_sent < daily_limit AND email LIKE '%yahoo%' ORDER BY last_used ASC LIMIT 1";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) { $smtp = $res->fetch_assoc(); $domain_match = true; }
} 
elseif (strpos($to_email, 'outlook') !== false || strpos($to_email, 'hotmail') !== false) {
    // Outlook রিসিভারের জন্য Outlook সেন্ডার
    $sql = "SELECT * FROM smtp_accounts WHERE today_sent < daily_limit AND (email LIKE '%outlook%' OR email LIKE '%hotmail%' OR email LIKE '%live%') ORDER BY last_used ASC LIMIT 1";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) { $smtp = $res->fetch_assoc(); $domain_match = true; }
}

// ম্যাচিং না পেলে বা অন্য ডোমেইন হলে জেনারেল রোটেশন
if (!$smtp) {
    $smtp_res = $conn->query("SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY last_used ASC, id ASC LIMIT 1");
    if ($smtp_res->num_rows == 0) {
        echo json_encode(['status' => 'quota_error']);
        exit;
    }
    $smtp = $smtp_res->fetch_assoc();
}

// ৩. ক্যাম্পেইন লোড
$camp = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();

// ডিলে (Yahoo টু Yahoo হলে একটু স্লো সেন্ডিং ভালো)
$delay = (strpos($smtp['email'], 'yahoo') !== false) ? 3 : 1; 
sleep($delay);

$mail = new PHPMailer(true);
try {
    // সার্ভার সেটিংস
    $mail->isSMTP();
    $mail->Host       = $smtp['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp['email'];
    $mail->Password   = $smtp['password'];
    $mail->Port       = $smtp['port'];
    
    // এনক্রিপশন (465 = SSL, 587 = TLS)
    if ($smtp['port'] == 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64'; 

    // --- HEADERS SETUP ---
    $mail->setFrom($smtp['email'], $camp['sender_name']); 
    $mail->addAddress($client['email']);
    
    // Yahoo স্প্যাম ফিক্স: Reply-To অবশ্যই Sender হতে হবে
    $mail->addReplyTo($smtp['email'], $camp['sender_name']);

    // --- MESSAGE-ID LOGIC (CRITICAL FIX) ---
    // Yahoo এর জন্য আমরা কাস্টম Message-ID দেবো না, সার্ভারকে দিতে দেবো।
    // অন্যথায় Yahoo এটাকে স্প্যাম হিসেবে ডিটেক্ট করে।
    if (stripos($smtp['host'], 'yahoo') === false && stripos($smtp['host'], 'aol') === false) {
        // Gmail/Outlook এর জন্য কাস্টম ID (Trust Score বাড়ায়)
        $host_parts = parse_url($smtp['host']);
        $server_domain = isset($host_parts['host']) ? $host_parts['host'] : $smtp['host'];
        
        if (stripos($smtp['host'], 'gmail') !== false) {
            $msg_domain = 'mail.gmail.com';
        } elseif (stripos($smtp['host'], 'office365') !== false) {
            $msg_domain = 'eur.prd.01.prod.outlook.com';
        } else {
            $msg_domain = $server_domain;
        }
        $mail->MessageID = "<" . md5(uniqid(time()) . $smtp['email']) . "@" . $msg_domain . ">";
    }
    // Yahoo/AOL এর জন্য $mail->MessageID সেট করা হলো না (অটোমেটিক জেনারেট হবে)

    // --- GENERIC HEADERS ---
    // Yahoo এর জন্য X-Mailer ব্ল্যাঙ্ক রাখাই ভালো, অথবা জেনেরিক নাম
    $mail->XMailer = 'Microsoft Outlook 16.0'; 
    $mail->Priority = 3; 
    
    // List-Unsubscribe (Yahoo ইনবক্সের জন্য বাধ্যতামূলক)
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:'.$smtp['email'].'?subject=Unsubscribe>');
    
    // কনটেন্ট
    $mail->isHTML(true);
    $mail->Subject = $camp['subject'];
    
    $htmlBody = str_replace('{{LOGO}}', $camp['logo_url'], $camp['body']);
    
    // ফুটার
    $footer = "<div style='margin-top:20px; padding-top:10px; border-top:1px solid #ddd; font-size:11px; color:#777;'>
        <p>This message was sent to {$client['email']}.</p>
        <p><a href='mailto:{$smtp['email']}?subject=Unsubscribe' style='color:#555; text-decoration:none;'>Unsubscribe</a></p>
    </div>";
    
    $mail->Body = $htmlBody . $footer;
    $mail->AltBody = strip_tags($htmlBody);

    $mail->send();

    // ডাটাবেস আপডেট
    $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + 1, last_used = NOW() WHERE id = " . $smtp['id']);

    // লগ
    $routeTag = $domain_match ? "<span style='color:#0ea5e9'>[Smart]</span>" : "";
    echo json_encode([
        'status' => 'success', 
        'log' => "<span style='color:#10b981'>✔ Sent: {$client['email']}</span> <small style='color:#94a3b8'>via {$smtp['email']} $routeTag</small>"
    ]);

} catch (Exception $e) {
    $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
    
    $errorMsg = $mail->ErrorInfo;
    // এরর মেসেজ ক্লিন করা
    if (strpos($errorMsg, 'connect()') !== false) $errorMsg = "Connection Timeout";
    elseif (strpos($errorMsg, 'Authentication') !== false) $errorMsg = "Auth Failed (Check Pass)";
    elseif (strpos($errorMsg, 'data not accepted') !== false) $errorMsg = "Spam Rejected";

    echo json_encode([
        'status' => 'error', 
        'log' => "<span style='color:#ef4444'>❌ Failed: {$client['email']}</span> <small style='color:#fbbf24'>($errorMsg)</small>"
    ]);
}
?>