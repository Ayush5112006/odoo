<?php
require_once 'config.php';
requireLogin();

// Handle expense approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_id'])) {
    $expense_id = $_POST['expense_id'];
    $action = $_POST['action'];
    $comments = $_POST['comments'] ?? '';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {

     // In manager.php - Fetching pending expenses with user data
$stmt = $conn->prepare("
    SELECT e.*, u.full_name as employee_name 
    FROM expenses e 
    JOIN users u ON e.employee_id = u.id 
    WHERE e.status = 'pending' 
    ORDER BY e.expense_date DESC
");

// In manager.php - Fetching statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_expenses,
        SUM(amount) as total_amount,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
    FROM expenses
");
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt->execute();
$pendingExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $new_status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE expenses SET status = ?, manager_comments = ? WHERE id = ?");
        $stmt->execute([$new_status, $comments, $expense_id]);
        
        $_SESSION['success'] = "Expense {$new_status} successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating expense: " . $e->getMessage();
    }
    
    redirect('manager.php');
}

if (!isManager()) {
    redirect('index.php');
}

// Fetch pending approvals for the manager
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("
    SELECT e.*, u.full_name as employee_name 
    FROM expenses e 
    JOIN users u ON e.employee_id = u.id 
    WHERE e.status = 'pending' 
    ORDER BY e.expense_date DESC
");
$stmt->execute();
$pendingExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch stats
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_expenses,
        SUM(amount) as total_amount,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
    FROM expenses
");
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - ExpenseFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #059669;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .table-row:hover {
            background-color: #f9fafb;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .stat-pending::before {
            background-color: var(--warning);
        }
        
        .stat-approved::before {
            background-color: var(--success);
        }
        
        .stat-rejected::before {
            background-color: var(--danger);
        }
        
        .stat-total::before {
            background-color: var(--primary);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 bg-white shadow-md flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">ExpenseFlow</h1>
                </div>
            </div>
            
            <div class="flex-1 p-4">
                <nav class="space-y-2">
                    <a href="manager.php" class="flex items-center space-x-3 p-3 rounded-lg bg-indigo-50 text-indigo-700 font-medium">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="manager.php?view=approvals" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-check-circle w-5"></i>
                        <span>Approvals</span>
                        <span class="ml-auto bg-indigo-600 text-white text-xs px-2 py-1 rounded-full"><?php echo count($pendingExpenses); ?></span>
                    </a>
                    <a href="manager.php?view=team" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-users w-5"></i>
                        <span>Team</span>
                    </a>
                    <a href="manager.php?view=reports" class="flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Reports</span>
                    </a>
                </nav>
            </div>
            
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3 p-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                        <p class="text-sm text-gray-500">Manager</p>
                    </div>
                </div>
                <a href="logout.php" class="w-full mt-2 flex items-center space-x-2 p-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Manager Dashboard</h2>
                        <p class="text-gray-600">Review and approve expense reports</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" placeholder="Search expenses..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
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
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card card p-5 stat-total">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Expenses</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">$<?php echo number_format($stats['total_amount'] ?? 0, 2); ?></h3>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <i class="fas fa-receipt text-indigo-600"></i>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up"></i> 12.5% from last month
                        </p>
                    </div>
                    
                    <div class="stat-card card p-5 stat-pending">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Pending Approval</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['pending_count'] ?? 0; ?></h3>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                        </div>
                        <p class="text-yellow-600 text-sm mt-2">Needs your attention</p>
                    </div>
                    
                    <div class="stat-card card p-5 stat-approved">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Approved</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['approved_count'] ?? 0; ?></h3>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-2">This month</p>
                    </div>
                    
                    <div class="stat-card card p-5 stat-rejected">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Rejected</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['rejected_count'] ?? 0; ?></h3>
                            </div>
                            <div class="p-3 bg-red-100 rounded-lg">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                        </div>
                        <p class="text-red-600 text-sm mt-2">Requires follow-up</p>
                    </div>
                </div>
                
                <!-- Pending Approvals Section -->
                <div class="card p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Pending Approvals</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-sort mr-1"></i> Sort
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-gray-200">
                                    <th class="pb-3 text-gray-500 font-medium">Employee</th>
                                    <th class="pb-3 text-gray-500 font-medium">Description</th>
                                    <th class="pb-3 text-gray-500 font-medium">Category</th>
                                    <th class="pb-3 text-gray-500 font-medium">Amount</th>
                                    <th class="pb-3 text-gray-500 font-medium">Date</th>
                                    <th class="pb-3 text-gray-500 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pending-approvals-table">
                                <?php foreach ($pendingExpenses as $expense): ?>
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-indigo-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium"><?php echo htmlspecialchars($expense['employee_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div>
                                            <p class="font-medium"><?php echo htmlspecialchars($expense['description']); ?></p>
                                            <?php if ($expense['receipt_path']): ?>
                                                <p class="text-xs text-indigo-600 flex items-center mt-1"><i class="fas fa-receipt mr-1"></i> Receipt attached</p>
                                            <?php else: ?>
                                                <p class="text-xs text-gray-500 flex items-center mt-1"><i class="fas fa-times mr-1"></i> No receipt</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm"><?php echo htmlspecialchars($expense['category']); ?></span>
                                    </td>
                                    <td class="py-4 font-medium">$<?php echo number_format($expense['amount'], 2); ?></td>
                                    <td class="py-4 text-gray-600"><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="approve-btn p-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200" data-id="<?php echo $expense['id']; ?>" data-description="<?php echo htmlspecialchars($expense['description']); ?>" data-amount="<?php echo $expense['amount']; ?>" data-employee="<?php echo htmlspecialchars($expense['employee_name']); ?>">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="reject-btn p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200" data-id="<?php echo $expense['id']; ?>" data-description="<?php echo htmlspecialchars($expense['description']); ?>" data-amount="<?php echo $expense['amount']; ?>" data-employee="<?php echo htmlspecialchars($expense['employee_name']); ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($pendingExpenses)): ?>
                    <div class="text-center py-8 text-gray-500">
                        No pending approvals at the moment.
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Modal for Approval/Rejection -->
    <div class="modal-overlay" id="action-modal">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800" id="modal-title">Approve Expense</h3>
                <button class="text-gray-400 hover:text-gray-600" id="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-6" id="expense-details">
                <!-- Expense details will be populated here -->
            </div>
            
            <form method="POST" id="action-form">
                <input type="hidden" id="expense-id" name="expense_id">
                <input type="hidden" id="action-type" name="action">
                <div class="mb-4">
                    <label for="comments" class="block text-gray-700 mb-2">Comments (Optional)</label>
                    <textarea name="comments" id="comments" rows="3" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add any comments for the employee..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50" id="cancel-action">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" id="approve-btn">
                        Approve Expense
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
        const modalOverlay = document.getElementById('action-modal');
        const modalTitle = document.getElementById('modal-title');
        const expenseDetails = document.getElementById('expense-details');
        const closeModalBtn = document.getElementById('close-modal');
        const cancelActionBtn = document.getElementById('cancel-action');
        const approveBtn = document.getElementById('approve-btn');
        const actionForm = document.getElementById('action-form');
        const expenseIdInput = document.getElementById('expense-id');
        const actionTypeInput = document.getElementById('action-type');
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners for modal
            closeModalBtn.addEventListener('click', closeModal);
            cancelActionBtn.addEventListener('click', closeModal);
            
            // Add event listeners to action buttons
            document.querySelectorAll('.approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    openActionModal(
                        this.getAttribute('data-id'),
                        'approve',
                        this.getAttribute('data-description'),
                        this.getAttribute('data-amount'),
                        this.getAttribute('data-employee')
                    );
                });
            });
            
            document.querySelectorAll('.reject-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    openActionModal(
                        this.getAttribute('data-id'),
                        'reject',
                        this.getAttribute('data-description'),
                        this.getAttribute('data-amount'),
                        this.getAttribute('data-employee')
                    );
                });
            });
        });
        
        // Open modal for approval/rejection
        function openActionModal(expenseId, action, description, amount, employee) {
            expenseIdInput.value = expenseId;
            actionTypeInput.value = action;
            
            // Update modal title and button
            if (action === 'approve') {
                modalTitle.textContent = 'Approve Expense';
                approveBtn.textContent = 'Approve Expense';
                approveBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
            } else {
                modalTitle.textContent = 'Reject Expense';
                approveBtn.textContent = 'Reject Expense';
                approveBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
            }
            
            // Populate expense details
            expenseDetails.innerHTML = `
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Employee:</span>
                        <span class="font-medium">${employee}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Description:</span>
                        <span class="font-medium">${description}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium">$${parseFloat(amount).toFixed(2)}</span>
                    </div>
                </div>
            `;
            
            // Show modal
            modalOverlay.classList.add('active');
        }
        
        // Close modal
        function closeModal() {
            modalOverlay.classList.remove('active');
        }
    </script>
</body>
</html>