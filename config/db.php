<?php
// Only start session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "hrms_db");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
