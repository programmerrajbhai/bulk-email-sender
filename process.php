<?php
require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// ১. টেম্পলেট লোড করা (Subject & Body)
$campaign_res = $conn->query("SELECT * FROM email_campaign WHERE id=1");
if($campaign_res->num_rows > 0){
    $campaign = $campaign_res->fetch_assoc();
    $emailSubject = $campaign['subject'];
    $emailBody = $campaign['body'];
} else {
    $emailSubject = "Test Email";
    $emailBody = "This is a test email.";
}

// ২. ক্লায়েন্ট সিলেক্ট করা (Pending)
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

// ৩. সেন্ডার সিলেক্ট করা
$smtp_sql = "SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY RAND() LIMIT 1";
$smtp_result = $conn->query($smtp_sql);

if ($smtp_result->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'No SMTP account available or limit exceeded!']);
    exit;
}

$sender = $smtp_result->fetch_assoc();

// ৪. মেইল পাঠানো শুরু
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
    
    // SSL Fix for XAMPP
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom($sender['email'], 'Support Team'); // আপনার ব্র্যান্ড নেম দিন

    foreach ($clients as $client) {
        try {
            $mail->addAddress($client['email']);
            $mail->isHTML(true);
            
            // ডাটাবেস থেকে পাওয়া সাবজেক্ট এবং বডি সেট করা
            $mail->Subject = $emailSubject;
            $mail->Body    = $emailBody;

            $mail->send();

            $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
            $sent_count++;
            $log .= "<div class='log-entry text-success'>✅ Sent to: " . $client['email'] . "</div>";
            $mail->clearAddresses();
            
        } catch (Exception $e) {
             $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
             $log .= "<div class='log-entry text-danger'>❌ Failed: " . $client['email'] . "</div>";
        }
    }

    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + $sent_count WHERE id = " . $sender['id']);
    echo json_encode(['status' => 'success', 'sent' => $sent_count, 'log' => $log]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Mailer Error: " . $mail->ErrorInfo]);
}
?>