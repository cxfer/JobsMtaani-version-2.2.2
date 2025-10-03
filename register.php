<?php
// register.php
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

/*
 * Handle regular form POST registration (email/password)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['code'])) {
    $userObj = new User();
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_type = isset($_POST['user_type']) && $_POST['user_type'] === 'provider' ? 'service_provider' : 'customer';
    $terms = isset($_POST['terms']);

    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (!$terms) $errors[] = "You must accept the terms and conditions";

    if (empty($errors) && $userObj->emailExists($email)) {
        $errors[] = "Email already exists";
    }

    if ($user_type === 'service_provider') {
        $business_name = trim($_POST['business_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $description = trim($_POST['description'] ?? '');
        if (empty($business_name)) $errors[] = "Business name is required for service providers";
        if (empty($category)) $errors[] = "Service category is required";
        if (empty($description)) $errors[] = "Business description is required";
    }

    if (empty($errors)) {
        $userData = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'password' => Auth::hashPassword($password),
            'user_type' => $user_type,
            'status' => 'pending',
            'subscription_plan' => 'free'
        ];
        if ($user_type === 'service_provider') {
            $userData['business_name'] = $business_name;
            $userData['category'] = $category;
            $userData['description'] = $description;
        }

        $userId = $userObj->createUser($userData);
        if ($userId) {
            $verificationToken = bin2hex(random_bytes(32));
            if (method_exists($userObj, 'setVerificationToken')) {
                $userObj->setVerificationToken($userId, $verificationToken);
            }
            $success = "Registration successful! Please check your email to verify your account.";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?php echo htmlspecialchars($appName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Complete Modern Design System */
        :root {
            /* Primary Colors */
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
            
            /* Secondary Colors */
            --secondary-50: #fff7ed;
            --secondary-100: #ffedd5;
            --secondary-200: #fed7aa;
            --secondary-300: #fdba74;
            --secondary-400: #fb923c;
            --secondary-500: #f97316;
            --secondary-600: #ea580c;
            --secondary-700: #c2410c;
            --secondary-800: #9a3412;
            --secondary-900: #7c2d12;
            
            /* Accent Colors */
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --accent-info: #0ea5e9;
            
            /* Neutral Colors */
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
            
            /* Semantic Colors */
            --background: var(--neutral-50);
            --surface: #ffffff;
            --border: var(--neutral-200);
            --text-primary: var(--neutral-900);
            --text-secondary: var(--neutral-600);
            --text-tertiary: var(--neutral-400);
            
            /* Spacing System */
            --spacing-0: 0;
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
            
            /* Typography Scale */
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            --font-size-5xl: 3rem;
            
            /* Font Weights */
            --font-weight-light: 300;
            --font-weight-normal: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --font-weight-extrabold: 800;
            
            /* Border Radius */
            --radius-xs: 0.125rem;
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-2xl: 1rem;
            --radius-3xl: 1.5rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            
            /* Transitions */
            --transition-all: all 0.2s ease;
            --transition-colors: color 0.2s ease;
            --transition-opacity: opacity 0.2s ease;
            --transition-transform: transform 0.2s ease;
            --transition-shadow: box-shadow 0.2s ease;
        }
        
        /* Base Styles */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: var(--spacing-8) 0;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            position: relative;
            overflow: hidden;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
        }
        
        .register-card {
            background-color: var(--surface);
            border-radius: var(--radius-3xl);
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .register-image {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: var(--spacing-8);
            position: relative;
            overflow: hidden;
        }
        
        .register-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .register-image-content {
            position: relative;
            z-index: 2;
        }
        
        .register-image h2 {
            color: white;
            font-size: var(--font-size-3xl);
            margin-bottom: var(--spacing-4);
        }
        
        .register-form {
            padding: var(--spacing-8);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: var(--spacing-8);
        }
        
        .register-header h1 {
            font-size: var(--font-size-3xl);
            margin-bottom: var(--spacing-3);
            color: var(--text-primary);
        }
        
        .register-header p {
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
        
        .form-control, .form-select {
            width: 100%;
            padding: var(--spacing-3) var(--spacing-4);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            font-size: var(--font-size-base);
            transition: var(--transition-all);
            background-color: var(--surface);
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-400);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-4);
        }
        
        .form-check-input {
            margin-right: var(--spacing-2);
        }
        
        .btn-register {
            width: 100%;
            padding: var(--spacing-3) var(--spacing-4);
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-semibold);
            border-radius: var(--radius-full);
            transition: var(--transition-all);
            box-shadow: var(--shadow-md);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
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
        
        .login-link {
            text-align: center;
            color: var(--text-secondary);
            margin-top: var(--spacing-6);
        }
        
        .login-link a {
            color: var(--primary-600);
            font-weight: var(--font-weight-semibold);
            text-decoration: none;
            transition: var(--transition-colors);
        }
        
        .login-link a:hover {
            color: var(--primary-800);
            text-decoration: underline;
        }
        
        .user-type-selector {
            display: flex;
            gap: var(--spacing-4);
            margin-bottom: var(--spacing-5);
        }
        
        .user-type-option {
            flex: 1;
            text-align: center;
            padding: var(--spacing-5);
            border: 2px solid var(--border);
            border-radius: var(--radius-xl);
            cursor: pointer;
            transition: var(--transition-all);
            background-color: var(--surface);
        }
        
        .user-type-option:hover {
            border-color: var(--primary-300);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .user-type-option.active {
            border-color: var(--primary-600);
            background-color: var(--primary-50);
            box-shadow: var(--shadow-md);
        }
        
        .user-type-option i {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--spacing-3);
            color: var(--primary-600);
        }
        
        .alert {
            border-radius: var(--radius-lg);
            padding: var(--spacing-4);
            margin-bottom: var(--spacing-5);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: var(--accent-danger);
        }
        
        .alert-success {
            background-color: #dcfce7;
            border-color: #bbf7d0;
            color: var(--accent-success);
        }
        
        @media (max-width: 768px) {
            .register-image {
                display: none;
            }
            
            .register-form {
                padding: var(--spacing-6);
            }
            
            .social-login {
                grid-template-columns: 1fr;
            }
            
            .user-type-selector {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <main class="register-container">
        <div class="container">
            <div class="register-card">
                <div class="row g-0">
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="register-image">
                            <div class="register-image-content">
                                <h2>Join <?php echo $appName; ?></h2>
                                <p>Connect with trusted service professionals or offer your services to customers</p>
                                <div class="mt-5">
                                    <i class="fas fa-users fa-5x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="register-form">
                            <div class="register-header">
                                <h1>Create Account</h1>
                                <p>Join our community of service providers and customers</p>
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
                            
                            <form method="POST" id="registerForm">
                                <div class="user-type-selector">
                                    <div class="user-type-option active" data-type="customer">
                                        <i class="fas fa-user"></i>
                                        <h4>Customer</h4>
                                        <p class="mb-0">Find services</p>
                                    </div>
                                    <div class="user-type-option" data-type="provider">
                                        <i class="fas fa-briefcase"></i>
                                        <h4>Provider</h4>
                                        <p class="mb-0">Offer services</p>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="user_type" id="user_type" value="customer">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
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
                            
                            <div class="login-link">
                                <p>Already have an account? <a href="login.php">Sign In</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // User type selector
        document.querySelectorAll('.user-type-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.user-type-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                this.classList.add('active');
                document.getElementById('user_type').value = this.dataset.type;
            });
        });
    </script>
</body>
</html>