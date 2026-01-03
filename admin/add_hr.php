<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../config/email.php");
include("../config/id_generator.php");

if($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: dashboard.php");
    exit;
}

if(isset($_POST['save'])){
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $pass = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: add_hr.php");
        exit;
    }

    // Generate custom ID
    $custom_id = generateEmployeeID($conn, $_SESSION['company_name'], $fname, $lname);

    // Get company logo from current admin/user
    $company_logo = '';
    $logo_query = mysqli_query($conn, "SELECT company_logo FROM users WHERE company_name='{$_SESSION['company_name']}' AND company_logo IS NOT NULL AND company_logo != '' LIMIT 1");
    if ($logo_query && mysqli_num_rows($logo_query) > 0) {
        $logo_row = mysqli_fetch_assoc($logo_query);
        $company_logo = $logo_row['company_logo'];
    }

    $insert = mysqli_query($conn,"
        INSERT INTO users (custom_id, company_name, first_name, last_name, email, password, role, status, company_logo)
        VALUES ('$custom_id', '{$_SESSION['company_name']}', '$fname', '$lname', '$email', '$pass', 'hr', 'active', '$company_logo')
    ");

    if($insert) {
        // Send login credentials email
        $full_name = $fname . ' ' . $lname;
        sendLoginCredentials($email, $full_name, $password, $_SESSION['company_name'], 'hr');
        
        $_SESSION['success'] = "HR Officer created successfully! Login ID: $custom_id. Credentials sent to email.";
        header("Location: employees.php");
    } else {
        $_SESSION['error'] = "Error creating HR: " . mysqli_error($conn);
        header("Location: add_hr.php");
    }
    exit;
}

include("../includes/header.php");
?>

<div class="container mt-4">
    <h3>Add HR Officer</h3>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" name="save" class="btn btn-primary">Create HR Officer</button>
                    <a href="employees.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>

<?php include("../includes/footer.php"); ?>
