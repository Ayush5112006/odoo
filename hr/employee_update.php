<?php
include("../config/db.php");
include("../auth/auth_check.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request!";
    header("Location: employees.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);
$fname = mysqli_real_escape_string($conn, $_POST['first_name']);
$lname = mysqli_real_escape_string($conn, $_POST['last_name']);
$status = mysqli_real_escape_string($conn, $_POST['status']);

// Verify employee belongs to this company
$verify = mysqli_query($conn, "SELECT id FROM users WHERE id='$id' AND company_name='{$_SESSION['company_name']}'");
if (mysqli_num_rows($verify) === 0) {
    $_SESSION['error'] = "Employee not found or does not belong to your company!";
    header("Location: employees.php");
    exit;
}

$update = mysqli_query($conn, "
UPDATE users SET 
first_name='$fname',
last_name='$lname',
status='$status'
WHERE id='$id' AND company_name='{$_SESSION['company_name']}'
");

if ($update) {
    $_SESSION['success'] = "Employee updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update employee: " . mysqli_error($conn);
}

header("Location: employees.php");
?>
