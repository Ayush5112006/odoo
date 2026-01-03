<?php
include("../../config/db.php");

if ($_SESSION['role'] !== 'employee') {
    echo "Access denied"; exit;
}
include("../../includes/header.php");

$uid = $_SESSION['user_id'];
$s = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM salary WHERE user_id=$uid"));
?>

<div class="container mt-4">
<h4>My Salary</h4>

<?php if($s): ?>
<table class="table table-bordered bg-white text-dark">
<tr><th>Basic</th><td><?= $s['basic'] ?></td></tr>
<tr><th>HRA</th><td><?= $s['hra'] ?></td></tr>
<tr><th>Allowance</th><td><?= $s['allowance'] ?></td></tr>
<tr><th>PF</th><td><?= $s['pf'] ?></td></tr>
<tr><th>Tax</th><td><?= $s['tax'] ?></td></tr>
<tr><th>Gross</th><td><?= $s['gross'] ?></td></tr>
<tr><th><b>Net Pay</b></th><td><b><?= $s['net'] ?></b></td></tr>
</table>
<?php else: ?>
<p>No salary data available.</p>
<?php endif; ?>
</div>

<?php include("../../includes/footer.php"); ?>
