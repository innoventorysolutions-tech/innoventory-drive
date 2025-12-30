<?php
include 'db.php';

if(isset($_GET['id'], $_GET['status'])){
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    
    $validStatuses = ['Pending', 'Approved', 'Denied', 'pending', 'approved', 'denied'];

    if(in_array($status, $validStatuses)){
        $status = mysqli_real_escape_string($conn, strtolower($status));
        $sql = "UPDATE users SET status='$status' WHERE id=$id";
        mysqli_query($conn, $sql);
    }
}
header("Location: index.php"); 
exit;
?>
