<?php
require_once "../../session.php";
require_once "../../config.php"; // $db is defined here

/* ---------- SECURITY ---------- */
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

/* ---------- FETCH USERS ---------- */
$pendingStmt  = $db->prepare("SELECT * FROM users WHERE status='pending' ORDER BY name");
$pendingStmt->execute();
$pending = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$approvedStmt = $db->prepare("SELECT id,name,email,role,storage_gb,user_home_path FROM users WHERE status='approved' ORDER BY name");
$approvedStmt->execute();
$approved = $approvedStmt->fetchAll(PDO::FETCH_ASSOC);

$deniedStmt   = $db->prepare("SELECT id,name,email,role,storage_gb FROM users WHERE status='denied' ORDER BY name");
$deniedStmt->execute();
$denied = $deniedStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- DASHBOARD METRICS ---------- */
$totalUsersStmt = $db->prepare("SELECT COUNT(*) as total FROM users");
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['total'];

$approvedUsersStmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE status='approved'");
$approvedUsersStmt->execute();
$approvedUsers = $approvedUsersStmt->fetch(PDO::FETCH_ASSOC)['total'];

$pendingUsersStmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE status='pending'");
$pendingUsersStmt->execute();
$pendingUsers = $pendingUsersStmt->fetch(PDO::FETCH_ASSOC)['total'];

$totalStorageStmt = $db->prepare("SELECT SUM(storage_gb) as total FROM users WHERE status='approved'");
$totalStorageStmt->execute();
$totalStorage = $totalStorageStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Progress bar calculation (Assuming a 100GB limit for the whole system, change as needed)
$limit = 100; 
$percentage = ($totalStorage / $limit) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Innoventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #fff;
            --main-bg: #f8f9fa;
            --text-color: #3c4043;
            --active-bg: #e8f0fe;
            --active-color: #1967d2;
            --hover-bg: #f1f3f4;
        }

        body.dark {
            --sidebar-bg: #1e1e1e;
            --main-bg: #121212;
            --text-color: #e8eaed;
            --active-bg: #3c4043;
            --active-color: #8ab4f8;
            --hover-bg: #2d2e30;
        }

        body { margin: 0; font-family: 'Google Sans', Roboto, Arial, sans-serif; background: var(--main-bg); color: var(--text-color); transition: 0.3s; }
        .app { display: flex; height: 100vh; overflow: hidden; }

        /* ---------- SIDEBAR (Google Drive Style) ---------- */
        .sidebar { 
            width: 280px; 
            background: var(--sidebar-bg); 
            padding: 12px; 
            display: flex; 
            flex-direction: column; 
            border-right: 1px solid rgba(0,0,0,0.1);
        }
        
        .sidebar-logo { padding: 10px 10px 20px; width: 150px; }

        /* "New" Button Style */
        .btn-new {
            display: flex; align-items: center; width: fit-content;
            padding: 12px 24px; margin-bottom: 16px; border-radius: 16px;
            background: white; border: 1px solid #dadce0;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
            cursor: pointer; font-size: 14px; font-weight: 500;
        }
        .btn-new i { font-size: 20px; margin-right: 12px; color: #4285f4; }
        .dark .btn-new { background: #3c4043; border-color: #5f6368; color: white; }

        .nav-item {
            display: flex; align-items: center; padding: 10px 20px;
            text-decoration: none; color: inherit; font-size: 14px;
            border-radius: 0 25px 25px 0; margin-right: 10px; margin-bottom: 2px;
        }
        .nav-item i { width: 24px; margin-right: 18px; font-size: 18px; text-align: center; }
        .nav-item:hover { background: var(--hover-bg); }
        .nav-item.active { background: var(--active-bg); color: var(--active-color); font-weight: bold; }

        .sidebar-divider { border-top: 1px solid rgba(0,0,0,0.1); margin: 10px 0; }

        /* Storage Section */
        .storage-section { margin-top: auto; padding: 20px 10px; }
        .storage-bar-container { height: 4px; background: #e8eaed; border-radius: 2px; margin: 8px 0; overflow: hidden; }
        .storage-bar-fill { height: 100%; background: #1967d2; transition: 0.5s; }
        .storage-text { font-size: 13px; color: #5f6368; }
        .dark .storage-text { color: #bdc1c6; }

        /* ---------- MAIN CONTENT ---------- */
        main { flex: 1; padding: 24px; overflow-y: auto; }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        
        #search {
            width: 100%; max-width: 600px; padding: 12px 20px;
            background: #f1f3f4; border: none; border-radius: 8px;
            font-size: 16px; outline: none;
        }
        .dark #search { background: #2d2e30; color: white; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; }
        .dark table { background: #1e1e1e; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05); }
        th { background: rgba(0,0,0,0.02); font-weight: 500; font-size: 13px; color: #5f6368; }
        
        .badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .pending { background: #fef7e0; color: #b06000; }
        .admin { background: #e8f0fe; color: #1967d2; }
        .user { background: #f1f3f4; color: #3c4043; }
        .denied { background: #fce8e6; color: #d93025; }

        .toggle-btn { padding: 8px 16px; cursor: pointer; border-radius: 8px; border: 1px solid #dadce0; background: white; }
        .dark .toggle-btn { background: #3c4043; color: white; border-color: #5f6368; }
    </style>
</head>
<body>

<div class="app">
    <div class="sidebar">
        <img src="/innoventory/logo/logo.png" class="sidebar-logo" alt="Logo">
        
        <div class="btn-new" onclick="location.href='add_user.php'">
            <i class="fa-solid fa-plus"></i> New
        </div>

        <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="/innoventory/" class="nav-item"><i class="fa-solid fa-hard-drive"></i> My Drive</a>
        <a href="/innoventory/pkg/user-management/users.php" class="nav-item"><i class="fa-solid fa-users"></i> Users</a>
        <a href="/innoventory/pkg/user-management/analysis.php" class="nav-item"><i class="fa-solid fa-chart-line"></i> Analysis</a>
        
        <div class="sidebar-divider"></div>
        
        <a href="#" class="nav-item"><i class="fa-solid fa-user-group"></i> Shared with me</a>
        <a href="#" class="nav-item"><i class="fa-solid fa-clock"></i> Recent</a>
        <a href="#" class="nav-item"><i class="fa-solid fa-star"></i> Starred</a>
        
        <div class="sidebar-divider"></div>

        <a href="#" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Spam</a>
        <a href="#" class="nav-item"><i class="fa-solid fa-trash-can"></i> Bin</a>
        <a href="logout.php" class="nav-item" style="color:#d93025"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>

        <div class="storage-section">
            <div class="nav-item" style="padding:0; margin-bottom:5px;">
                <i class="fa-solid fa-cloud"></i> <span style="font-size:14px">Storage</span>
            </div>
            <div class="storage-bar-container">
                <div class="storage-bar-fill" style="width: <?=$percentage?>%;"></div>
            </div>
            <div class="storage-text">
                <?=$totalStorage?> GB of <?=$limit?> GB used
            </div>
            <button class="toggle-btn" style="width:100%; margin-top:15px;" onclick="toggleMode()">
                <i class="fa-solid fa-circle-half-stroke"></i> Switch Theme
            </button>
        </div>
    </div>

    <main>
        <div class="header-top">
            <h1>Admin Dashboard</h1>
            <input id="search" placeholder="Search by name, email, or status...">
        </div>

        <h3>Pending Requests (<?=$pendingUsers?>)</h3>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Storage</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php if(count($pending)==0): ?>
                    <tr><td colspan="5">No pending users</td></tr>
                <?php endif; foreach($pending as $u): ?>
                <tr>
                    <td><?=$u['name']?></td>
                    <td><?=$u['email']?></td>
                    <td><span class="badge <?=$u['role']?>"><?=$u['role']?></span></td>
                    <td><?=$u['storage_gb']?> GB</td>
                    <td>
                        <a href="approve.php?id=<?=$u['id']?>" style="color:#16a34a; text-decoration:none; margin-right:10px;">Approve</a>
                        <a href="deny.php?id=<?=$u['id']?>" style="color:#dc2626; text-decoration:none;">Deny</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 style="margin-top:40px;">Approved Users (<?=$approvedUsers?>)</h3>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Storage</th><th>Path</th></tr>
            </thead>
            <tbody>
                <?php foreach($approved as $u): ?>
                <tr>
                    <td><?=$u['name']?></td>
                    <td><?=$u['email']?></td>
                    <td><span class="badge <?=$u['role']?>"><?=$u['role']?></span></td>
                    <td><?=$u['storage_gb']?> GB</td>
                    <td style="font-family:monospace; font-size:12px;"><?=$u['user_home_path']?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<script>
    // Search functionality
    document.getElementById("search").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll("tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    // Theme Toggle
    function toggleMode() {
        document.body.classList.toggle("dark");
        localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
    }
    if(localStorage.getItem("theme") === "dark") document.body.classList.add("dark");
</script>

</body>
</html>
