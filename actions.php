<?php
include "db.php";
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$ids = $_POST['ids'] ?? [];

if ($action && !empty($ids)) {
    $status = ($action === 'approve') ? 'approved' : 'denied';
    
    // Sanitize IDs
    $safeIds = array_map('intval', $ids);
    $idStr = implode(',', $safeIds);
    
    if (!empty($safeIds)) {
        $sql = "UPDATE users SET status='$status' WHERE id IN ($idStr)";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No valid IDs provided']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>