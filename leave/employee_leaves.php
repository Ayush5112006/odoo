<?php
include("../config/db.php");
include("../includes/header.php");

$uid = $_SESSION['user_id'];

$data = mysqli_query($conn,"
SELECT * FROM leaves 
WHERE user_id=$uid 
ORDER BY applied_at DESC
");
?>

<div class="container mt-4">
<div class="d-flex justify-content-between">
    <h4>My Leaves</h4>
    <a href="apply_leave.php" class="btn btn-dark btn-sm">+ New</a>
</div>

<table class="table table-bordered bg-white text-dark mt-3">
<thead>
<tr>
    <th>Type</th>
    <th>Start</th>
    <th>End</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= ucfirst($row['type']) ?></td>
    <td><?= $row['start_date'] ?></td>
    <td><?= $row['end_date'] ?></td>
    <td>
        <span class="badge 
        <?= $row['status']=='approved'?'bg-success':($row['status']=='rejected'?'bg-danger':'bg-warning') ?>">
        <?= ucfirst($row['status']) ?>
        </span>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>
