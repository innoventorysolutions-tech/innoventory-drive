<?php
require_once "../config.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm"]);
    $role     = trim($_POST["role"]); // user/admin

    // Duplicate email check
    $check = $db->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists!";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    }
    else {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $insert = $db->prepare("
            INSERT INTO users (name, email, password, role, status)
            VALUES (?,?,?,?, 'pending')
        ");
        $insert->bind_param("ssss", $name, $email, $hash, $role);

        if ($insert->execute()) {
            header("Location: signup_waiting.php");
            exit;
        } else {
            $error = "Registration failed!";
        }
        $insert->close();
    }

    $check->close();
}
mysqli_close($db);
?>

<form method="POST">
    <input type="text" name="name"     placeholder="Full Name" required><br>
    <input type="email" name="email"   placeholder="Email" required><br>

    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="confirm"  placeholder="Confirm Password" required><br>

    <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br>

    <button type="submit">Sign Up</button>
</form>

<?php
if($error) echo "<p style='color:red;'>$error</p>";
?>
