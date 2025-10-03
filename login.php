<?php
session_start();
require_once 'includes/app_settings.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';
require_once 'classes/OAuth.php';
require_once 'config/database.php';

$appName = AppSettings::get('app_name', 'JobsMtaani');
$errors = [];
$success = '';

// Handle social login callbacks
if (isset($_GET['provider']) && isset($_GET['code'])) {
    try {
        $oauth = new OAuth();
        $provider = $_GET['provider'];
        
        switch ($provider) {
            case 'google':
                $user = $oauth->processGoogleCallback($_GET['code']);
                break;
            case 'facebook':
                $user = $oauth->processFacebookCallback($_GET['code']);
                break;
            case 'twitter':
                $user = $oauth->processTwitterCallback($_GET['code']);
                break;
            case 'yahoo':
                $user = $oauth->processYahooCallback($_GET['code']);
                break;
            default:
                throw new Exception('Unsupported provider');
        }
        
        // Check if user needs onboarding (new social user)
        $database = new Database();
        $db = $database->getConnection();
        $userObj = new User($db);
        $userData = $userObj->getUserById($user['id']);
        
        // Check if user has completed onboarding
        $needsOnboarding = false;
        if (empty($userData['phone']) || empty($userData['address']) || empty($userData['city'])) {
            $needsOnboarding = true;
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['pending_user_type'] = $user['user_type'];
        }
        
        if ($needsOnboarding) {
            // Redirect to onboarding form
            header('Location: onboarding.php');
            exit;
        } else {
            // Login the user
            Auth::login($userData);
            
            // Redirect based on user type
            switch($userData['user_type']) {
                case 'superadmin':
                case 'admin':
                    header('Location: admin/');
                    break;
                case 'service_provider':
                    header('Location: provider/');
                    break;
                case 'customer':
                    header('Location: customer/');
                    break;
                default:
                    header('Location: dashboard.php');
            }
            exit;
        }
        
    } catch (Exception $e) {
        $errors[] = "Social login failed: " . $e->getMessage();
    }
}

// Handle social login initiation
if (isset($_GET['social_login'])) {
    $provider = $_GET['social_login'];
    $oauth = new OAuth();
    
    switch ($provider) {
        case 'google':
            $oauth->handleGoogleLogin();
            break;
        case 'facebook':
            $oauth->handleFacebookLogin();
            break;
        case 'twitter':
            $oauth->handleTwitterLogin();
            break;
        case 'yahoo':
            $oauth->handleYahooLogin();
            break;
        default:
            $errors[] = "Unsupported social login provider";
    }
    exit;
}

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    switch($user['user_type']) {
        case 'superadmin':
        case 'admin':
            header('Location: admin/');
            break;
        case 'service_provider':
            header('Location: provider/');
            break;
        case 'customer':
            header('Location: customer/');
            break;
        default:
            header('Location: dashboard.php');
    }
    exit;
}

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $userObj = new User($db);
        $userData = $userObj->login($email, $password);
        
        if ($userData) {
            if ($userData['status'] === 'pending') {
                $errors[] = "Please verify your email address before logging in";
            } elseif ($userData['status'] === 'suspended') {
                $errors[] = "Your account has been suspended. Contact support for assistance";
            } else {
                Auth::login($userData);
                
                // Redirect based on user type
                switch($userData['user_type']) {
                    case 'superadmin':
                    case 'admin':
                        header('Location: admin/');
                        break;
                    case 'service_provider':
                        header('Location: provider/');
                        break;
                    case 'customer':
                        header('Location: customer/');
                        break;
                    default:
                        header('Location: dashboard.php');
                }
                exit;
            }
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}

include 'includes/header.php';
?>

<style>
    :root {
        --primary-50: #eff6ff;
        --primary-100: #dbeafe;
        --primary-200: #bfdbfe;
        --primary-300: #93c5fd;
        --primary-400: #60a5fa;
        --primary-500: #3b82f6;
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
        --primary-800: #1e40af;
        --primary-900: #1e3a8a;
        --secondary-50: #f5f3ff;
        --secondary-100: #ede9fe;
        --secondary-200: #ddd6fe;
        --secondary-300: #c4b5fd;
        --secondary-400: #a78bfa;
        --secondary-500: #8b5cf6;
        --secondary-600: #7c3aed;
        --secondary-700: #6d28d9;
        --secondary-800: #5b21b6;
        --secondary-900: #4c1d95;
        --success-50: #f0fdf4;
        --success-100: #dcfce7;
        --success-200: #bbf7d0;
        --success-300: #86efac;
        --success-400: #4ade80;
        --success-500: #22c55e;
        --success-600: #16a34a;
        --success-700: #15803d;
        --success-800: #166534;
        --success-900: #14532d;
        --warning-50: #fff7ed;
        --warning-100: #ffedd5;
        --warning-200: #fed7aa;
        --warning-300: #fdba74;
        --warning-400: #fb923c;
        --warning-500: #f97316;
        --warning-600: #ea580c;
        --warning-700: #c2410c;
        --warning-800: #9a3412;
        --warning-900: #7c2d12;
        --danger-50: #fef2f2;
        --danger-100: #fee2e2;
        --danger-200: #fecaca;
        --danger-300: #fca5a5;
        --danger-400: #f87171;
        --danger-500: #ef4444;
        --danger-600: #dc2626;
        --danger-700: #b91c1c;
        --danger-800: #991b1b;
        --danger-900: #7f1d1d;
        --neutral-50: #f8fafc;
        --neutral-100: #f1f5f9;
        --neutral-200: #e2e8f0;
        --neutral-300: #cbd5e1;
        --neutral-400: #94a3b8;
        --neutral-500: #64748b;
        --neutral-600: #475569;
        --neutral-700: #334155;
        --neutral-800: #1e293b;
        --neutral-900: #0f172a;
        --surface: #ffffff;
        --surface-secondary: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-tertiary: #94a3b8;
        --border: #e2e8f0;
        --radius-sm: 0.125rem;
        --radius-md: 0.25rem;
        --radius-lg: 0.5rem;
        --radius-xl: 0.75rem;
        --radius-2xl: 1rem;
        --radius-3xl: 1.5rem;
        --radius-full: 9999px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --spacing-1: 0.25rem;
        --spacing-2: 0.5rem;
        --spacing-3: 0.75rem;
        --spacing-4: 1rem;
        --spacing-5: 1.25rem;
        --spacing-6: 1.5rem;
        --spacing-7: 1.75rem;
        --spacing-8: 2rem;
        --spacing-9: 2.25rem;
        --spacing-10: 2.5rem;
        --spacing-11: 2.75rem;
        --spacing-12: 3rem;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-base: 1rem;
        --font-size-lg: 1.125rem;
        --font-size-xl: 1.25rem;
        --font-size-2xl: 1.5rem;
        --font-size-3xl: 1.875rem;
        --font-size-4xl: 2.25rem;
        --font-size-5xl: 3rem;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;
        --transition-all: all 0.2s ease-in-out;
        --transition-colors: color 0.2s ease-in-out;
        --transition-transform: transform 0.2s ease-in-out;
        --transition-shadow: box-shadow 0.2s ease-in-out;
        --transition-opacity: opacity 0.2s ease-in-out;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: var(--spacing-8) 0;
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
        position: relative;
        overflow: hidden;
    }
    
    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
    }
    
    .login-card {
        background-color: var(--surface);
        border-radius: var(--radius-3xl);
        box-shadow: var(--shadow-2xl);
        overflow: hidden;
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .login-image {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: var(--spacing-8);
        position: relative;
        overflow: hidden;
    }
    
    .login-image::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }
    
    .login-image-content {
        position: relative;
        z-index: 2;
    }
    
    .login-image h2 {
        color: white;
        font-size: var(--font-size-3xl);
        margin-bottom: var(--spacing-4);
    }
    
    .login-form {
        padding: var(--spacing-8);
    }
    
    .login-header {
        text-align: center;
        margin-bottom: var(--spacing-8);
    }
    
    .login-header h1 {
        font-size: var(--font-size-3xl);
        margin-bottom: var(--spacing-3);
        color: var(--text-primary);
    }
    
    .login-header p {
        color: var(--text-secondary);
        margin-bottom: 0;
    }
    
    .form-group {
        margin-bottom: var(--spacing-5);
    }
    
    .form-label {
        display: block;
        margin-bottom: var(--spacing-2);
        font-weight: var(--font-weight-medium);
        color: var(--text-primary);
    }
    
    .form-control {
        width: 100%;
        padding: var(--spacing-3) var(--spacing-4);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-base);
        transition: var(--transition-all);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-400);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .input-group {
        position: relative;
    }
    
    .input-group-append {
        position: absolute;
        right: var(--spacing-3);
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--text-secondary);
    }
    
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: var(--spacing-4);
    }
    
    .form-check-input {
        margin-right: var(--spacing-2);
    }
    
    .forgot-password {
        color: var(--primary-600);
        text-decoration: none;
        font-weight: var(--font-weight-medium);
        font-size: var(--font-size-sm);
        transition: var(--transition-colors);
    }
    
    .forgot-password:hover {
        color: var(--primary-800);
        text-decoration: underline;
    }
    
    .btn-login {
        width: 100%;
        padding: var(--spacing-3) var(--spacing-4);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        border-radius: var(--radius-full);
        transition: var(--transition-all);
        box-shadow: var(--shadow-md);
        background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%);
        border: none;
        color: white;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
    }
    
    .divider {
        text-align: center;
        margin: var(--spacing-6) 0;
        position: relative;
        color: var(--text-secondary);
    }
    
    .divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: var(--border);
        z-index: 1;
    }
    
    .divider span {
        position: relative;
        background-color: var(--surface);
        padding: 0 var(--spacing-3);
        z-index: 2;
    }
    
    .social-login {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-3);
        margin-bottom: var(--spacing-6);
    }
    
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-3);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background-color: var(--surface);
        color: var(--text-primary);
        font-weight: var(--font-weight-medium);
        transition: var(--transition-all);
        text-decoration: none;
        box-shadow: var(--shadow-sm);
    }
    
    .social-btn:hover {
        background-color: var(--neutral-50);
        border-color: var(--neutral-300);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .social-btn i {
        margin-right: var(--spacing-2);
    }
    
    .signup-link {
        text-align: center;
        color: var(--text-secondary);
        margin-top: var(--spacing-6);
    }
    
    .signup-link a {
        color: var(--primary-600);
        font-weight: var(--font-weight-semibold);
        text-decoration: none;
        transition: var(--transition-colors);
    }
    
    .signup-link a:hover {
        color: var(--primary-800);
        text-decoration: underline;
    }
    
    .alert {
        border-radius: var(--radius-lg);
        padding: var(--spacing-4);
        margin-bottom: var(--spacing-5);
    }
    
    .alert-danger {
        background-color: var(--danger-50);
        border-color: var(--danger-200);
        color: var(--danger-700);
    }
    
    .alert-success {
        background-color: var(--success-50);
        border-color: var(--success-200);
        color: var(--success-700);
    }
    
    @media (max-width: 768px) {
        .login-image {
            display: none;
        }
        
        .login-form {
            padding: var(--spacing-6);
        }
        
        .social-login {
            grid-template-columns: 1fr;
        }
        
        .login-card {
            margin: var(--spacing-4);
        }
    }
    
    @media (max-width: 576px) {
        .login-form {
            padding: var(--spacing-4);
        }
        
        .login-header h1 {
            font-size: var(--font-size-2xl);
        }
    }
</style>

<main class="login-container">
    <div class="container">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="login-image">
                        <div class="login-image-content">
                            <h2>Welcome Back to <?php echo $appName; ?></h2>
                            <p>Sign in to access your account and continue your journey</p>
                            <div class="mt-5">
                                <i class="fas fa-lock fa-5x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-form">
                        <div class="login-header">
                            <h1>Sign In</h1>
                            <p>Enter your credentials to access your account</p>
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
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="loginForm">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                    <div class="input-group-append">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="input-group-append" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>
                        
                        <div class="divider">
                            <span>or continue with</span>
                        </div>
                        
                        <div class="social-login">
                            <a href="?social_login=google" class="social-btn">
                                <i class="fab fa-google"></i> Google
                            </a>
                            <a href="?social_login=facebook" class="social-btn">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="?social_login=twitter" class="social-btn">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="?social_login=yahoo" class="social-btn">
                                <i class="fab fa-yahoo"></i> Yahoo
                            </a>
                        </div>
                        
                        <div class="signup-link">
                            <p>Don't have an account? <a href="register.php">Create Account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/login.js"></script>
<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
</body>
</html>