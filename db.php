<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "innoventory_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>