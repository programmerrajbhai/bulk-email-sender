<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

// PHPMailer Check
if (!file_exists('PHPMailer/src/PHPMailer.php')) {
    echo json_encode(['status' => 'error', 'log' => "<span style='color:red'>PHPMailer Missing!</span>"]);
    exit;
}

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ১. পেন্ডিং কাস্টমার চেক
$client_res = $conn->query("SELECT * FROM clients WHERE status = 'Pending' LIMIT 1");
if ($client_res->num_rows == 0) {
    echo json_encode(['status' => 'finished']);
    exit;
}
$client = $client_res->fetch_assoc();

// ২. SMTP রোটেশন (Round Robin Logic)
// লজিক: যার কোটা আছে এবং অনেকক্ষণ ব্যবহার হয়নি (last_used ASC) তাকে নাও
$smtp_res = $conn->query("SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY last_used ASC LIMIT 1");

if ($smtp_res->num_rows == 0) {
    echo json_encode(['status' => 'quota_error']);
    exit;
}
$smtp = $smtp_res->fetch_assoc();

// ৩. মেইল কন্টেন্ট
$camp = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();

// --- ANTI-SPAM TRICKS ---
// ট্রিক ১: ইউনিক আইডি জেনারেশন (Message-ID এবং Body এর জন্য)
$unique_id = md5(uniqid(mt_rand(), true));
$invisible_hash = "<span style='display:none; color:#ffffff; font-size:0px;'>Ref: $unique_id</span>";

// ট্রিক ২: মেইলার হেডার রোটেশন (Fake User Agent)
$user_agents = [
    'Microsoft Outlook 16.0',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'iPhone Mail (18E212)',
    'Thunderbird 91.0'
];
$random_agent = $user_agents[array_rand($user_agents)];

// ৪. মেইল সেন্ডিং
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $smtp['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp['email'];
    $mail->Password   = $smtp['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtp['port'];
    
    // স্প্যাম বাইপাস এনকোডিং
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64'; 

    // SSL ফিক্স
    $mail->SMTPOptions = [
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
    ];

    // --- Headers ---
    // From অবশ্যই SMTP ইউজার হতে হবে
    $mail->setFrom($smtp['email'], $camp['sender_name']); 
    $mail->addAddress($client['email']);
    $mail->addReplyTo($smtp['email'], $camp['sender_name']);
    
    // Custom Headers for Trust Score
    $mail->addCustomHeader('X-Mailer', $random_agent);
    $mail->addCustomHeader('X-Priority', '3'); // Normal Priority (High দিলে স্প্যাম হয়)
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:'.$smtp['email'].'?subject=unsubscribe>');

    // --- Body ---
    $mail->isHTML(true);
    $mail->Subject = $camp['subject'];
    
    // লোগো + ইনভিজিবল হ্যাশ যুক্ত করা
    $htmlBody = str_replace('{{LOGO}}', $camp['logo_url'], $camp['body']);
    $mail->Body = $htmlBody . "<br>" . $invisible_hash;
    
    // প্লেইন টেক্সট ভার্সন (খুবই জরুরি স্প্যাম এড়াতে)
    $mail->AltBody = strip_tags($htmlBody) . "\n\nRef: $unique_id";

    $mail->send();

    // --- সফল: ডাটাবেস আপডেট ---
    $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
    // last_used আপডেট করা যাতে পরের বার এই মেইল সবার শেষে সিরিয়াল পায় (Looping)
    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + 1, last_used = NOW() WHERE id = " . $smtp['id']);

    echo json_encode([
        'status' => 'success', 
        'log' => "<span style='color:#10b981'>✔ Sent: {$client['email']}</span> <small style='color:#94a3b8'>via {$smtp['email']}</small>"
    ]);

} catch (Exception $e) {
    // ফেইল
    $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
    echo json_encode([
        'status' => 'error', 
        'log' => "<span style='color:#ef4444'>❌ Failed: {$client['email']}</span> <br><small style='color:#fbbf24'>{$mail->ErrorInfo}</small>"
    ]);
}
?>