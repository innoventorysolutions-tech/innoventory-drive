<?php
session_start();
require_once "config.php";

// Security check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

// SAFE session access (NO warning)
$username = $_SESSION['username'] ?? 'User';
$role     = $_SESSION['role'] ?? 'N/A';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
<p>Your role: <?= htmlspecialchars($role) ?></p>

<a href="logout.php">Logout</a>

</body>
</html>

