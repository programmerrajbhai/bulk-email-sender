<?php
// Strict Error Reporting Off for Production
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

// PHPMailer চেক
if (!file_exists('PHPMailer/src/PHPMailer.php')) {
    echo json_encode(['status' => 'error', 'log' => "<span style='color:red'>PHPMailer Missing! Upload PHPMailer folder.</span>"]);
    exit;
}

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// ১. ক্লায়েন্ট সিলেক্ট (যাকে মেইল পাঠানো হয়নি)
$client_res = $conn->query("SELECT * FROM clients WHERE status = 'Pending' LIMIT 1");
if ($client_res->num_rows == 0) {
    echo json_encode(['status' => 'finished']);
    exit;
}
$client = $client_res->fetch_assoc();

// ২. SMTP রোটেশন (Smart Round Robin with Load Balancing)
// লজিক: যার কোটা আছে এবং অনেকক্ষণ ব্যবহার হয়নি তাকে আগে নাও
$smtp_res = $conn->query("SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY last_used ASC, id ASC LIMIT 1");

if ($smtp_res->num_rows == 0) {
    echo json_encode(['status' => 'quota_error']);
    exit;
}
$smtp = $smtp_res->fetch_assoc();

// ৩. ক্যাম্পেইন লোড
$camp = $conn->query("SELECT * FROM email_campaign WHERE id=1")->fetch_assoc();

// --- DEEP LOGIC: Message-ID Customization ---
// Gmail এবং Outlook এর জন্য আলাদা ডোমেইন ফরম্যাট তৈরি করা
$host_parts = parse_url($smtp['host']);
$server_domain = isset($host_parts['host']) ? $host_parts['host'] : $smtp['host'];

// Gmail এর জন্য স্পেশাল প্যাটার্ন, অন্যদের জন্য জেনেরিক
if (stripos($smtp['host'], 'gmail') !== false) {
    $msg_domain = 'mail.gmail.com'; 
} elseif (stripos($smtp['host'], 'office365') !== false || stripos($smtp['host'], 'outlook') !== false) {
    $msg_domain = 'eur.prd.01.prod.outlook.com'; // Outlook Internal Server Mimic
} else {
    $msg_domain = $server_domain;
}

// ইউনিক ট্র্যাকিং আইডি
$unique_id = uniqid('ref_', true);
$message_id = "<" . md5(uniqid(time()) . $smtp['email']) . "@" . $msg_domain . ">";

// --- HUMAN BEHAVIOR SIMULATION ---
// রোবোটিক আচরণ এড়াতে ১-২ সেকেন্ড র‍্যান্ডম ডিলে (Gmail এর জন্য খুবই জরুরি)
sleep(rand(1, 2));

// ৪. মেইল কনফিগারেশন
$mail = new PHPMailer(true);
try {
    // সার্ভার সেটিংস
    $mail->isSMTP();
    $mail->Host       = $smtp['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp['email'];
    $mail->Password   = $smtp['password'];
    
    // --- PORT & SSL INTELLIGENCE ---
    $mail->Port = $smtp['port'];
    
    // Gmail সাধারণত TLS (587) পছন্দ করে, Yahoo/AOL SSL (465) পছন্দ করে
    if ($smtp['port'] == 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    // SSL Certificate Bypass (লোকালহোস্ট বা শেয়ারড হোস্টিং এ এরর ফিক্স করতে)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'quoted-printable'; // Base64 এর চেয়ে স্প্যাম স্কোর কম

    // --- HEADER MANIPULATION (The Secret Sauce) ---
    
    // ১. Sender এবং Reply-To একই রাখা (Trust Score বাড়ায়)
    $mail->setFrom($smtp['email'], $camp['sender_name']); 
    $mail->addAddress($client['email']);
    $mail->addReplyTo($smtp['email'], $camp['sender_name']);
    
    // ২. X-Mailer লুকানো (Bot Detection এড়ানো)
    $mail->XMailer = ' '; // এটাকে খালি রাখলে PHPMailer ডিফল্ট নাম পাঠাবে না
    
    // ৩. Custom Message ID (Gmail/Outlook কে বোকা বানানোর জন্য)
    $mail->MessageID = $message_id;

    // ৪. Essential Headers for Inbox Delivery
    $mail->Priority = 3; // 3 = Normal. (1 দিলে স্প্যামে যায়)
    $mail->addCustomHeader('MIME-Version', '1.0');
    $mail->addCustomHeader('X-Report-Abuse', 'Please reply with UNSUBSCRIBE'); // Yahoo ট্রাস্ট করে
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:'.$smtp['email'].'?subject=unsubscribe>');
    $mail->addCustomHeader('Auto-Submitted', 'auto-generated'); // Outlook এর জন্য

    // --- Body & Content Optimization ---
    $mail->isHTML(true);
    $mail->Subject = $camp['subject'];
    
    // লোগো বসানো
    $htmlBody = str_replace('{{LOGO}}', $camp['logo_url'], $camp['body']);
    
    // ক্লিন ফুটার (ফিজিক্যাল এড্রেস না থাকলে স্প্যামে যায়)
    $footer_style = "color:#6b7280; font-size:11px; line-height:1.5; margin-top:30px; border-top:1px solid #e5e7eb; padding-top:10px;";
    $footer = "<div style='$footer_style'>
        This email was sent to {$client['email']}. If you did not request this, please ignore it.<br>
        <a href='mailto:{$smtp['email']}?subject=Unsubscribe' style='color:#6366f1; text-decoration:none;'>Unsubscribe</a> | Privacy Policy
    </div>";
    
    $mail->Body = $htmlBody . $footer;
    
    // প্লেইন টেক্সট ভার্সন (Anti-Spam এর জন্য বাধ্যতামূলক)
    // HTML ট্যাগ সরিয়ে শুধু টেক্সট রাখা
    $mail->AltBody = strip_tags(str_replace(['<br>', '</div>'], ["\n", "\n"], $htmlBody)) . "\n\nUnsubscribe: Reply with UNSUBSCRIBE";

    // ৫. সেন্ড করা
    $mail->send();

    // --- সফল: ডাটাবেস আপডেট ---
    $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
    
    // SMTP Usage আপডেট + Last Used Time
    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + 1, last_used = NOW() WHERE id = " . $smtp['id']);

    echo json_encode([
        'status' => 'success', 
        'log' => "<span style='color:#10b981'>✔ Sent: {$client['email']}</span> <small style='color:#94a3b8'>via {$smtp['email']}</small>"
    ]);

} catch (Exception $e) {
    // ফেইল হ্যান্ডলিং
    $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
    
    // এরর মেসেজ ক্লিন করা
    $rawError = $mail->ErrorInfo;
    $shortError = "Connection Error";
    
    // কমন এরর ডিটেকশন
    if (strpos($rawError, 'connect()') !== false) $shortError = "Connection Blocked (Check Port/Pass)";
    if (strpos($rawError, 'Authentication') !== false) $shortError = "Wrong Password/App Password";
    if (strpos($rawError, 'Data not accepted') !== false) $shortError = "Spam Filter Blocked Content";

    echo json_encode([
        'status' => 'error', 
        'log' => "<span style='color:#ef4444'>❌ Failed: {$client['email']}</span> <br><small style='color:#fbbf24'>$shortError</small>"
    ]);
}
?>