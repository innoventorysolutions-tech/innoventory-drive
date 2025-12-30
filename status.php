<?php
include "db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = $_GET['status'] ?? '';

if ($id > 0 && $status) {
    $status = mysqli_real_escape_string($conn, $status);
    mysqli_query($conn, "UPDATE users SET status='$status' WHERE id=$id");
}

header("Location: index.php");
exit;
?>
