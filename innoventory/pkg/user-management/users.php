<?php
require_once "../../session.php";
require_once "../../config.php";

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Admin access only
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!==true || $_SESSION["role"]!=="admin"){
    header("Location: ../../index.php");
    exit;
}

// Fetch all users using PDO
$stmt = $db->prepare("SELECT * FROM users ORDER BY role, name"); // <-- fixed
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users - Innoventory</title>
<link rel="stylesheet" href="../../css/main.css">
<style>
body, html { margin:0; padding:0; font-family:Arial,sans-serif; background:#f5f7fa; height:100%; width:100%; }
.app-grid { display:flex; height:100vh; overflow:hidden; }
.sidebar { width:260px; background:#fff; border-right:1px solid #e0e0e0; display:flex; flex-direction:column; padding:20px; box-sizing:border-box; overflow-y:auto; }
.sidebar .site-logo img { width:160px; margin-bottom:25px; }
.sidebar .btn-new { margin-bottom:20px; text-align:center; padding:8px; border:1px solid #3b82f6; border-radius:6px; color:#3b82f6; font-weight:600; display:block; text-decoration:none; transition:all 0.2s; }
.sidebar .btn-new:hover { background:#3b82f6; color:#fff; }
.sidebar .menu { list-style:none; padding:0; margin:0; flex:1; }
.sidebar .menu li { margin-bottom:12px; }
.sidebar .menu li a { text-decoration:none; color:#4b5563; font-weight:500; padding:8px 12px; display:block; border-radius:6px; transition:0.2s; }
.sidebar .menu li.active a, .sidebar .menu li a:hover { background:#3b82f6; color:#fff; }
.sidebar .btn-logout { padding:8px 12px; text-align:center; border-radius:6px; background:#d13212; color:#fff; text-decoration:none; font-weight:500; transition:0.2s; }
.sidebar .btn-logout:hover { background:#b0280f; }
main { flex:1; overflow-y:auto; padding:30px 40px; box-sizing:border-box; }
table { width:100%; border-collapse:collapse; margin-top:20px; font-size:14px; background:#fff; border-radius:8px; overflow:hidden; }
table th, table td { padding:14px 12px; border-bottom:1px solid #e5e7eb; }
table th { background:#f3f4f6; font-weight:600; }
table tr:hover { background:#f9fafb; }
.role-badge { font-size:13px; padding:5px 14px; border-radius:12px; font-weight:600; }
.role-badge.admin { background:rgba(255,243,199,0.12); color:#92400e; }
.role-badge.user { background:rgba(219,234,254,0.06); color:#1e40af; }
</style>
</head>
<body>
<?php include '../../common/header.php'; ?>

<div class="app-grid">
    <aside class="sidebar">
        <div class="site-logo"><a href="/innoventory/index.php"><img src="/innoventory/logo/logo.png" alt="Innoventory logo"></a></div>
        <a href="/innoventory/pkg/user-management/users.php" class="btn-new">+ Add User</a>
        <ul class="menu">
            <li><a href="/innoventory/pkg/user-management/admin_dashboard.php">Dashboard</a></li>
            <li class="active"><a href="/innoventory/pkg/user-management/users.php">Users</a></li>
        </ul>
        <a href="logout.php" class="btn-logout">Logout</a>
    </aside>

    <main>
        <h1>All Users</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Storage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($users) > 0): 
                    foreach($users as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><span class="role-badge <?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                        <td><?= isset($row['storage_gb']) ? intval($row['storage_gb'])." GB" : "N/A" ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align:center;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>

