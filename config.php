<?php
$DB_HOST = 'localhost';
$DB_NAME = 'inmvonetry_db';
$DB_USER = 'root';
$DB_PASS = '';
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error) die("DB err: ".$conn->connect_error);
$conn->set_charset("utf8mb4");
?>
