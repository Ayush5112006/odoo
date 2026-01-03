<?php
include("../config/db.php");
include("../auth/auth_check.php");

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Employee ID not provided!";
    header("Location: employees.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id='$id' AND role='employee' AND company_name='{$_SESSION['company_name']}'")); 

if (!$data) {
    $_SESSION['error'] = "Employee not found!";
    header("Location: employees.php");
    exit;
}

// Get employee details
$emp_details = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employee_details WHERE user_id='$id'"));

// Get recent attendance
$attendance = mysqli_query($conn, "SELECT * FROM attendance WHERE user_id='$id' ORDER BY date DESC LIMIT 10");

// Get leaves
$leaves = mysqli_query($conn, "SELECT * FROM leaves WHERE user_id='$id' ORDER BY applied_at DESC LIMIT 5");

// Get salary info
$salary = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM salary WHERE user_id='$id'"));

include("../includes/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-3">
            <a href="employees.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Employees</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 text-center mb-4">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #875A7B 0%, #5E3B5A 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: bold;">
                    <?= strtoupper(substr($data['first_name'], 0, 1)); ?>
                </div>
                <h4><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?></h4>
                <p class="text-muted"><?= htmlspecialchars($data['email']); ?></p>
                <span class="badge bg-<?= $data['status']=='active'?'success':'danger' ?> me-2"><?= ucfirst($data['status']); ?></span>
                <span class="badge bg-info"><?= ucfirst($data['role']); ?></span>
            </div>

            <div class="card p-4 mb-4">
                <h5>Account Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Member Since:</strong></td>
                        <td><?= date('d M Y', strtotime($data['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?= $data['phone'] ?? 'Not provided'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-<?= $data['status']=='active'?'success':'danger' ?>"><?= ucfirst($data['status']); ?></span></td>
                    </tr>
                </table>
            </div>

            <?php if ($emp_details): ?>
            <div class="card p-4">
                <h5>Employee Details</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td><?= $emp_details['department'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Designation:</strong></td>
                        <td><?= $emp_details['designation'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Manager:</strong></td>
                        <td><?= $emp_details['manager'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Location:</strong></td>
                        <td><?= $emp_details['location'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Join Date:</strong></td>
                        <td><?= $emp_details['join_date'] ?? 'N/A'; ?></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <!-- Edit Section -->
            <div class="card p-4 mb-4" id="edit">
                <h5>Edit Employee Information</h5>
                
                <form method="post" action="employee_update.php">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input name="first_name" value="<?= htmlspecialchars($data['first_name']) ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input name="last_name" value="<?= htmlspecialchars($data['last_name']) ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input value="<?= htmlspecialchars($data['email']) ?>" class="form-control" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= $data['status']=='active'?'selected':'' ?>>Active</option>
                                <option value="inactive" <?= $data['status']=='inactive'?'selected':'' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input value="<?= htmlspecialchars($data['company_name']) ?>" class="form-control" disabled>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="employees.php" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Attendance Section -->
            <div class="card p-4 mb-4">
                <h5>Recent Attendance (Last 10 Days)</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
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
                            if(mysqli_num_rows($attendance) > 0) {
                                while($row = mysqli_fetch_assoc($attendance)) {
                                    $badge = $row['status'] == 'present' ? 'success' : 'warning';
                                    echo "<tr>
                                        <td>{$row['date']}</td>
                                        <td>{$row['check_in']}</td>
                                        <td>{$row['check_out']}</td>
                                        <td>{$row['work_hours']}</td>
                                        <td><span class='badge bg-{$badge}'>" . ucfirst($row['status']) . "</span></td>
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

            <!-- Leaves Section -->
            <div class="card p-4 mb-4">
                <h5>Recent Leave Applications (Last 5)</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
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
                            if(mysqli_num_rows($leaves) > 0) {
                                while($row = mysqli_fetch_assoc($leaves)) {
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

            <!-- Salary Section -->
            <?php if ($salary): ?>
            <div class="card p-4">
                <h5>Salary Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Basic:</strong></td>
                                <td>₹ <?= number_format($salary['basic'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>HRA:</strong></td>
                                <td>₹ <?= number_format($salary['hra'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Allowance:</strong></td>
                                <td>₹ <?= number_format($salary['allowance'], 2); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>PF:</strong></td>
                                <td>₹ <?= number_format($salary['pf'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tax:</strong></td>
                                <td>₹ <?= number_format($salary['tax'], 2); ?></td>
                            </tr>
                            <tr style="background: #f0f0f0;">
                                <td><strong>Gross Salary:</strong></td>
                                <td><strong>₹ <?= number_format($salary['gross'], 2); ?></strong></td>
                            </tr>
                            <tr style="background: #e8f5e9;">
                                <td><strong>Net Salary:</strong></td>
                                <td><strong>₹ <?= number_format($salary['net'], 2); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card p-4">
                <p class="text-muted">No salary information set for this employee.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
