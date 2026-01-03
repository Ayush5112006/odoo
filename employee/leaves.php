<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4">
                <h5>Apply For Leave</h5>
                <form method="POST" action="../leave/apply_leave.php">
                    <div class="mb-3">
                        <label class="form-label">Leave Type</label>
                        <select name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="paid">Paid Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="unpaid">Unpaid Leave</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Leave</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5>My Leave Applications</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "SELECT * FROM leaves WHERE user_id='{$_SESSION['user_id']}' ORDER BY applied_at DESC");
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $days = $end->diff($start)->days + 1;
                                
                                $status_color = $row['status'] == 'approved' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger');
                                
                                echo "<tr>
                                    <td><span class='badge bg-info'>" . ucfirst($row['type']) . "</span></td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$days}</td>
                                    <td><span class='badge bg-{$status_color}'>" . ucfirst($row['status']) . "</span></td>
                                    <td>" . date('d M Y', strtotime($row['applied_at'])) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No leave applications</td></tr>";
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
