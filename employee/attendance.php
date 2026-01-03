<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container mt-4">
    <h3>My Attendance</h3>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Status Today</h6>
                            <?php
                            $today = date('Y-m-d');
                            $q = mysqli_query($conn,"SELECT * FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date='$today'");
                            $att = mysqli_fetch_assoc($q);
                            
                            if($att && $att['check_in']) {
                                echo "<p><span class='badge bg-success'>Checked In</span></p>";
                                if($att['check_out']) {
                                    echo "<p><span class='badge bg-info'>Checked Out</span></p>";
                                    echo "<p><small>Work Hours: " . ($att['work_hours'] ?? 'N/A') . "</small></p>";
                                } else {
                                    echo "<form method='POST' action='../attendance/checkout.php' class='mt-3'>";
                                    echo "<button type='submit' class='btn btn-danger btn-sm'>Checkout</button>";
                                    echo "</form>";
                                }
                            } else {
                                echo "<p><span class='badge bg-warning'>Not Checked</span></p>";
                                echo "<form method='POST' action='../attendance/checkin.php' class='mt-3'>";
                                echo "<button type='submit' class='btn btn-success btn-sm'>Checkin</button>";
                                echo "</form>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">This Month</h6>
                            <?php
                            $current_month = date('Y-m');
                            $q = mysqli_query($conn,"SELECT COUNT(*) as present FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date LIKE '$current_month%' AND status='present'");
                            $row = mysqli_fetch_assoc($q);
                            echo "<p style='font-size: 1.5rem; color: #667eea; margin: 0;'>" . $row['present'] . "</p>";
                            echo "<small class='text-muted'>Days Present</small>";
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Attendance Rate</h6>
                            <?php
                            $current_month = date('Y-m');
                            $q1 = mysqli_query($conn,"SELECT COUNT(*) as present FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date LIKE '$current_month%' AND status='present'");
                            $q2 = mysqli_query($conn,"SELECT COUNT(*) as total FROM attendance WHERE user_id='{$_SESSION['user_id']}' AND date LIKE '$current_month%'");
                            
                            $present = mysqli_fetch_assoc($q1)['present'];
                            $total = mysqli_fetch_assoc($q2)['total'];
                            
                            $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                            echo "<p style='font-size: 1.5rem; color: #764ba2; margin: 0;'>" . $rate . "%</p>";
                            echo "<small class='text-muted'>Percentage</small>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-4">
                <h5>Attendance History</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Work Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn,"SELECT * FROM attendance WHERE user_id='{$_SESSION['user_id']}' ORDER BY date DESC LIMIT 30");
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $status_color = $row['status'] == 'present' ? 'success' : 'warning';
                                echo "<tr>
                                    <td>{$row['date']}</td>
                                    <td>{$row['check_in']}</td>
                                    <td>{$row['check_out']}</td>
                                    <td>{$row['work_hours']}</td>
                                    <td><span class='badge bg-{$status_color}'>" . ucfirst($row['status']) . "</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted'>No attendance records</td></tr>";
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
