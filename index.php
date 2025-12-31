<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if config file exists
if (!file_exists("config.php")) {
    die("Error: config.php file not found. Please check your file structure.");
}

require_once "config.php";

// Check database connection
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error() . "<br>Please check your database settings in config.php");
}

// 1. Handle Login Logic if Form Submitted
$error = "";

// Check for error messages from redirects
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'pending') {
        $error = "Your account is still pending approval.";
    } elseif ($_GET['error'] === 'denied') {
        $error = "Your access has been denied.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $login_type = isset($_POST['login_type']) && $_POST['login_type'] === 'admin' ? 'admin' : 'user';
    $password = trim($_POST['password']);

    if ($login_type === 'admin') {
        // Admins can sign in with username (name) OR their admin email; enforce role = 'admin'
        $username = trim($_POST['username']);
        $stmt = $db->prepare("SELECT id, name, password, role, status FROM users WHERE (name = ? OR email = ?) AND role = 'admin' LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
    } else {
        // Regular users sign in with email and must have role = 'user'
        $email = trim($_POST['email']);
        $stmt = $db->prepare("SELECT id, name, password, role, status FROM users WHERE email = ? AND role = 'user' LIMIT 1");
        $stmt->bind_param("s", $email);
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $role, $status);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if ($status === 'pending') {
                $error = "Your account is still pending approval.";
            } elseif ($status === 'denied') {
                $error = "Your access has been denied.";
            } else {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["role"] = $role;

                if ($role === 'admin') {
                    header("Location: pkg/user-management/admin_dashboard.php");
                } else {
                    header("Location: pkg/user-management/user_dashboard.php");
                }
                exit;
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        if ($login_type === 'admin') {
            $error = "No admin account found with that username or email.";
        } else {
            $error = "No account found with that email.";
        }
    }
    $stmt->close();
}

// 2. If already logged in, redirect to appropriate dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // FETCH USER FROM DB (TO CHECK STATUS)
    $id = $_SESSION["id"];
    $query = $db->prepare("SELECT status, role FROM users WHERE id=? LIMIT 1");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    // STATUS CHECK
    if ($user["status"] === "pending") {
        // Could redirect to a pending page if it exists
        $error = "Your account is still pending approval.";
    } elseif ($user["status"] === "denied") {
        $error = "Your access has been denied.";
    } else {
        // ROLE CHECK
        if ($user["role"] === "admin") {
            header("Location: pkg/user-management/admin_dashboard.php");
            exit;
        } elseif ($user["role"] === "user") {
            header("Location: pkg/user-management/user_dashboard.php");
            exit;
        }
    }
    $query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Innoventory</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'common/header.php'; ?>

    <div class="login-container">
        <h1>Sign in</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" id="login_type" name="login_type" value="user">

            <div id="userFields">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="username">
                </div>
            </div>

            <div id="adminFields" style="display:none;">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" placeholder="username or admin email" autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="password-options">
                <div class="checkbox-group">
                    <input type="checkbox" id="showPassword" onchange="togglePassword()">
                    <label for="showPassword">Show Password</label>
                </div>
            </div>

            <button type="submit" name="login_btn" class="btn-primary">Sign in</button>
        </form>

        <div class="actions-vertical">
            <button id="btnAdmin" class="btn-ghost">Sign in as Admin</button>
            <a href="pkg/user-management/register.php" class="btn-ghost">Request access as user</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const showPasswordCheckbox = document.getElementById('showPassword');
            if (showPasswordCheckbox.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }

        // Admin toggle behavior
        const btnAdmin = document.getElementById('btnAdmin');
        const userFields = document.getElementById('userFields');
        const adminFields = document.getElementById('adminFields');
        const loginTypeInput = document.getElementById('login_type');

        let adminMode = false;
        btnAdmin.addEventListener('click', function(e){
            e.preventDefault();
            adminMode = !adminMode;
            if (adminMode) {
                userFields.style.display = 'none';
                adminFields.style.display = 'block';
                loginTypeInput.value = 'admin';
                btnAdmin.textContent = 'Back to User Sign in';
            } else {
                userFields.style.display = 'block';
                adminFields.style.display = 'none';
                loginTypeInput.value = 'user';
                btnAdmin.textContent = 'Sign in as Admin';
            }
        });
    </script>
</body>
</html>