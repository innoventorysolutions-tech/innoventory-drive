<?php
header('Content-Type: application/json');
include "config.php";

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : '';
$sql = "SELECT id, name, email, role, status, COALESCE(notes,'') as notes FROM requests";
$params = [];
if($status && in_array($status,['pending','approved','denied'])){
  $sql .= " WHERE status = ?";
  $params[] = $status;
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if($params){
  $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while($row = $res->fetch_assoc()){
  $out[] = $row;
}
echo json_encode($out);
