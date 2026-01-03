<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$checkout = date('H:i:s');

$get = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT check_in FROM attendance 
    WHERE user_id='$user_id' AND date='$today'
"));

if (!$get) {
    $_SESSION['error'] = "No check-in record found!";
    header("Location: ../employee/attendance.php");
    exit;
}

$checkin = strtotime($get['check_in']);
$checkout_time = strtotime($checkout);

$worked = $checkout_time - $checkin;
$hours = gmdate("H:i", $worked);

$status = ($worked < 4*3600) ? 'halfday' : 'present';

mysqli_query($conn, "
    UPDATE attendance SET
    check_out='$checkout',
    work_hours='$hours',
    status='$status'
    WHERE user_id='$user_id' AND date='$today'
");

$_SESSION['success'] = "Checked out successfully!";
header("Location: ../employee/attendance.php");
exit;
?>
