<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../config/email.php");
include("../config/id_generator.php");
include("../includes/header.php");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $pass = password_hash($password, PASSWORD_BCRYPT);
    $dept = mysqli_real_escape_string($conn, $_POST['department'] ?? '');
    $desig = mysqli_real_escape_string($conn, $_POST['designation'] ?? '');

    // Check if email exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Email already exists!";
    } else {
        // Generate custom ID
        $custom_id = generateEmployeeID($conn, $_SESSION['company_name'], $fname, $lname);
        
        // Get company logo from current admin/user
        $company_logo = '';
        $logo_query = mysqli_query($conn, "SELECT company_logo FROM users WHERE company_name='{$_SESSION['company_name']}' AND company_logo IS NOT NULL AND company_logo != '' LIMIT 1");
        if ($logo_query && mysqli_num_rows($logo_query) > 0) {
            $logo_row = mysqli_fetch_assoc($logo_query);
            $company_logo = $logo_row['company_logo'];
        }
        
        $insert = mysqli_query($conn, "
            INSERT INTO users (custom_id, company_name, first_name, last_name, email, password, role, status, email_verified, company_logo)
            VALUES ('$custom_id', '{$_SESSION['company_name']}', '$fname', '$lname', '$email', '$pass', 'employee', 'active', 1, '$company_logo')
        ");

        if ($insert) {
            $user_id = mysqli_insert_id($conn);
            
            // Add employee details
            if ($dept || $desig) {
                mysqli_query($conn, "
                    INSERT INTO employee_details (user_id, department, designation)
                    VALUES ('$user_id', '$dept', '$desig')
                ");
            }
            
            // Send login credentials email
            $full_name = $fname . ' ' . $lname;
            sendLoginCredentials($email, $full_name, $password, $_SESSION['company_name'], 'employee');
            
            $_SESSION['success'] = "Employee added successfully! Login ID: $custom_id. Credentials sent to email.";
            header("Location: employees.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to add employee: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card p-4">
                <h4>Add New Employee</h4>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" placeholder="e.g., IT, HR, Sales">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g., Manager, Developer">
                        </div>

                        <div class="col-12 mb-3">
                            <button type="submit" class="btn btn-primary">Add Employee</button>
                            <a href="employees.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>
