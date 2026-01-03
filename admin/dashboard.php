<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <h3>Admin Dashboard</h3>
    <p class="text-muted">Welcome <?= $_SESSION['first_name']; ?>, here's your company overview</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-people" style="font-size: 2rem; color: #667eea;"></i>
                <h5 class="mt-3">Total Employees</h5>
                <p style="font-size: 1.5rem; color: #667eea; margin: 0;">
                    <?php
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM users WHERE role='employee' AND company_name='{$_SESSION['company_name']}'");
                    echo mysqli_fetch_assoc($q)['total'];
                    ?>
                </p>
                <a href="employees.php" class="btn btn-sm btn-primary mt-3">View All</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-person-badge" style="font-size: 2rem; color: #764ba2;"></i>
                <h5 class="mt-3">HR Officers</h5>
                <p style="font-size: 1.5rem; color: #764ba2; margin: 0;">
                    <?php
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM users WHERE role='hr' AND company_name='{$_SESSION['company_name']}'");
                    echo mysqli_fetch_assoc($q)['total'];
                    ?>
                </p>
                <a href="employees.php?filter=hr" class="btn btn-sm btn-primary mt-3">View All</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-hourglass-split" style="font-size: 2rem; color: #667eea;"></i>
                <h5 class="mt-3">Pending Leaves</h5>
                <p style="font-size: 1.5rem; color: #667eea; margin: 0;">
                    <?php
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM leaves WHERE status='pending' AND user_id IN (SELECT id FROM users WHERE company_name='{$_SESSION['company_name']}')");
                    echo mysqli_fetch_assoc($q)['total'];
                    ?>
                </p>
                <a href="leaves.php" class="btn btn-sm btn-primary mt-3">Review</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <i class="bi bi-calendar-check" style="font-size: 2rem; color: #764ba2;"></i>
                <h5 class="mt-3">Today Present</h5>
                <p style="font-size: 1.5rem; color: #764ba2; margin: 0;">
                    <?php
                    $today = date('Y-m-d');
                    $q = mysqli_query($conn,"SELECT COUNT(*) as total FROM attendance WHERE date='$today' AND status='present' AND user_id IN (SELECT id FROM users WHERE company_name='{$_SESSION['company_name']}')");
                    echo mysqli_fetch_assoc($q)['total'];
                    ?>
                </p>
                <a href="attendance.php" class="btn btn-sm btn-primary mt-3">View</a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card p-4">
                <h5>Recent Employees</h5>
                <table class="table table-striped table-sm mt-3">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn,"SELECT * FROM users WHERE role IN ('employee','hr') AND company_name='{$_SESSION['company_name']}' ORDER BY created_at DESC LIMIT 5");
                        while($row = mysqli_fetch_assoc($q)) {
                            echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td>{$row['email']}</td>
                                <td><span class='badge bg-info'>" . ucfirst($row['role']) . "</span></td>
                                <td><span class='badge bg-" . ($row['status'] == 'active' ? 'success' : 'danger') . "'>" . ucfirst($row['status']) . "</span></td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4">
                <h5>Pending Leave Applications</h5>
                <table class="table table-striped table-sm mt-3">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn,"
                            SELECT l.*, u.first_name FROM leaves l
                            JOIN users u ON l.user_id = u.id
                            WHERE l.status='pending' AND u.company_name='{$_SESSION['company_name']}'
                            ORDER BY l.applied_at DESC LIMIT 5
                        ");
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $days = $end->diff($start)->days + 1;
                                
                                echo "<tr>
                                    <td>{$row['first_name']}</td>
                                    <td><span class='badge bg-info'>" . ucfirst($row['type']) . "</span></td>
                                    <td>{$days} days</td>
                                    <td><a href='leaves.php' class='btn btn-sm btn-primary'>Review</a></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted'>No pending leaves</td></tr>";
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

<?php include("../includes/footer.php"); ?>
