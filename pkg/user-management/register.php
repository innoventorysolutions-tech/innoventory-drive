<?php
require_once "../../config.php"; // Go up two levels to find config

$msg = "";
$error = "";

// Prevent admin registration via this page. Redirect admin requests to login.
if (isset($_GET['role']) && $_GET['role'] === 'admin') {
    header("Location: ../../index.php");
    exit;
}
$role = 'user';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]); // Get role from hidden field
    
    // Check if email exists
    $check = $db->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Both admin and user requests start as 'pending'
        $status = 'pending';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $status);

        if ($stmt->execute()) {
            if ($role === 'admin') {
                $msg = "Admin registration request submitted! Please wait for approval from an existing admin.";
            } else {
                $msg = "Request submitted successfully! Please wait for Admin approval.";
            }
        } else {
            $error = "Error submitting request.";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Access - Innoventory</title>
    <link rel="stylesheet" href="../../css/main.css">
</head>
<body>
    <?php include '../../common/header.php'; ?>

    <div class="login-container">
        <h1>Request Access</h1>
        <?php if($msg) echo "<div class='error-message' style='background:#ecfdf5;color:#065f46;border-color:#bbf7d0;'>$msg</div>"; ?>
        <?php if($error) echo "<div class='error-message' style='background:#fff5f5;color:#b91c1c;border-color:#fecaca;'>$error</div>"; ?>

        <div style="background: #dbeafe; color: #1e40af; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px;">
            Submit a request for access. An administrator will review and approve your request.
        </div>

        <form method="POST">
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Full Name" required autocomplete="name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Create Password</label>
                <input type="password" id="password" name="password" placeholder="Create Password" required autocomplete="new-password" minlength="6">
            </div>
            <div class="actions-vertical">
                <button type="submit" class="btn-primary">Submit Request</button>
                <a href="../../index.php" class="btn-ghost">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>