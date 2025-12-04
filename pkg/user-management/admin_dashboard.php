<?php
require_once "../session.php";
require_once "../config.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($db, "SELECT * FROM users WHERE role='user'");
?>

<h1>Admin Dashboard</h1>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row["name"]; ?></td>
    <td><?= $row["email"]; ?></td>
    <td><?= $row["status"]; ?></td>

    <td>
        <a href="approve.php?id=<?= $row['id'] ?>">Approve</a> | 
        <a href="deny.php?id=<?= $row['id'] ?>">Deny</a>
    </td>

</tr>
<?php endwhile; ?>
</table>
