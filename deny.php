<?php
include "config.php";
if(!isset($_GET['id'])) { http_response_code(400); echo "Missing id"; exit;}
$id = (int)$_GET['id'];
$stmt = $conn->prepare("UPDATE requests SET status='denied' WHERE id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
echo "ok";
