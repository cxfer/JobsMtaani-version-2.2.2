<?php
session_start();
require_once 'includes/app_settings.php';
require_once 'classes/User.php';

$appName = AppSettings::get('app_name', 'JobsMtaani');
$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $userObj = new User();
    
    if ($userObj->verifyEmail($token)) {
        $success = true;
        $message = "Email verified successfully! You can now log in to your account.";
    } else {
        $message = "Invalid or expired verification token. Please request a new verification email.";
    }
} else {
    $message = "No verification token provided.";
}

include 'includes/header.php';
?>

<main class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <?php if ($success): ?>
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <h2 class="display-6 fw-bold text-success">Email Verified!</h2>
                            <?php else: ?>
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                </div>
                                <h2 class="display-6 fw-bold text-danger">Verification Failed</h2>
                            <?php endif; ?>
                        </div>

                        <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> mb-4">
                            <?php echo htmlspecialchars($message); ?>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <?php if ($success): ?>
                                <a href="login.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login Now
                                </a>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Register Again
                                </a>
                                <a href="resend-verification.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-envelope me-2"></i>Resend Email
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <a href="index.php" class="text-muted text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
