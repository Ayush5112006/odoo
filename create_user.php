<?php
require_once 'config.php';
requireLogin();
if (!isAdmin()) {
    redirect('employee.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        // Generate a random password
        $temp_password = bin2hex(random_bytes(4)); // 8 character temporary password
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, company) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashed_password, $role, $_SESSION['company']]);
        
        $_SESSION['success'] = "User created successfully! Temporary password: " . $temp_password;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error creating user: " . $e->getMessage();
    }
}

redirect('admin.php');
?>