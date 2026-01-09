<?php
// Database configuration
$host = "localhost";            // Usually localhost in XAMPP
$dbname = "innoventory_db";     // Correct database name from your screenshot
$username = "root";             // Default XAMPP MySQL username
$password = "";                 // Default XAMPP MySQL password (empty)

// Create PDO connection
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
