<?php
include "config.php";

$name = $_POST['name'];
$sql = "INSERT INTO requests(name, status) VALUES('$name', 'Pending')";

mysqli_query($conn, $sql);

header("Location: list.php");
?>
