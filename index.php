<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Welcome <?= $_SESSION['user'] ?></h2>

<a href="list.php">View Requests</a><br>
<a href="add.php">Add Request</a><br><br>

<a href="logout.php">Logout</a>

</body>
</html>
