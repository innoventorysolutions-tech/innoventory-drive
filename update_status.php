<?php
include "../database/db.php";

$id = $_GET['id'];
$action = $_GET['action'];

$status = ($action == "approve") ? "Approved" : "Denied";

$sql = "UPDATE approve SET status='$status' WHERE id='$id'";

if ($conn->query($sql)) {
    header("Location: ../index.php");
}
?>
