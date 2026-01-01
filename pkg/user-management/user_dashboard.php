<?php
require_once "../../session.php";
require_once "../../config.php";

<<<<<<< HEAD
=======
// --- START: PREVENT BROWSER CACHING (ADD THIS) ---
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// --- END: PREVENT BROWSER CACHING ---

// 1. Security Check: Ensure only 'user' role can access
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "user") {
    header("Location: ../../index.php");
    exit;
}
>>>>>>> aab77d8
// Check if user is logged in and is a user (not admin)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../../index.php");
    exit;
}

// Check user status
$id = $_SESSION["id"];
$query = $db->prepare("SELECT status, role FROM users WHERE id=? LIMIT 1");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user["role"] === "admin") {
    header("Location: admin_dashboard.php");
    exit;
}

if ($user["status"] === "pending") {
    header("Location: ../../index.php?error=pending");
    exit;
}

if ($user["status"] === "denied") {
    header("Location: ../../index.php?error=denied");
    exit;
}

$query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Innoventory</title>
    <link rel="stylesheet" href="../../css/main.css">
    <style>
        body {
<<<<<<< HEAD
            background: #f6f6f9;
            padding: 20px;
=======
            background: var(--bg);
            padding: 20px;
            color: var(--text);
>>>>>>> aab77d8
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
<<<<<<< HEAD
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
=======
            background: var(--panel);
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--soft-shadow);
>>>>>>> aab77d8
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
<<<<<<< HEAD
            border-bottom: 1px solid #e5e7eb;
        }
        .header-left h1 {
            margin: 0;
            color: #16191f;
=======
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .header-left h1 {
            margin: 0;
            color: var(--text);
>>>>>>> aab77d8
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-info {
<<<<<<< HEAD
            color: #6b7280;
=======
            color: var(--muted);
>>>>>>> aab77d8
            font-size: 14px;
        }
        .btn-logout {
            padding: 8px 16px;
            background: #d13212;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-logout:hover {
            background: #b0280f;
        }
        .welcome-message {
<<<<<<< HEAD
            background: #dbeafe;
            color: #1e40af;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }
        .welcome-message h2 {
            margin: 0 0 10px 0;
            color: #1e40af;
        }
        .welcome-message p {
            margin: 0;
            color: #1e3a8a;
=======
            background: rgba(59,130,246,0.06);
            color: var(--accent-strong);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid rgba(59,130,246,0.25);
        }
        .welcome-message h2 {
            margin: 0 0 10px 0;
            color: var(--accent-strong);
        }
        .welcome-message p {
            margin: 0;
            color: var(--accent-strong);
>>>>>>> aab77d8
        }
    </style>
</head>
<body>
    <?php include '../../common/header.php'; ?>

    <div class="app-grid">
        <aside class="sidebar">
<<<<<<< HEAD
=======
            <div class="site-logo">
                <a href="/innoventory/index.php"><img src="/innoventory/logo/logo.png" alt="Innoventory logo"></a>
            </div>
>>>>>>> aab77d8
            <a href="/innoventory/pkg/user-management/user_dashboard.php" class="btn-new">+ New</a>
            <ul class="menu">
                <li class="active"><a href="/innoventory/pkg/user-management/user_dashboard.php">Dashboard</a></li>
                <li><a href="/innoventory/">My Drive</a></li>
                <li><a href="/innoventory/#shared">Shared</a></li>
<<<<<<< HEAD
                <li><a href="/innoventory/pkg/user-management/admin_dashboard.php">Users</a></li>
=======
                <!-- Users link hidden for non-admin users -->
>>>>>>> aab77d8
                <li><a href="/innoventory/#starred">Starred</a></li>
                <li><a href="/innoventory/#bin">Bin</a></li>
                <li><a href="/innoventory/#storage">Storage</a></li>
            </ul>
<<<<<<< HEAD
=======
            <?php
            // Compute storage usage for current user (best-effort)
            $storageAllocated = 0; // in GB
            $storageUsedBytes = 0;
            $uid = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
            if ($uid) {
                $urow = $db->prepare("SELECT storage_gb FROM users WHERE id=? LIMIT 1");
                if ($urow) {
                    $urow->bind_param("i", $uid);
                    $urow->execute();
                    $urow->bind_result($storageAllocated);
                    $urow->fetch();
                    $urow->close();
                }

                // Look for common user storage folders and sum sizes (best-effort)
                $root = dirname(__DIR__, 2); // site root
                $candidates = [
                    $root . '/uploads/' . $uid,
                    $root . '/files/' . $uid,
                    $root . '/storage/' . $uid,
                    $root . '/user_files/' . $uid,
                    $root . '/data/users/' . $uid,
                    $root . '/user/' . $uid,
                ];

                function dir_size($path) {
                    $size = 0;
                    if (!is_dir($path)) return 0;
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
                    foreach ($it as $file) { if ($file->isFile()) $size += $file->getSize(); }
                    return $size;
                }

                foreach ($candidates as $p) {
                    if (is_dir($p)) {
                        $storageUsedBytes += dir_size($p);
                    }
                }
            }

            $allocatedBytes = max(0, intval($storageAllocated)) * 1024 * 1024 * 1024;
            $usedGB = round($storageUsedBytes / (1024*1024*1024), 2);
            $percent = ($allocatedBytes > 0) ? round(($storageUsedBytes / $allocatedBytes) * 100, 1) : 0;
            ?>

            <div class="storage-widget" title="Storage used">
                <svg class="chart" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <?php $r=18; $c = 2*M_PI*$r; $dash = ($percent/100)*$c; ?>
                    <circle cx="22" cy="22" r="<?php echo $r; ?>" stroke="rgba(0,0,0,0.06)" stroke-width="6" fill="none" />
                    <circle cx="22" cy="22" r="<?php echo $r; ?>" stroke="var(--accent)" stroke-width="6" fill="none"
                        stroke-dasharray="<?php echo $dash; ?> <?php echo $c; ?>" stroke-linecap="round" transform="rotate(-90 22 22)" />
                    <text x="22" y="26" text-anchor="middle" font-size="10" fill="var(--text)"><?php echo intval($percent); ?>%</text>
                </svg>
                <div class="meta">
                    <span class="amount"><?php echo $usedGB; ?> / <?php echo intval($storageAllocated); ?> GB</span>
                    <span class="label">Storage used</span>
                </div>
            </div>
            <div style="padding:12px 16px 18px;">
                <a href="logout.php" class="btn-logout" style="display:block; width:100%; text-align:center;">Logout</a>
            </div>
>>>>>>> aab77d8
        </aside>

        <main>
            <div class="dashboard-container">
                <div class="header-section">
                    <div class="header-left">
                        <h1>User Dashboard</h1>
                    </div>
                    <div class="header-right">
                        <span class="user-info">Welcome, <?= htmlspecialchars($_SESSION["name"] ?? "User"); ?></span>
<<<<<<< HEAD
                        <a href="logout.php" class="btn-logout">Logout</a>
                    </div>
                </div>

=======
                    </div>
                </div>

                <?php if (!empty($_SESSION['show_welcome'])): ?>
>>>>>>> aab77d8
                <div class="welcome-message">
                    <h2>Welcome to Innoventory!</h2>
                    <p>You have successfully logged in. Your account has been approved and you can now access the system.</p>
                </div>
<<<<<<< HEAD

                <div style="text-align: center; padding: 40px; color: #6b7280;">
=======
                <?php unset($_SESSION['show_welcome']); endif; ?>

                <div style="text-align: center; padding: 40px; color: var(--muted);">
>>>>>>> aab77d8
                    <p>More features coming soon...</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>