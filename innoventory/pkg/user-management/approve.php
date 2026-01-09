<?php
require_once "../../session.php";
require_once "../../config.php"; // Make sure this defines $db as PDO

// --------- ADMIN ACCESS ONLY ----------
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

// --------- CHECK USER ID ----------
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php?msg=Error: User ID missing");
    exit;
}

$userId = intval($_GET['id']);

// --------- CHECK DATABASE CONNECTION ----------
if (!isset($db)) {
    die("Database connection not found. Check config.php");
}

// --------- FETCH USER INFO ----------
$stmt = $db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_dashboard.php?msg=Error: User not found");
    exit;
}

$email = $user['email'];

// --------- UUID V4 FUNCTION ----------
function generate_uuid_v4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// --------- CREATE USER FOLDER ----------
$uuid = generate_uuid_v4();
$modifiedEmail = preg_replace('/[^a-zA-Z0-9]/', '_', $email);
$folderName = $modifiedEmail . "_" . $uuid;
$rootPath = dirname(__DIR__, 2); // project root
$fullPath = $rootPath . "/files/" . $folderName . "/files";

if (!is_dir($fullPath)) {
    mkdir($fullPath, 0755, true);
}

// --------- UPDATE USER STATUS ----------
$updateStmt = $db->prepare("UPDATE users SET status = 'approved', user_home_path = ? WHERE id = ?");
$updateStmt->execute([$fullPath, $userId]);

// --------- REDIRECT WITH SUCCESS ----------
header("Location: admin_dashboard.php?msg=User approved successfully!");
exit;
?>






