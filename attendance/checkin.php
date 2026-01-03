<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$time = date('H:i:s');

$check = mysqli_query($conn, "
    SELECT * FROM attendance 
    WHERE user_id='$user_id' AND date='$today'
");

if(mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "
        INSERT INTO attendance (user_id, date, check_in, status)
        VALUES ('$user_id', '$today', '$time', 'present')
    ");
    $_SESSION['success'] = "Checked in successfully!";
} else {
    $_SESSION['error'] = "Already checked in today!";
}

header("Location: ../employee/attendance.php");
exit;
?>
