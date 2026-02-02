<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "ultima_mailer_pro"; 

// ১. কানেকশন
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'DB Connection Failed: ' . $conn->connect_error]));
}

// ২. ডাটাবেস চেক এবং তৈরি
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);
$conn->set_charset("utf8mb4");

// ৩. টেবিল স্ট্রাকচার (UNIQUE KEY removed from clients)
$tables = [
    "clients" => "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        email VARCHAR(255) DEFAULT NULL, 
        status VARCHAR(50) DEFAULT 'Pending'
        -- UNIQUE KEY removed to allow duplicates
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "smtp_accounts" => "CREATE TABLE IF NOT EXISTS smtp_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        host VARCHAR(255) DEFAULT NULL, 
        email VARCHAR(255) DEFAULT NULL, 
        password VARCHAR(255) DEFAULT NULL, 
        port INT DEFAULT 587, 
        daily_limit INT DEFAULT 500, 
        today_sent INT DEFAULT 0,
        last_used TIMESTAMP NULL DEFAULT NULL, 
        UNIQUE KEY(email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "email_campaign" => "CREATE TABLE IF NOT EXISTS email_campaign (
        id INT PRIMARY KEY, 
        subject VARCHAR(255) DEFAULT NULL, 
        body LONGTEXT DEFAULT NULL, 
        sender_name VARCHAR(255) DEFAULT NULL, 
        logo_url VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $name => $sql) {
    if (!$conn->query($sql)) {
        die("Error creating table $name: " . $conn->error);
    }
}

// ৪. মিসিং কলাম চেক (Auto-Fix Feature)
$col_check = $conn->query("SHOW COLUMNS FROM smtp_accounts LIKE 'last_used'");
if ($col_check->num_rows == 0) {
    $conn->query("ALTER TABLE smtp_accounts ADD last_used TIMESTAMP NULL DEFAULT NULL");
}

// ৫. ডিফল্ট ক্যাম্পেইন ডাটা
$chk_camp = $conn->query("SELECT * FROM email_campaign LIMIT 1");
if ($chk_camp->num_rows == 0) {
    $conn->query("INSERT INTO email_campaign (id, subject, body, sender_name, logo_url) 
    VALUES (1, 'Payment Notification', 'Your transaction is successful.', 'Service Team', 'https://via.placeholder.com/150')");
}
?>