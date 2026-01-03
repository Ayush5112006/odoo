<?php
include("../../config/db.php");
include("../../auth/auth_check.php");

if ($_SESSION['role'] !== 'admin') {
    echo "Access denied"; exit;
}

if(isset($_POST['save'])){
    $uid = mysqli_real_escape_string($conn, $_POST['user_id']);
    $basic = mysqli_real_escape_string($conn, $_POST['basic']);
    $hra = mysqli_real_escape_string($conn, $_POST['hra']);
    $allowance = mysqli_real_escape_string($conn, $_POST['allowance']);
    $pf = mysqli_real_escape_string($conn, $_POST['pf']);
    $tax = mysqli_real_escape_string($conn, $_POST['tax']);

    $gross = $basic + $hra + $allowance;
    $net = $gross - $pf - $tax;

    $query = "INSERT INTO salary (user_id, basic, hra, allowance, pf, tax, gross, net) 
    VALUES ('$uid','$basic','$hra','$allowance','$pf','$tax','$gross','$net')
    ON DUPLICATE KEY UPDATE
    basic='$basic', hra='$hra', allowance='$allowance',
    pf='$pf', tax='$tax', gross='$gross', net='$net'";
    
    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Salary added/updated successfully!";
        header("Location: ../payroll.php");
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}
include("../../includes/header.php");
?>

<div class="container mt-4">
<h4>Add / Update Salary</h4>

<form method="post" class="row g-3">
    <div class="col-md-6">
        <label>Employee</label>
        <select name="user_id" class="form-control" required>
            <?php
            $u = mysqli_query($conn,"SELECT id,first_name FROM users WHERE role='employee' AND company_name='{$_SESSION['company_name']}'");
            while($r=mysqli_fetch_assoc($u)){
                echo "<option value='{$r['id']}'>{$r['first_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-4"><label>Basic</label><input name="basic" class="form-control" required></div>
    <div class="col-md-4"><label>HRA</label><input name="hra" class="form-control" required></div>
    <div class="col-md-4"><label>Allowance</label><input name="allowance" class="form-control" required></div>
    <div class="col-md-4"><label>PF</label><input name="pf" class="form-control" required></div>
    <div class="col-md-4"><label>Tax</label><input name="tax" class="form-control" required></div>

    <div class="col-12">
        <button class="btn btn-dark" name="save">Save Salary</button>
    </div>
</form>
</div>

<?php include("../../includes/footer.php"); ?>
