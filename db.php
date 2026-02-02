<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "bulk_email_db";

// কানেকশন
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'DB Connection Failed']));
}

// ডাটাবেস তৈরি
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);
$conn->set_charset("utf8mb4");

// টেবিল সেটআপ
$tables = [
    "clients" => "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        email VARCHAR(255) UNIQUE, 
        status VARCHAR(50) DEFAULT 'Pending'
    )",
    "smtp_accounts" => "CREATE TABLE IF NOT EXISTS smtp_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        host VARCHAR(255), 
        email VARCHAR(255), 
        password VARCHAR(255), 
        port INT DEFAULT 587, 
        daily_limit INT DEFAULT 500, 
        today_sent INT DEFAULT 0,
        last_used TIMESTAMP NULL DEFAULT NULL, 
        UNIQUE KEY(email)
    )",
    "email_campaign" => "CREATE TABLE IF NOT EXISTS email_campaign (
        id INT PRIMARY KEY, 
        subject VARCHAR(255), 
        body LONGTEXT, 
        sender_name VARCHAR(255),
        logo_url VARCHAR(255)
    )"
];

foreach ($tables as $sql) { $conn->query($sql); }

// ডিফল্ট ক্যাম্পেইন
$conn->query("INSERT IGNORE INTO email_campaign (id, subject, body, sender_name, logo_url) 
VALUES (1, 'Payment Notification', 'Your transaction is successful.', 'Service Team', 'https://via.placeholder.com/150')");
?>