<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container mt-4">
    <h3>My Salary</h3>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card p-4">
                <h5>Salary Details</h5>
                
                <?php
                $q = mysqli_query($conn, "SELECT * FROM salary WHERE user_id='{$_SESSION['user_id']}'");
                $salary = mysqli_fetch_assoc($q);
                
                if ($salary) {
                    echo "
                    <table class='table mt-3'>
                        <tbody>
                            <tr>
                                <td><strong>Basic Salary</strong></td>
                                <td>Rs. " . number_format($salary['basic'], 2) . "</td>
                                <td><strong>Gross Salary</strong></td>
                                <td>Rs. " . number_format($salary['gross'], 2) . "</td>
                            </tr>
                            <tr>
                                <td><strong>HRA</strong></td>
                                <td>Rs. " . number_format($salary['hra'], 2) . "</td>
                                <td><strong>Net Salary</strong></td>
                                <td>Rs. " . number_format($salary['net'], 2) . "</td>
                            </tr>
                            <tr>
                                <td><strong>Allowance</strong></td>
                                <td>Rs. " . number_format($salary['allowance'], 2) . "</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style='border-top: 2px solid #ddd;'>
                                <td><strong>PF (Deduction)</strong></td>
                                <td>Rs. " . number_format($salary['pf'], 2) . "</td>
                                <td><strong>Tax (Deduction)</strong></td>
                                <td>Rs. " . number_format($salary['tax'], 2) . "</td>
                            </tr>
                        </tbody>
                    </table>
                    ";
                } else {
                    echo "<p class='text-muted'>No salary information found. Please contact HR.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card p-4">
                <h5>Salary History</h5>
                <p class="text-muted">Salary records for last 12 months will be displayed here.</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Basic</th>
                            <th>Allowance</th>
                            <th>Gross</th>
                            <th>Deductions</th>
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No salary history available yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
