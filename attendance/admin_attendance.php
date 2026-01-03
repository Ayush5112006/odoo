<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");

$data = mysqli_query($conn,"
SELECT u.first_name,u.last_name,a.*
FROM attendance a
JOIN users u ON u.id=a.user_id
WHERE u.company_name='{$_SESSION['company_name']}'
ORDER BY a.date DESC
");
?>

<div class="container mt-4">
<h4>Attendance Records</h4>

<table class="table table-bordered bg-white text-dark">
<thead>
<tr>
    <th>Employee</th>
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
    <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
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
