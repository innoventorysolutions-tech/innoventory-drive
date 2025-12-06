<?php
$conn = new mysqli("localhost", "root", "", "inmvonetry_db");
if ($conn->connect_error) { die("Connection failed: ".$conn->connect_error); }

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM requests WHERE id='$id'");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_request'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $conn->query("UPDATE requests SET name='$name', email='$email', role='$role' WHERE id='$id'");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Request</title>
<style>
body { background:#121212; color:#fff; font-family:'Segoe UI', sans-serif; padding:50px; }
form { background:#1e1e2e; padding:20px; border-radius:8px; width:400px; margin:auto; }
input { width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:none; }
button { padding:10px 20px; border:none; border-radius:5px; background:#28a745; color:#fff; cursor:pointer; }
button:hover { background:#1e7e34; }
</style>
</head>
<body>
<form method="POST" action="">
<h2>Edit Request</h2>
<input type="text" name="name" value="<?php echo $row['name']; ?>" required>
<input type="email" name="email" value="<?php echo $row['email']; ?>" required>
<input type="text" name="role" value="<?php echo $row['role']; ?>" required>
<button type="submit" name="edit_request">Update</button>
</form>
</body>
</html>
