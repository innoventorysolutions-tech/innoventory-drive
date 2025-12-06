<?php
require_once "db.php";

$name   = $_POST['name'];
$email  = $_POST['email'];
$role   = $_POST['role'];
$action = $_POST['action'];
$status = $_POST['status'];

$sql = "INSERT INTO Approve (name, email, role, action, status)
        VALUES ('$name', '$email', '$role', '$action', '$status')";

if ($mysqli->query($sql)) {
    header("Location: index.php");
} else {
    echo "Error: " . $mysqli->error;
}
?>
