<?php
include("../config/db.php");
include("../auth/auth_check.php");

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Employee ID not provided!";
    header("Location: employees.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id='$id' AND company_name='{$_SESSION['company_name']}'"));

if (!$data) {
    $_SESSION['error'] = "Employee not found!";
    header("Location: employees.php");
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = mysqli_query($conn, "
    UPDATE users SET 
    first_name='$fname',
    last_name='$lname',
    role='$role',
    status='$status'
    WHERE id='$id' AND company_name='{$_SESSION['company_name']}'
    ");

    if ($update) {
        $_SESSION['success'] = "Employee updated successfully!";
        header("Location: employee_view.php?id=$id");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update employee: " . mysqli_error($conn);
    }
}

include("../includes/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Employee Profile</h4>
                    <a href="employees.php" class="btn btn-secondary btn-sm">← Back to Employees</a>
                </div>

                <?php if (!isset($_GET['action']) || $_GET['action'] !== 'edit'): ?>
                    <!-- View Mode -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>First Name</strong></label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($data['first_name']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Last Name</strong></label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($data['last_name']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Email</strong></label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($data['email']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Role</strong></label>
                                <p class="form-control-plaintext"><span class="badge bg-info"><?= ucfirst($data['role']) ?></span></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Status</strong></label>
                                <p class="form-control-plaintext"><span class="badge bg-<?= $data['status']=='active'?'success':'danger' ?>"><?= ucfirst($data['status']) ?></span></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Company</strong></label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($data['company_name']) ?></p>
                            </div>
                        </div>

                        <div class="col-12">
                            <a href="employee_view.php?id=<?= $id ?>&action=edit" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit Employee
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Edit Mode -->
                    <form method="post" id="edit">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>First Name</strong></label>
                                <input name="first_name" value="<?= htmlspecialchars($data['first_name']) ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><strong>Last Name</strong></label>
                                <input name="last_name" value="<?= htmlspecialchars($data['last_name']) ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><strong>Email</strong></label>
                                <input value="<?= htmlspecialchars($data['email']) ?>" class="form-control" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><strong>Role</strong></label>
                                <select name="role" class="form-control" required>
                                    <option value="employee" <?= $data['role']=='employee'?'selected':'' ?>>Employee</option>
                                    <option value="hr" <?= $data['role']=='hr'?'selected':'' ?>>HR Officer</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><strong>Status</strong></label>
                                <select name="status" class="form-control" required>
                                    <option value="active" <?= $data['status']=='active'?'selected':'' ?>>Active</option>
                                    <option value="inactive" <?= $data['status']=='inactive'?'selected':'' ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><strong>Company</strong></label>
                                <input value="<?= htmlspecialchars($data['company_name']) ?>" class="form-control" disabled>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>
                                <a href="employee_view.php?id=<?= $id ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4">
                <h5>Account Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td><?= date('d M Y', strtotime($data['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-<?= $data['status']=='active'?'success':'danger' ?>"><?= ucfirst($data['status']) ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td><span class="badge bg-info"><?= ucfirst($data['role']) ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
