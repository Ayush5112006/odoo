<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify OTP - HRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #875A7B 0%, #5E3B5A 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.otp-card {
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
        <div class="col-md-5 col-lg-4">
            <div class="card otp-card bg-white">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4 text-dark">Verify OTP</h3>
                    
                    <?php if (isset($_SESSION['register_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['register_success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['register_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['otp_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['otp_error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['otp_error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['otp_email'])): ?>
                        <p class="text-center text-muted mb-4">
                            OTP has been sent to: <strong><?= substr($_SESSION['otp_email'], 0, 3) . '***' . substr($_SESSION['otp_email'], -10); ?></strong>
                        </p>
                        
                        <form method="post" action="verify_otp_process.php">
                            <div class="mb-3">
                                <label class="form-label text-dark">Enter OTP</label>
                                <input name="otp" type="text" class="form-control form-control-lg text-center" 
                                       placeholder="000000" maxlength="6" inputmode="numeric" required 
                                       pattern="[0-9]{6}" title="OTP must be 6 digits">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Verify OTP</button>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive OTP?</p>
                            <a href="resend_otp.php" class="text-primary">Resend OTP</a>
                        </div>
                        
                        <hr>
                        <p class="text-center text-muted mb-0">
                            <a href="register.php" class="text-primary">Back to Registration</a>
                        </p>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No OTP session found. Please register first.
                        </div>
                        <p class="text-center">
                            <a href="register.php" class="btn btn-primary">Go to Registration</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
