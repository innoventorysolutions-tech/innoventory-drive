<?php
$conn = new mysqli("localhost", "root", "", "inmvonetry_db");
if ($conn->connect_error) { die("Connection failed: ".$conn->connect_error); }

$id = $_GET['id'];
$conn->query("DELETE FROM requests WHERE id='$id'");
$conn->close();
header("Location: index.php");
exit;
?>
