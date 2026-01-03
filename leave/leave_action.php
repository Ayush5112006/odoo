<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['admin', 'hr'])) {
    $_SESSION['error'] = "Access denied!";
    header("Location: ../admin/leaves.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/leaves.php");
    exit;
}

$leave_id = mysqli_real_escape_string($conn, $_POST['leave_id']);

// Check which button was clicked
$action = '';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
}

// If no explicit action, check button values
if (!$action) {
    if (isset($_POST['approve'])) {
        $action = 'approve';
    } elseif (isset($_POST['reject'])) {
        $action = 'reject';
    }
}

$status = ($action === 'approve') ? 'approved' : 'rejected';

$update = mysqli_query($conn, "
    UPDATE leaves SET status='$status'
    WHERE id='$leave_id'
");

if ($update) {
    $_SESSION['success'] = "Leave " . $status . " successfully!";
} else {
    $_SESSION['error'] = "Failed to update leave: " . mysqli_error($conn);
}

// Redirect to appropriate page based on role
if ($_SESSION['role'] === 'hr') {
    header("Location: ../hr/leaves.php");
} else {
    header("Location: ../admin/leaves.php");
}
exit;
?>
