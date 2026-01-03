<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");

// Verify HR can only see their company's data
if ($_SESSION['role'] !== 'hr') {
    $_SESSION['error'] = "Access denied!";
    header("Location: dashboard.php");
    exit;
}
?>

<div class="container-fluid mt-4">
    <h3>Payroll Management</h3>
    <p class="text-muted">View salary information for employees in your company</p>

    <div class="card p-4 mt-4">
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
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "
                    SELECT s.*, u.first_name, u.last_name FROM salary s
                    JOIN users u ON s.user_id = u.id
                    WHERE u.company_name='{$_SESSION['company_name']}' AND u.role='employee'
                    ORDER BY u.first_name ASC
                ");
                
                if(mysqli_num_rows($q) > 0) {
                    while($row = mysqli_fetch_assoc($q)) {
                        echo "<tr>
                            <td>{$row['first_name']} {$row['last_name']}</td>
                            <td>₹ " . number_format($row['basic'], 2) . "</td>
                            <td>₹ " . number_format($row['hra'], 2) . "</td>
                            <td>₹ " . number_format($row['allowance'], 2) . "</td>
                            <td><strong>₹ " . number_format($row['gross'], 2) . "</strong></td>
                            <td>₹ " . number_format($row['pf'], 2) . "</td>
                            <td>₹ " . number_format($row['tax'], 2) . "</td>
                            <td><strong>₹ " . number_format($row['net'], 2) . "</strong></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center text-muted'>No salary data available yet</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
