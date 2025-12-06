<?php
include "config.php";
$result = mysqli_query($conn, "SELECT * FROM requests");
?>
<!DOCTYPE html>
<html>
<body>
<h2>All Requests</h2>

<table border="1" cellpadding="10">
<tr>
  <th>ID</th>
  <th>Name</th>
  <th>Status</th>
  <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['name'] ?></td>
  <td><?= $row['status'] ?></td>
  <td>
      <a href="approve.php?id=<?= $row['id'] ?>">Approve</a> |
      <a href="deny.php?id=<?= $row['id'] ?>">Deny</a> |
      <a href="delete_request.php?id=<?= $row['id'] ?>">Delete</a>
  </td>
</tr>
<?php } ?>

</table>

</body>
</html>
