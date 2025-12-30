<?php
session_start();
include "db.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $check = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully! <a href='login.php'>Login here</a>";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Register - Innoventory</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
    .login-card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    h2 { text-align: center; color: #333; margin-bottom: 24px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; margin-bottom: 8px; color: #666; font-weight: 500; }
    input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
    button { width: 100%; background: #28a745; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    button:hover { background: #218838; }
    .error { color: #dc3545; text-align: center; margin-bottom: 16px; font-size: 14px; }
    .success { color: #28a745; text-align: center; margin-bottom: 16px; font-size: 14px; }
    .links { text-align: center; margin-top: 16px; font-size: 14px; }
    .links a { color: #007bff; text-decoration: none; }
</style>
</head>
<body>
    <div class="login-card">
        <h2>Create Admin Account</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <div class="links">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
