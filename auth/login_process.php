<?php
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass = $_POST['password'];

$q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if (!$q) {
    die("Query Error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($q);

if($user && password_verify($pass, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['company_name'] = $user['company_name'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['company_logo'] = $user['company_logo'];

    header("Location: ../index.php");
    exit;
} else {
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: login.php");
    exit;
}
