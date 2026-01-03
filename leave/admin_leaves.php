<?php
include("../config/db.php");
include("../includes/header.php");

if (!in_array($_SESSION['role'], ['admin','hr'])) {
    echo "Access denied";
    exit;
}

$data = mysqli_query($conn,"
SELECT l.*, u.first_name, u.last_name 
FROM leaves l
JOIN users u ON u.id=l.user_id
ORDER BY l.applied_at DESC
");
?>

<div class="container mt-4">
<h4>Leave Requests</h4>

<table class="table table-bordered bg-white text-dark">
<thead>
<tr>
    <th>Employee</th>
    <th>Type</th>
    <th>Period</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php while($row=mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
    <td><?= ucfirst($row['type']) ?></td>
    <td><?= $row['start_date'].' to '.$row['end_date'] ?></td>
    <td><?= ucfirst($row['status']) ?></td>
    <td>
        <?php if($row['status']=='pending'): ?>
            <a href="leave_action.php?id=<?= $row['id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
            <a href="leave_action.php?id=<?= $row['id'] ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>
