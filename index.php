<?php
session_start();
require_once "config.php";

// NOT LOGGED IN → GO TO LOGIN
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: user-management/login.php");
    exit;
}

// FETCH USER FROM DB (TO CHECK STATUS)
$id = $_SESSION["id"];
$query = $db->prepare("SELECT status, role FROM users WHERE id=? LIMIT 1");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// STATUS CHECK
if ($user["status"] === "pending") {
    header("Location: user-management/pending.php");
    exit;
}

if ($user["status"] === "denied") {
    header("Location: user-management/denied.php");
    exit;
}

// ROLE CHECK
if ($user["role"] === "admin") {
    header("Location: user-management/admin_dashboard.php");
    exit;
}

if ($user["role"] === "user") {
    header("Location: user-management/user_dashboard.php");
    exit;
}

// DEFAULT (should not hit)
header("Location: user-management/login.php");
exit;
?>
