<?php
require_once 'config.php';
requireLogin();

// Handle expense submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $description = $_POST['description'] ?? '';
    $expense_date = $_POST['expense_date'] ?? '';
    $category = $_POST['category'] ?? '';
    $paid_by = $_POST['paid_by'] ?? '';
    $amount = $_POST['amount'] ?? '';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {


        // In employee.php - Fetching user's expenses
$stmt = $conn->prepare("SELECT * FROM expenses WHERE employee_id = ? ORDER BY expense_date DESC");
$stmt->execute([$_SESSION['user_id']]);
while ($expense = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Display expense data
}
        $stmt = $conn->prepare("INSERT INTO expenses (employee_id, description, expense_date, category, paid_by, amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $description, $expense_date, $category, $paid_by, $amount]);
        
        $_SESSION['success'] = "Expense submitted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error submitting expense: " . $e->getMessage();
    }
    
    redirect('employee.php');
}

if (!isEmployee()) {
    redirect('index.php');
}

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("SELECT * FROM expenses WHERE employee_id = ? ORDER BY expense_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching expenses: " . $e->getMessage());
    $expenses = [];
    $_SESSION['error'] = "Unable to load expense history. Please try again.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - ExpenseFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-2xl font-bold text-indigo-600">
                    ExpenseFlow
                </div>
                <div>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p class="text-gray-600">Here is your expense summary.</p>
        </div>

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

        <!-- Top Section: Details & Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Employee Details Card -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
                    <div>
                        <span class="font-medium text-gray-500">Full Name:</span>
                        <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Email Address:</span>
                        <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Company:</span>
                        <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['company']); ?></p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Role:</span>
                        <p class="font-semibold">Employee</p>
                    </div>
                </div>
                
                <!-- Expense Submission Form -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Submit New Expense</h3>
                    <form method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" id="description" name="description" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="expense_date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="expense_date" name="expense_date" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select id="category" name="category" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Category</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Meals">Meals</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Software">Software</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="paid_by" class="block text-sm font-medium text-gray-700">Paid By</label>
                                <select id="paid_by" name="paid_by" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Payment Method</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Company Card">Company Card</option>
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount ($)</label>
                                <input type="number" step="0.01" id="amount" name="amount" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-indigo-700 transition">
                            Submit Expense
                        </button>
                    </form>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-center items-center text-center">
                 <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                 <div class="w-full space-y-3">
                     <label for="file-upload" class="cursor-pointer w-full inline-block bg-indigo-100 text-indigo-700 font-semibold px-6 py-3 rounded-lg hover:bg-indigo-200 transition">
                        Attach Bill
                     </label>
                     <input id="file-upload" type="file" class="hidden">
                 </div>
            </div>

        </div>

        <!-- Expense History Table -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-bold mb-4">My Expense History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM expenses WHERE employee_id = ? ORDER BY expense_date DESC");
                        $stmt->execute([$_SESSION['user_id']]);
                        while ($expense = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $status_color = '';
                            if ($expense['status'] == 'approved') {
                                $status_color = 'bg-green-100 text-green-800';
                            } elseif ($expense['status'] == 'rejected') {
                                $status_color = 'bg-red-100 text-red-800';
                            } else {
                                $status_color = 'bg-yellow-100 text-yellow-800';
                            }
                            echo '<tr>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($expense['description']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($expense['expense_date']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($expense['category']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($expense['paid_by']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap font-medium">$' . number_format($expense['amount'], 2) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">';
                            echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $status_color . '">';
                            echo ucfirst($expense['status']);
                            echo '</span>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>