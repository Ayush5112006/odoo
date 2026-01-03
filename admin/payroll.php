<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Payroll Management</h3>
        <a href="payroll/add_salary.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Salary</a>
    </div>

    <div class="card p-4">
        <h5>Employee Salaries</h5>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Basic</th>
                    <th>HRA</th>
                    <th>Allowance</th>
                    <th>Gross</th>
                    <th>PF</th>
                    <th>Tax</th>
                    <th>Net</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "
                    SELECT s.*, u.first_name FROM salary s
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE u.company_name='{$_SESSION['company_name']}' OR (u.company_name IS NULL AND s.user_id IN (SELECT id FROM users WHERE company_name='{$_SESSION['company_name']}'))
                    ORDER BY u.first_name ASC
                ");
                
                if(mysqli_num_rows($q) > 0) {
                    while($row = mysqli_fetch_assoc($q)) {
                        echo "<tr>
                            <td>{$row['first_name']}</td>
                            <td>₹ " . number_format($row['basic'], 2) . "</td>
                            <td>₹ " . number_format($row['hra'], 2) . "</td>
                            <td>₹ " . number_format($row['allowance'], 2) . "</td>
                            <td><strong>₹ " . number_format($row['gross'], 2) . "</strong></td>
                            <td>₹ " . number_format($row['pf'], 2) . "</td>
                            <td>₹ " . number_format($row['tax'], 2) . "</td>
                            <td><strong>₹ " . number_format($row['net'], 2) . "</strong></td>
                            <td>
                                <a href='#' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='#' class='btn btn-sm btn-danger'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center text-muted'>No salary records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
