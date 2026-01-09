<?php
require_once "../../session.php";
require_once "../../config.php";

// ONLY ADMIN CAN ACCESS THIS PAGE
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    
    // Update user status to denied using PDO
    $query = $db->prepare("UPDATE users SET status = 'denied' WHERE id = ?");
    
    if ($query->execute([$id])) {
        header("Location: admin_dashboard.php?msg=User denied successfully");
        exit;
    } else {
        header("Location: admin_dashboard.php?msg=Error denying user");
        exit;
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
?>
