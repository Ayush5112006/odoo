<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

if (!isset($_SESSION['otp_email'])) {
    $_SESSION['otp_error'] = "No OTP verification session found";
    header("Location: verify_otp.php");
    exit;
}

$email = $_SESSION['otp_email'];
$otp = mysqli_real_escape_string($conn, $_POST['otp']);

$q = mysqli_query($conn, "
SELECT * FROM users 
WHERE email='$email' AND otp='$otp'
");

if (mysqli_num_rows($q) == 1) {
    $user = mysqli_fetch_assoc($q);
    
    // Update user: verify email and activate account
    mysqli_query($conn, "
    UPDATE users 
    SET email_verified=1, otp=NULL, status='active'
    WHERE email='$email'
    ");

    // Send welcome email
    include("../config/email.php");
    try {
        @sendCompanyWelcomeEmail($email, $user['first_name'], $user['company_name']);
    } catch (Exception $e) {
        // Email errors won't block verification
    }

    unset($_SESSION['otp_email']);
    unset($_SESSION['company_name_temp']);
    
    $_SESSION['otp_success'] = "Email verified successfully! You can now login.";
    header("Location: login.php");
    exit;
} else {
    $_SESSION['otp_error'] = "Invalid OTP. Please try again.";
    header("Location: verify_otp.php");
    exit;
}
