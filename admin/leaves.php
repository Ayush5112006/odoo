<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <h3>Leave Management</h3>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#approved">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rejected">Rejected</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="pending" class="tab-pane fade show active">
            <div class="card p-4">
                <h5>Pending Leave Applications</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Applied On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "
                            SELECT l.*, u.first_name FROM leaves l
                            JOIN users u ON l.user_id = u.id
                            WHERE l.status='pending' AND u.company_name='{$_SESSION['company_name']}'
                            ORDER BY l.applied_at DESC
                        ");
                        
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $days = $end->diff($start)->days + 1;
                                
                                echo "<tr>
                                    <td>{$row['first_name']}</td>
                                    <td><span class='badge bg-info'>" . ucfirst($row['type']) . "</span></td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$days}</td>
                                    <td><small>{$row['reason']}</small></td>
                                    <td>" . date('d M Y', strtotime($row['applied_at'])) . "</td>
                                    <td>
                                        <form method='POST' action='../leave/leave_action.php' style='display: inline;'>
                                            <input type='hidden' name='leave_id' value='{$row['id']}'>
                                            <input type='hidden' name='action' value='approve'>
                                            <button type='submit' class='btn btn-sm btn-success'>Approve</button>
                                        </form>
                                        <form method='POST' action='../leave/leave_action.php' style='display: inline;'>
                                            <input type='hidden' name='leave_id' value='{$row['id']}'>
                                            <input type='hidden' name='action' value='reject'>
                                            <button type='submit' class='btn btn-sm btn-danger'>Reject</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center text-muted'>No pending leave applications</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="approved" class="tab-pane fade">
            <div class="card p-4">
                <h5>Approved Leaves</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "
                            SELECT l.*, u.first_name FROM leaves l
                            JOIN users u ON l.user_id = u.id
                            WHERE l.status='approved' AND u.company_name='{$_SESSION['company_name']}'
                            ORDER BY l.applied_at DESC
                        ");
                        
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $days = $end->diff($start)->days + 1;
                                
                                echo "<tr>
                                    <td>{$row['first_name']}</td>
                                    <td><span class='badge bg-info'>" . ucfirst($row['type']) . "</span></td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$days}</td>
                                    <td>" . date('d M Y', strtotime($row['applied_at'])) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No approved leaves</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="rejected" class="tab-pane fade">
            <div class="card p-4">
                <h5>Rejected Leaves</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "
                            SELECT l.*, u.first_name FROM leaves l
                            JOIN users u ON l.user_id = u.id
                            WHERE l.status='rejected' AND u.company_name='{$_SESSION['company_name']}'
                            ORDER BY l.applied_at DESC
                        ");
                        
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $days = $end->diff($start)->days + 1;
                                
                                echo "<tr>
                                    <td>{$row['first_name']}</td>
                                    <td><span class='badge bg-info'>" . ucfirst($row['type']) . "</span></td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$days}</td>
                                    <td>" . date('d M Y', strtotime($row['applied_at'])) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No rejected leaves</td></tr>";
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
