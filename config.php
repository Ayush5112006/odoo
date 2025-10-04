<?php
// config.php
session_start();

// Database configuration for expense_management
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'expense_management');

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isManager() {
    return getUserRole() === 'manager';
}

function isEmployee() {
    return getUserRole() === 'employee';
}

// Check if user has permission to access page
function checkPermission($required_role) {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    
    $user_role = getUserRole();
    
    if ($required_role === 'admin' && !isAdmin()) {
        redirect('employee.php');
    }
    
    if ($required_role === 'manager' && !isManager() && !isAdmin()) {
        redirect('employee.php');
    }
    
    if ($required_role === 'employee' && !isEmployee() && !isManager() && !isAdmin()) {
        redirect('login.php');
    }
}

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>