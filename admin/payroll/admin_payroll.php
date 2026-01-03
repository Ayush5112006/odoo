<?php
include("../../config/db.php");
include("../../auth/auth_check.php");

if ($_SESSION['role'] !== 'admin') {
    echo "Access denied"; exit;
}
include("../../includes/header.php");

$data = mysqli_query($conn,"
SELECT u.first_name,u.last_name,s.*
FROM salary s
JOIN users u ON u.id=s.user_id
WHERE u.company_name='{$_SESSION['company_name']}'
ORDER BY u.first_name ASC
");
?>

<div class="container mt-4">
<div class="d-flex justify-content-between">
    <h4>Payroll</h4>
    <a href="add_salary.php" class="btn btn-dark btn-sm">+ Add Salary</a>
</div>

<table class="table table-bordered bg-white text-dark mt-3">
<thead>
<tr>
    <th>Employee</th>
    <th>Gross</th>
    <th>PF</th>
    <th>Tax</th>
    <th>Net Pay</th>
</tr>
</thead>
<tbody>

<?php while($r=mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $r['first_name'].' '.$r['last_name'] ?></td>
    <td><?= $r['gross'] ?></td>
    <td><?= $r['pf'] ?></td>
    <td><?= $r['tax'] ?></td>
    <td><b><?= $r['net'] ?></b></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<?php include("../../includes/footer.php"); ?>
