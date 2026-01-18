<?php
require_once "../../session.php";
require_once "../../config.php";

header('Content-Type: application/json');

/* 1) Security */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
$role = $_SESSION['role'] ?? '';

/* 2) Data */
$filename = basename($_POST['filename'] ?? '');
if ($filename === '') {
    echo json_encode(['success' => false, 'message' => 'Filename missing']);
    exit;
}

/* 3) Determine target */
$targetUserId = $currentUserId;

if ($role === 'admin' && isset($_POST['user_id'])) {
    $targetUserId = (int) $_POST['user_id'];
}

if ($targetUserId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid target user']);
    exit;
}

/* 4) File Path */
$filePath = "../../uploads/user_" . $targetUserId . "/" . $filename;

/* 5) Delete */
if (!file_exists($filePath)) {
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

if (!unlink($filePath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete file from disk']);
    exit;
}

/* 6) Ensure table exists */
$db->query("
    CREATE TABLE IF NOT EXISTS starred_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_star (user_id, filename)
    )
");

/* 7) Remove from starred DB */
$stmt = $db->prepare("DELETE FROM starred_files WHERE user_id = ? AND filename = ?");
$stmt->bind_param("is", $targetUserId, $filename);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
exit;
?>
