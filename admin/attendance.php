<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");

$date = $_GET['date'] ?? date('Y-m-d');
?>

<div class="container-fluid mt-4">
    <h3>Attendance Management</h3>
    
    <div class="card p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Select Date</label>
                <input type="date" name="date" class="form-control" value="<?= $date; ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <h5>Attendance for <?= date('d M Y', strtotime($date)); ?></h5>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Employee</th>
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
                    LEFT JOIN users u ON a.user_id = u.id
                    WHERE a.date='$date' AND u.company_name='{$_SESSION['company_name']}'
                    ORDER BY u.first_name ASC
                ");
                
                if(mysqli_num_rows($q) > 0) {
                    while($row = mysqli_fetch_assoc($q)) {
                        $status_color = $row['status'] == 'present' ? 'success' : ($row['status'] == 'halfday' ? 'warning' : 'danger');
                        echo "<tr>
                            <td>{$row['first_name']}</td>
                            <td>{$row['check_in']}</td>
                            <td>{$row['check_out']}</td>
                            <td>{$row['work_hours']}</td>
                            <td><span class='badge bg-{$status_color}'>" . ucfirst($row['status']) . "</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>No attendance records for this date</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="card p-4 mt-4">
        <h5>Monthly Report</h5>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Half Day</th>
                    <th>Leave</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $month = date('Y-m', strtotime($date));
                
                $emp_q = mysqli_query($conn, "SELECT DISTINCT user_id, (SELECT first_name FROM users WHERE id = a.user_id) as first_name 
                                            FROM attendance a 
                                            JOIN users u ON a.user_id = u.id
                                            WHERE a.date LIKE '$month%' AND u.company_name='{$_SESSION['company_name']}'
                                            ORDER BY first_name");
                
                if(mysqli_num_rows($emp_q) > 0) {
                    while($emp = mysqli_fetch_assoc($emp_q)) {
                        $uid = $emp['user_id'];
                        
                        $present = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM attendance WHERE user_id='$uid' AND status='present' AND date LIKE '$month%'"))['cnt'];
                        $absent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM attendance WHERE user_id='$uid' AND status='absent' AND date LIKE '$month%'"))['cnt'];
                        $halfday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM attendance WHERE user_id='$uid' AND status='halfday' AND date LIKE '$month%'"))['cnt'];
                        $leave = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM leaves WHERE user_id='$uid' AND status='approved' AND start_date LIKE '$month%'"))['cnt'];
                        
                        $total = $present + $absent + $halfday + $leave;
                        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                        
                        echo "<tr>
                            <td>{$emp['first_name']}</td>
                            <td>{$present}</td>
                            <td>{$absent}</td>
                            <td>{$halfday}</td>
                            <td>{$leave}</td>
                            <td><span class='badge bg-info'>{$percentage}%</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted'>No attendance records for this month</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
