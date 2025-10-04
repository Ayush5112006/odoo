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
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $company = $_POST['company_name'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    // Validate password
    if (strlen($password) < 7) {
        $_SESSION['error'] = "Password must be at least 7 characters long";
        redirect('register.php');
    }
    
    if ($password !== ($_POST['confirm_password'] ?? '')) {
        $_SESSION['error'] = "Passwords do not match";
        redirect('register.php');
    }
    
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Email already registered";
            redirect('register.php');
        }
        
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, company, country) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashed_password, $role, $company, $country]);
        
        // Auto-login after registration
        $user_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['role'] = $role;
        $_SESSION['company'] = $company;
        
        $_SESSION['success'] = "Account created successfully!";
        
        // Redirect based on role
        switch ($role) {
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
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        redirect('register.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ExpenseFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .role-btn.active {
            background-color: #4f46e5; /* indigo-600 */
            color: white;
            border-color: #4f46e5; /* indigo-600 */
        }
        /* Custom styles for validation feedback */
        .error-message {
            color: #ef4444; /* red-500 */
            font-size: 0.875rem; /* text-sm */
            margin-top: 0.25rem;
        }
        input.invalid {
            border-color: #ef4444; /* red-500 */
        }
        input.invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px #fecaca; /* red-200 */
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
                    Create your account
                </h2>
            </div>

            <!-- Display error messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Sign Up Form Card -->
            <div class="bg-white p-8 rounded-xl shadow-lg w-full">
                <form id="signup-form" class="space-y-6" method="POST" novalidate>
                    <input type="hidden" id="selected-role" name="role" value="employee">

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="sr-only">Full name</label>
                        <input id="full_name" name="full_name" type="text" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Full name">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Email address">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Password (min. 7 characters)">
                        <div id="password-error" class="error-message hidden"></div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="sr-only">Confirm password</label>
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Confirm password">
                         <div id="confirm-password-error" class="error-message hidden"></div>
                    </div>

                     <!-- Company Name -->
                    <div>
                        <label for="company_name" class="sr-only">Company name</label>
                        <input id="company_name" name="company_name" type="text" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Company name">
                    </div>
                    
                    <!-- Role Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sign up as:</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" class="role-btn active w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="employee">Employee</button>
                            <button type="button" class="role-btn w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="manager">Manager</button>
                            <button type="button" class="role-btn w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" data-role="admin">Admin</button>
                        </div>
                    </div>
                    
                    <!-- Country Dropdown -->
                    <div>
                        <label for="country" class="sr-only">Country</label>
                        <select id="country" name="country" class="w-full px-3 py-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500">
                           <option value="" disabled selected>Select your country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="IN">India</option>
                            <!-- Add more countries as needed -->
                        </select>
                    </div>

                    <!-- Sign Up Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sign In Link -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Already have an account?
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Role Button Logic ---
            const roleButtons = document.querySelectorAll('.role-btn');
            const selectedRoleInput = document.getElementById('selected-role');

            roleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    roleButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    selectedRoleInput.value = button.dataset.role;
                });
            });

            // --- Form Validation Logic ---
            const form = document.getElementById('signup-form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const passwordError = document.getElementById('password-error');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            
            form.addEventListener('submit', (e) => {
                let isValid = true;
                
                // Reset previous errors
                passwordError.textContent = '';
                passwordError.classList.add('hidden');
                password.classList.remove('invalid');
                confirmPasswordError.textContent = '';
                confirmPasswordError.classList.add('hidden');
                confirmPassword.classList.remove('invalid');

                // 1. Password length validation
                if (password.value.length < 7) {
                    passwordError.textContent = 'Password must be at least 7 characters long.';
                    passwordError.classList.remove('hidden');
                    password.classList.add('invalid');
                    isValid = false;
                }

                // 2. Password match validation
                if (password.value !== confirmPassword.value) {
                    confirmPasswordError.textContent = 'Passwords do not match.';
                    confirmPasswordError.classList.remove('hidden');
                    confirmPassword.classList.add('invalid');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>

</body>
</html>