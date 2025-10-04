<?php
require_once 'config.php';
requireLogin();
if (!isAdmin()) {
    redirect('employee.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $description = $_POST['description'] ?? '';
    $manager_id = $_POST['manager_id'] ?? '';
    $manager_is_approver = isset($_POST['manager_is_approver']) ? 1 : 0;
    $enforce_sequence = isset($_POST['enforce_sequence']) ? 1 : 0;
    $min_approval_percentage = $_POST['min_approval_percentage'] ?? 100;
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare("INSERT INTO approval_rules (user_id, description, manager_id, manager_is_approver, enforce_sequence, min_approval_percentage) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $description, $manager_id, $manager_is_approver, $enforce_sequence, $min_approval_percentage]);
        
        $_SESSION['success'] = "Approval rule saved successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error saving approval rule: " . $e->getMessage();
    }
}

redirect('admin.php');
?>