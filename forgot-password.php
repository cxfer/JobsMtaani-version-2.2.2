<?php
session_start();
require_once 'includes/app_settings.php';
require_once 'classes/User.php';

$appName = AppSettings::get('app_name', 'JobsMtaani');
$errors = [];
$success = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    } else {
        $userObj = new User();
        
        if ($userObj->emailExists($email)) {
            // Generate reset token and send email
            $resetToken = bin2hex(random_bytes(32));
            // In a real app, save token and send email
            $success = "If an account with that email exists, you will receive password reset instructions.";
        } else {
            // Don't reveal if email exists or not for security
            $success = "If an account with that email exists, you will receive password reset instructions.";
        }
    }
}

include 'includes/header.php';
?>

<main class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-key fa-2x text-primary"></i>
                            </div>
                            <h2 class="display-6 fw-bold text-primary">Reset Password</h2>
                            <p class="text-muted">Enter your email to receive reset instructions</p>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-muted text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Login
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
