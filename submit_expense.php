<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'] ?? '';
    $expense_date = $_POST['expense_date'] ?? '';
    $category = $_POST['category'] ?? '';
    $paid_by = $_POST['paid_by'] ?? '';
    $amount = $_POST['amount'] ?? '';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare("INSERT INTO expenses (employee_id, description, expense_date, category, paid_by, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $description, $expense_date, $category, $paid_by, $amount]);
        
        $_SESSION['success'] = "Expense submitted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error submitting expense: " . $e->getMessage();
    }
}

redirect('employee.php');
?>