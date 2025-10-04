<?php
require_once 'config.php';
requireLogin();
if (!isAdmin()) {
    redirect('employee.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['full_name'])) {
        // Create user logic
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'employee';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        try {

            // In admin.php - Fetching all users
$stmt = $conn->query("SELECT * FROM users ORDER BY role, full_name");
while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Display user data
}
            $temp_password = bin2hex(random_bytes(4));
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, company) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashed_password, $role, $_SESSION['company']]);
            
            $_SESSION['success'] = "User created successfully! Temporary password: " . $temp_password;
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error creating user: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['user_id'])) {
        // Save approval rule logic
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
}

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ExpenseFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .toggle-checkbox:checked {
            right: 0;
            border-color: #4f46e5;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #4f46e5;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-halved text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">ExpenseFlow</h1>
                </div>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin.php" class="flex items-center space-x-3 p-3 rounded-lg bg-indigo-50 text-indigo-700 font-medium">
                    <i class="fas fa-cogs w-5"></i>
                    <span>Approval Rules</span>
                </a>
                <a href="admin.php?view=users" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-users-cog w-5"></i>
                    <span>User Management</span>
                </a>
                <a href="admin.php?view=expenses" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-file-invoice-dollar w-5"></i>
                    <span>All Expenses</span>
                </a>
                <a href="admin.php?view=analytics" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span>Analytics</span>
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-200">
                 <div class="flex items-center space-x-3 p-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                        <p class="text-sm text-gray-500">Administrator</p>
                    </div>
                </div>
                <a href="logout.php" class="w-full mt-2 flex items-center space-x-2 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Admin View (Approval Rules)</h2>
            </header>
            
            <main class="p-6">
                <!-- Display success/error messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Left Column: User Management -->
                    <div class="xl:col-span-1 space-y-8">
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Create New User</h3>
                            <form method="POST" class="space-y-4">
                                <div>
                                    <label for="new-user-name" class="block text-sm font-medium text-gray-700">User Name</label>
                                    <input type="text" id="new-user-name" name="full_name" placeholder="e.g., Marc" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                                <div>
                                    <label for="new-user-email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" id="new-user-email" name="email" placeholder="marc@example.com" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                                <div>
                                    <label for="new-user-role" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select id="new-user-role" name="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="employee">Employee</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-indigo-700 transition">
                                    Create User & Send Invite
                                </button>
                            </form>
                        </div>

                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Existing Users</h3>
                            <div class="space-y-3">
                                <?php
                                $stmt = $conn->query("SELECT * FROM users ORDER BY role, full_name");
                                while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<div class="flex justify-between items-center p-2 rounded-lg hover:bg-gray-50">';
                                    echo '<div>';
                                    echo '<p class="font-medium">' . htmlspecialchars($user['full_name']) . '</p>';
                                    echo '<p class="text-sm text-gray-500">' . ucfirst($user['role']) . '</p>';
                                    echo '</div>';
                                    echo '<button class="text-gray-400 hover:text-indigo-600"><i class="fas fa-pen"></i></button>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Approval Rules -->
                    <div class="xl:col-span-2 card p-6">
                         <h3 class="text-lg font-bold text-gray-800 mb-1">Approval Rule Configuration</h3>
                         <p class="text-sm text-gray-500 mb-6">Set up sequential or parallel approval workflows for users.</p>
                        
                        <form method="POST">
                            <div class="space-y-6">
                                 <div>
                                    <label for="rule-user" class="block text-sm font-medium text-gray-700">Rule applies to user</label>
                                    <select id="rule-user" name="user_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Select User</option>
                                        <?php
                                        $stmt = $conn->query("SELECT * FROM users WHERE role = 'employee'");
                                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['full_name']) . ' (Employee)</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                 <div>
                                    <label for="rule-description" class="block text-sm font-medium text-gray-700">Description of rule</label>
                                    <input type="text" id="rule-description" name="description" value="Approval rule for miscellaneous expenses" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                                <div>
                                    <label for="rule-manager" class="block text-sm font-medium text-gray-700">Manager for this rule</label>
                                    <select id="rule-manager" name="manager_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Select Manager</option>
                                        <?php
                                        $stmt = $conn->query("SELECT * FROM users WHERE role = 'manager'");
                                        while ($manager = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $manager['id'] . '">' . htmlspecialchars($manager['full_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Approvers Section -->
                                <div class="pt-4 border-t">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4">Approvers Setup</h4>
                                    
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <label for="manager-is-approver" class="font-medium text-gray-700">Manager is first approver?</label>
                                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                            <input type="checkbox" name="manager_is_approver" id="manager-is-approver" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked/>
                                            <label for="manager-is-approver" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                        </div>
                                    </div>
                                    
                                    <div id="approvers-list" class="my-4 space-y-2">
                                        <!-- Dynamic approvers will be added here -->
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mt-4">
                                        <label for="enforce-sequence" class="font-medium text-gray-700">Enforce approver sequence?</label>
                                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                            <input type="checkbox" name="enforce_sequence" id="enforce-sequence" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
                                            <label for="enforce-sequence" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                         <label for="min-approval" class="block text-sm font-medium text-gray-700">Minimum Approval Percentage (%)</label>
                                         <input type="number" id="min-approval" name="min_approval_percentage" placeholder="e.g., 75" class="mt-1 block w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                     </div>
                                </div>
                            </div>

                             <!-- Action Buttons -->
                            <div class="mt-8 pt-5 border-t border-gray-200">
                                <div class="flex justify-end space-x-3">
                                    <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Save Approval Rule
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>