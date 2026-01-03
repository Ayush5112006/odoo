<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");
include("../config/email.php");

if (!isset($_SESSION['otp_email'])) {
    $_SESSION['otp_error'] = "No OTP session found. Please register again.";
    header("Location: register.php");
    exit;
}

$email = $_SESSION['otp_email'];

// Generate new OTP
$otp = rand(100000, 999999);
$otp_time = date("Y-m-d H:i:s");

// Update OTP in database
$update = mysqli_query($conn, "
UPDATE users 
SET otp='$otp', otp_time='$otp_time'
WHERE email='$email'
");

if ($update) {
    // Get company name for email
    $user_query = mysqli_query($conn, "SELECT company_name, first_name FROM users WHERE email='$email'");
    if ($user_query && mysqli_num_rows($user_query) > 0) {
        $user = mysqli_fetch_assoc($user_query);
        
        // Send new OTP email
        try {
            sendOTPEmail($email, $otp, $user['company_name']);
            $_SESSION['register_success'] = "New OTP has been sent to your email!";
        } catch (Exception $e) {
            $_SESSION['otp_error'] = "Failed to send OTP email. Please try again.";
        }
    }
}

header("Location: verify_otp.php");
exit;
