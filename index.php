<?php
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
} elseif ($_SESSION['role'] === 'hr') {
    header("Location: hr/dashboard.php");
} else {
    header("Location: employee/dashboard.php");
}
exit;
?>
