<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// ১. টেম্পলেট ও সেন্ডার নেম লোড
$campaign_res = $conn->query("SELECT * FROM email_campaign WHERE id=1");
if($campaign_res->num_rows > 0){
    $campaign = $campaign_res->fetch_assoc();
    $emailSubject = $campaign['subject'];
    $emailBody = $campaign['body'];
    $senderName = $campaign['sender_name'] ?? 'Support Team';
} else {
    $emailSubject = "Notification";
    $emailBody = "Default message.";
    $senderName = "Support Team";
}

// ২. ক্লায়েন্ট সিলেক্ট
$limit = 5; 
$sql = "SELECT * FROM clients WHERE status = 'Pending' LIMIT $limit";
$result = $conn->query($sql);

$clients = [];
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

if (count($clients) == 0) {
    echo json_encode(['status' => 'finished', 'message' => 'All emails sent!']);
    exit;
}

// ৩. SMTP সিলেক্ট
$smtp_sql = "SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY RAND() LIMIT 1";
$smtp_result = $conn->query($smtp_sql);

if ($smtp_result->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Limit Exceeded! Please Reset Quota from Dashboard.']);
    exit;
}

$sender = $smtp_result->fetch_assoc();

// ৪. মেইল সেন্ডিং
$sent_count = 0;
$log = "";
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $sender['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $sender['email'];
    $mail->Password   = $sender['password']; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $sender['port'];
    $mail->CharSet = 'UTF-8';
    $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

    $mail->setFrom($sender['email'], $senderName); // Custom Name

    foreach ($clients as $client) {
        try {
            $mail->addAddress($client['email']);
            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body    = $emailBody;
            $mail->send();

            $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
            $sent_count++;
            $log .= "<div class='text-success'>✅ Sent to: " . $client['email'] . "</div>";
            $mail->clearAddresses();
        } catch (Exception $e) {
             $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
             $log .= "<div class='text-danger'>❌ Failed: " . $client['email'] . "</div>";
        }
    }

    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + $sent_count WHERE id = " . $sender['id']);
    echo json_encode(['status' => 'success', 'sent' => $sent_count, 'log' => $log]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Mailer Error: " . $mail->ErrorInfo]);
}
?>