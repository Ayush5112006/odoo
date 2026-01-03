<?php
include("../config/db.php");
include("../includes/header.php");

$user_id = $_SESSION['user_id'];

$data = mysqli_query($conn,"
SELECT * FROM attendance 
WHERE user_id=$user_id 
ORDER BY date DESC
");
?>

<div class="container mt-4">
<h4>My Attendance</h4>

<div class="mb-3">
    <a href="checkin.php" class="btn btn-success btn-sm">Check In</a>
    <a href="checkout.php" class="btn btn-danger btn-sm">Check Out</a>
</div>

<table class="table table-bordered bg-white text-dark">
<thead>
<tr>
    <th>Date</th>
    <th>Check In</th>
    <th>Check Out</th>
    <th>Work Hours</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $row['date'] ?></td>
    <td><?= $row['check_in'] ?></td>
    <td><?= $row['check_out'] ?></td>
    <td><?= $row['work_hours'] ?></td>
    <td>
        <span class="badge 
        <?= $row['status']=='present'?'bg-success':'bg-danger' ?>">
        <?= ucfirst($row['status']) ?>
        </span>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>
