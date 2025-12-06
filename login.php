<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $msg = "Invalid Login!";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Login</h2>
<form method="post">
    Email: <input type="text" name="email"><br><br>
    Password: <input type="password" name="password"><br><br>
    <button type="submit">Login</button>
</form>

<?php if(isset($msg)) echo "<p style='color:red;'>$msg</p>"; ?>
</body>
</html>
