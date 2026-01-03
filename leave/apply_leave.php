<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $start = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end = mysqli_real_escape_string($conn, $_POST['end_date']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // Validate dates
    if (strtotime($end) < strtotime($start)) {
        $_SESSION['error'] = "End date must be after start date!";
        header("Location: ../employee/leaves.php");
        exit;
    }

    $insert = mysqli_query($conn, "
        INSERT INTO leaves (user_id, type, start_date, end_date, reason, status)
        VALUES ('$uid', '$type', '$start', '$end', '$reason', 'pending')
    ");

    if ($insert) {
        $_SESSION['success'] = "Leave application submitted successfully!";
    } else {
        $_SESSION['error'] = "Failed to apply leave: " . mysqli_error($conn);
    }

    header("Location: ../employee/leaves.php");
    exit;
}

header("Location: ../employee/leaves.php");
exit;
?>
