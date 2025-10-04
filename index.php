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
        default:
            // Continue to show landing page
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExpenseFlow - Smart Expense Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <nav class="flex justify-between items-center px-6 py-4 bg-white shadow-sm">
    <div class="text-2xl font-bold text-indigo-600">ExpenseFlow</div>
    <div class="space-x-4">
      <a href="login.php" class="text-gray-600 hover:text-indigo-600 font-medium">Sign In</a>
      <a href="register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">Sign Up</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="container mx-auto px-6 py-16 text-center">
    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
      Simplify <span class="text-indigo-600">Expense Management</span>
    </h1>
    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
      Automate reimbursements, manage multi-level approvals, and gain complete transparency with our powerful ExpenseFlow platform.
    </p>
    <div class="mt-8 space-x-4">
      <a href="login.php" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition font-semibold">Sign In</a>
      <a href="register.php" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg shadow hover:bg-gray-200 transition font-semibold">Sign Up</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="bg-white py-16">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-900">Why Choose ExpenseFlow?</h2>
      <p class="text-center text-gray-600 mt-2 mb-10">Empowering companies with smart expense automation</p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Feature 1 -->
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <div class="text-indigo-600 text-4xl mb-4">⚡</div>
          <h3 class="text-xl font-semibold mb-2">Fast & Automated</h3>
          <p class="text-gray-600">Say goodbye to spreadsheets. Submit expenses in seconds and let the system handle the approvals.</p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <div class="text-indigo-600 text-4xl mb-4">✅</div>
          <h3 class="text-xl font-semibold mb-2">Smart Approvals</h3>
          <p class="text-gray-600">Multi-level workflows, percentage rules, and role-based approvals tailored to your company needs.</p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <div class="text-indigo-600 text-4xl mb-4">🔍</div>
          <h3 class="text-xl font-semibold mb-2">OCR for Receipts</h3>
          <p class="text-gray-600">Just scan your receipts. Our AI-powered OCR fills in expense details automatically.</p>
        </div>

        <!-- Feature 4 -->
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <div class="text-indigo-600 text-4xl mb-4">🌍</div>
          <h3 class="text-xl font-semibold mb-2">Multi-Currency</h3>
          <p class="text-gray-600">Supports global teams with automatic currency conversion and local compliance.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-100 py-6 text-center text-gray-600">
    © 2025 ExpenseFlow. All rights reserved.
  </footer>

</body>
</html>