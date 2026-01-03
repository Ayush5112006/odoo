<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container mt-4">
    <h3>Welcome, <?= $_SESSION['first_name']; ?></h3>
    <p class="text-muted">Employee Dashboard</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-calendar-check" style="font-size: 2rem; color: #667eea;"></i>
                <h5 class="mt-3">Today Attendance</h5>
                <p class="mb-0">
                    <?php
                    $today = date('Y-m-d');
                    $q = mysqli_query($conn,"SELECT * FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date='$today'");
                    $att = mysqli_fetch_assoc($q);
                    
                    if($att) {
                        echo $att['check_in'] ? "Checked In" : "Not Checked";
                    } else {
                        echo "Not Checked";
                    }
                    ?>
                </p>
                <a href="attendance.php" class="btn btn-sm btn-primary mt-2">View Details</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-hourglass-split" style="font-size: 2rem; color: #764ba2;"></i>
                <h5 class="mt-3">Pending Leaves</h5>
                <p class="mb-0">
                    <?php
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM leaves WHERE user_id='{$_SESSION['user_id']}' AND status='pending'");
                    echo mysqli_fetch_assoc($q)['total'];
                    ?>
                </p>
                <a href="leaves.php" class="btn btn-sm btn-primary mt-2">Apply Leave</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-graph-up" style="font-size: 2rem; color: #667eea;"></i>
                <h5 class="mt-3">This Month</h5>
                <p class="mb-0">
                    <?php
                    $current_month = date('Y-m');
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date LIKE '$current_month%'");
                    echo mysqli_fetch_assoc($q)['total'] . ' days';
                    ?>
                </p>
                <a href="attendance.php" class="btn btn-sm btn-primary mt-2">View</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-wallet2" style="font-size: 2rem; color: #764ba2;"></i>
                <h5 class="mt-3">Salary</h5>
                <p class="mb-0">View & Download</p>
                <a href="salary.php" class="btn btn-sm btn-primary mt-2">View Salary</a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card p-4">
                <h5>Recent Leaves</h5>
                <table class="table table-sm mt-3">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn,"SELECT * FROM leaves WHERE user_id='{$_SESSION['user_id']}' ORDER BY applied_at DESC LIMIT 5");
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $status_color = $row['status'] == 'approved' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger');
                                echo "<tr>
                                    <td><span class='badge bg-info'>{$row['type']}</span></td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td><span class='badge bg-{$status_color}'>" . ucfirst($row['status']) . "</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted'>No leaves applied yet</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
