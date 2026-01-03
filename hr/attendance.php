<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <h3>Attendance Management</h3>
    
    <div class="card p-4 mt-4">
        <h5>Employee Attendance Records</h5>
        <table class="table table-striped mt-3">
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
                <?php
                $q = mysqli_query($conn, "
                    SELECT a.*, u.first_name FROM attendance a
                    JOIN users u ON a.user_id = u.id
                    WHERE u.company_name='{$_SESSION['company_name']}'
                    ORDER BY a.date DESC, a.id DESC
                    LIMIT 100
                ");
                
                if(mysqli_num_rows($q) > 0) {
                    while($row = mysqli_fetch_assoc($q)) {
                        $status_color = $row['status'] == 'present' ? 'success' : 'warning';
                        echo "<tr>
                            <td>{$row['first_name']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['check_in']}</td>
                            <td>{$row['check_out']}</td>
                            <td>{$row['work_hours']}</td>
                            <td><span class='badge bg-{$status_color}'>" . ucfirst($row['status']) . "</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted'>No attendance records</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
