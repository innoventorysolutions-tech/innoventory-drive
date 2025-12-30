<?php
session_start();
include "db.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Innoventory</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
    .login-card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    h2 { text-align: center; color: #333; margin-bottom: 24px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; margin-bottom: 8px; color: #666; font-weight: 500; }
    input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
    button { width: 100%; background: #007bff; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    button:hover { background: #0056b3; }
    .error { color: #dc3545; text-align: center; margin-bottom: 16px; font-size: 14px; }
    .links { text-align: center; margin-top: 16px; font-size: 14px; }
    .links a { color: #007bff; text-decoration: none; }
</style>
</head>
<body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Sign In</button>
        </form>
        <div class="links">
            Don't have an account? <a href="register.php">Register</a><br><br>
            <a href="apply.php">← Back to Application</a>
        </div>
    </div>
</body>
</html>
