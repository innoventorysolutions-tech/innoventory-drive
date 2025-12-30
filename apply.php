<?php include "db.php"; ?>
<?php
$success = false;
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    $sql = "INSERT INTO users (name, email, reason, location, status) VALUES ('$name', '$email', '$reason', '$location', 'pending')";
    if(mysqli_query($conn, $sql)){
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Access - Innoventory</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
    h2 { text-align: center; color: #333; margin-bottom: 8px; margin-top: 0; }
    p.subtitle { text-align: center; color: #666; margin-bottom: 24px; font-size: 14px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; margin-bottom: 8px; color: #444; font-weight: 500; font-size: 14px; }
    input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; font-family: inherit; }
    textarea { resize: vertical; min-height: 80px; }
    button { width: 100%; background: #007bff; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    button:hover { background: #0056b3; }
    .success-msg { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb; }
    .links { text-align: center; margin-top: 16px; font-size: 14px; }
    .links a { color: #666; text-decoration: none; }
    .links a:hover { text-decoration: underline; }
</style>
</head>
<body>
    <div class="card">
        <h2>Request Access</h2>
        <p class="subtitle">Join the Innoventory platform</p>
        
        <?php if($success): ?>
            <div class="success-msg">
                <strong>Application Sent!</strong><br>
                Your request is pending approval.
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="e.g. John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="e.g. john@example.com" required>
            </div>
            <div class="form-group">
                <label>Reason for Access</label>
                <textarea name="reason" placeholder="Why do you need access?" required></textarea>
            </div>
            <div class="form-group">
                <label>Location / Department</label>
                <input type="text" name="location" placeholder="e.g. New York / IT Support" required>
            </div>
            <button type="submit" name="submit">Submit Application</button>
        </form>
        <div class="links">
            <a href="login.php">Are you an Admin? Login</a>
        </div>
    </div>
</body>
</html>
