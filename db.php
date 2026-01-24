<?php
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP এ সাধারণত পাসওয়ার্ড ফাঁকা থাকে
$dbname = "bulk_email_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>