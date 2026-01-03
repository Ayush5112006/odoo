<!DOCTYPE html>
<html>
<head>
<title>Login - HRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #875A7B 0%, #5E3B5A 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.login-card {
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    background-color: #FFFFFF;
}
.btn-primary {
    background-color: #875A7B !important;
    border-color: #875A7B !important;
}
.btn-primary:hover {
    background-color: #5E3B5A !important;
    border-color: #5E3B5A !important;
}
.text-dark {
    color: #343A40 !important;
}
.text-primary {
    color: #875A7B !important;
}
</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card login-card bg-white">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4 text-dark">HRMS Login</h3>
                    
                    <?php 
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    if (isset($_SESSION['otp_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['otp_success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['otp_success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['login_error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>

                    <form method="post" action="login_process.php">
                        <div class="mb-3">
                            <label class="form-label text-dark">Email</label>
                            <input name="email" type="email" class="form-control" placeholder="admin@demo.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark">Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                    </form>

                    <hr>
                    <p class="text-center text-dark mb-0">
                        Don't have an account? <a href="register.php" class="text-primary">Register here</a>
                    </p>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
