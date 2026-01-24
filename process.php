<?php
require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// ১. ক্লায়েন্ট সিলেক্ট করা (যাদের Status Pending)
// আমরা প্রতি রিকোয়েস্টে ৫ জন করে নিব
$limit = 5; 
$sql = "SELECT * FROM clients WHERE status = 'Pending' LIMIT $limit";
$result = $conn->query($sql);

$clients = [];
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

// যদি পেন্ডিং ক্লায়েন্ট না থাকে
if (count($clients) == 0) {
    echo json_encode(['status' => 'finished', 'message' => 'All emails sent!']);
    exit;
}

// ২. সেন্ডার সিলেক্ট করা (Rotation Logic)
// এমন সেন্ডার খুঁজব যার লিমিট শেষ হয়নি
$smtp_sql = "SELECT * FROM smtp_accounts WHERE today_sent < daily_limit ORDER BY RAND() LIMIT 1";
$smtp_result = $conn->query($smtp_sql);

if ($smtp_result->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'All SMTP Accounts Daily Limit Exceeded!']);
    exit;
}

$sender = $smtp_result->fetch_assoc();

// ৩. লুপ চালানো এবং মেইল পাঠানো
$sent_count = 0;
$log = "";

$mail = new PHPMailer(true);

try {
    // সার্ভার কনফিগারেশন (ডাইনামিক)
    $mail->isSMTP();
    $mail->Host       = $sender['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $sender['email'];
    $mail->Password   = $sender['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $sender['port'];

    // সেন্ডার সেটআপ
    $mail->setFrom($sender['email'], 'My Brand Name'); // নাম চেঞ্জ করতে পারেন

    foreach ($clients as $client) {
        try {
            // প্রাপক সেট করা
            $mail->addAddress($client['email']);

            // কন্টেন্ট
            $mail->isHTML(true);
            $mail->Subject = 'Special Offer for You!';
            $mail->Body    = 'Hello, this is a test email from our new system.';

            $mail->send();

            // সফল হলে ডাটাবেস আপডেট
            $conn->query("UPDATE clients SET status = 'Sent' WHERE id = " . $client['id']);
            $sent_count++;
            $log .= "Sent to: " . $client['email'] . " using " . $sender['email'] . "<br>";
            
            // সেন্ডার ক্লিয়ার করা (পরের লুপের জন্য)
            $mail->clearAddresses();
            
        } catch (Exception $e) {
             $conn->query("UPDATE clients SET status = 'Failed' WHERE id = " . $client['id']);
             $log .= "Failed: " . $client['email'] . " Error: " . $mail->ErrorInfo . "<br>";
        }
    }

    // ৪. সেন্ডার কাউন্ট আপডেট করা
    $conn->query("UPDATE smtp_accounts SET today_sent = today_sent + $sent_count WHERE id = " . $sender['id']);

    echo json_encode(['status' => 'success', 'sent' => $sent_count, 'log' => $log]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Mailer Error: " . $mail->ErrorInfo]);
}
?>