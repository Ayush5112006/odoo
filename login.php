<?php
require_once 'config.php';

// If user is already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    switch ($_SESSION['role']) {
        case 'admin':
            redirect('admin.php');
            break;
        case 'manager':
            redirect('manager.php');
            break;
        case 'employee':
            redirect('employee.php');
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['company'] = $user['company'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    redirect('admin.php');
                    break;
                case 'manager':
                    redirect('manager.php');
                    break;
                case 'employee':
                    redirect('employee.php');
                    break;
                default:
                    redirect('index.php');
            }
        } else {
            $_SESSION['error'] = "Invalid email, password, or role selection";
            redirect('login.php');
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        redirect('login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ExpenseFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Simple style for the active role button */
        .role-btn.active {
            background-color: #4f46e5; /* indigo-600 */
            color: white;
            border-color: #4f46e5; /* indigo-600 */
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h1 class="text-center text-3xl font-extrabold text-indigo-600">
                    ExpenseFlow
                </h1>
                <h2 class="mt-2 text-center text-2xl font-bold text-gray-900">
                    Sign in to your account
                </h2>
            </div>

            <!-- Display error messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form Card -->
            <div class="bg-white p-8 rounded-xl shadow-lg w-full">
                <form class="space-y-6" method="POST">
                    <!-- Hidden input to store selected role -->
                    <input type="hidden" id="selected-role" name="role" value="employee">

                    <!-- Role Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Login as:
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" class="role-btn active w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="employee">
                                Employee
                            </button>
                            <button type="button" class="role-btn w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="manager">
                                Manager
                            </button>
                            <button type="button" class="role-btn w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="admin">
                                Admin
                            </button>
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email-address" class="sr-only">Email address</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                    </div>

                    <!-- Forgot Password -->
                    <div class="flex items-center justify-end">
                        <div class="text-sm">
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sign Up Link -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Not a member?
                <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign up now
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleButtons = document.querySelectorAll('.role-btn');
            const selectedRoleInput = document.getElementById('selected-role');

            roleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons
                    roleButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to the clicked button
                    button.classList.add('active');
                    
                    // Update the hidden input value
                    selectedRoleInput.value = button.dataset.role;
                });
            });
        });
    </script>

</body>
</html>