<?php
require_once "db.php";

$id = $_GET['id'];
$action = $_GET['action'];

if ($action == "approve") {
    $mysqli->query("UPDATE Approve SET status='approved' WHERE sno=$id");
}
elseif ($action == "deny") {
    $mysqli->query("UPDATE Approve SET status='denied' WHERE sno=$id");
}
elseif ($action == "delete") {
    $mysqli->query("DELETE FROM Approve WHERE sno=$id");
}

header("Location: index.php");
exit;
?>
