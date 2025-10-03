<?php
require_once __DIR__ . '/app_settings.php';
require_once __DIR__ . '/../config/config.php';
$baseUrl = rtrim(APP_URL, '/');
$appName = AppSettings::get('app_name', 'JobsMtaani');
$appLogo = AppSettings::get('app_logo', '/jobsmtn.png');

// Determine user role if logged in
$userRole = null;
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    $userRole = $_SESSION['user_type'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . $appName : $appName; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Primary Color Palette - Professional Blue & Gold */
            --primary-50: #e6f0ff;
            --primary-100: #cce0ff;
            --primary-200: #99c0ff;
            --primary-300: #66a1ff;
            --primary-400: #3381ff;
            --primary-500: #0061ff;
            --primary-600: #004ecf;
            --primary-700: #003ba0;
            --primary-800: #002870;
            --primary-900: #001540;
            
            /* Secondary Color Palette - Gold Accent */
            --secondary-50: #fff8e6;
            --secondary-100: #ffefcc;
            --secondary-200: #ffe099;
            --secondary-300: #ffd166;
            --secondary-400: #ffc233;
            --secondary-500: #ffb300;
            --secondary-600: #cc8f00;
            --secondary-700: #996b00;
            --secondary-800: #664700;
            --secondary-900: #332400;
            
            /* Neutral Colors */
            --neutral-0: #ffffff;
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
            --neutral-950: #020617;
            
            /* Semantic Colors */
            --success-500: #10b981;
            --warning-500: #f59e0b;
            --danger-500: #ef4444;
            --info-500: #3b82f6;
            
            /* Spacing Scale */
            --spacing-0: 0rem;
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
            --spacing-14: 3.5rem;
            --spacing-16: 4rem;
            --spacing-20: 5rem;
            --spacing-24: 6rem;
            --spacing-28: 7rem;
            --spacing-32: 8rem;
            --spacing-36: 9rem;
            --spacing-40: 10rem;
            --spacing-44: 11rem;
            --spacing-48: 12rem;
            --spacing-52: 13rem;
            --spacing-56: 14rem;
            --spacing-60: 15rem;
            --spacing-64: 16rem;
            --spacing-72: 18rem;
            --spacing-80: 20rem;
            --spacing-96: 24rem;
            
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
            --font-size-6xl: 3.75rem;
            --font-size-7xl: 4.5rem;
            --font-size-8xl: 6rem;
            --font-size-9xl: 8rem;
            
            /* Font Weights */
            --font-weight-thin: 100;
            --font-weight-extralight: 200;
            --font-weight-light: 300;
            --font-weight-normal: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --font-weight-extrabold: 800;
            --font-weight-black: 900;
            
            /* Border Radius */
            --radius-none: 0;
            --radius-sm: 0.125rem;
            --radius: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-2xl: 1rem;
            --radius-3xl: 1.5rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Transitions */
            --transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-colors: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-opacity: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-transform: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-shadow: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-background: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: var(--font-size-base);
            line-height: 1.6;
            color: var(--neutral-800);
            background-color: var(--neutral-50);
            overflow-x: hidden;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--font-weight-bold);
            line-height: 1.2;
            color: var(--neutral-900);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: var(--font-size-5xl);
            font-weight: var(--font-weight-black);
        }

        h2 {
            font-size: var(--font-size-4xl);
            font-weight: var(--font-weight-extrabold);
        }

        h3 {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
        }

        h4 {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-semibold);
        }

        p {
            margin-bottom: 1rem;
            color: var(--neutral-700);
        }

        a {
            text-decoration: none;
            color: var(--primary-600);
            transition: var(--transition-colors);
        }

        a:hover {
            color: var(--primary-800);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--font-weight-semibold);
            border-radius: var(--radius-lg);
            transition: var(--transition-all);
            cursor: pointer;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: var(--font-size-base);
            line-height: 1.5;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-900) 100%);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-500) 0%, var(--secondary-700) 100%);
            color: var(--neutral-900);
            box-shadow: var(--shadow);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--secondary-600) 0%, var(--secondary-800) 100%);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-600);
            border: 2px solid var(--primary-600);
        }

        .btn-outline:hover {
            background: var(--primary-50);
            color: var(--primary-800);
            border-color: var(--primary-800);
        }

        /* Cards */
        .card {
            background: var(--neutral-0);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--neutral-200);
            transition: var(--transition-all);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-5px);
        }

        /* Forms */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--neutral-300);
            border-radius: var(--radius-lg);
            font-size: var(--font-size-base);
            transition: var(--transition-all);
            background-color: var(--neutral-0);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.1);
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--neutral-0);
            box-shadow: var(--shadow-sm);
            padding: var(--spacing-3) 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--font-weight-black);
            color: var(--primary-700) !important;
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .navbar-brand img {
            height: 45px;
            width: auto;
        }
        
        .navbar-nav .nav-link {
            font-weight: var(--font-weight-semibold);
            color: var(--neutral-700);
            padding: var(--spacing-3) var(--spacing-4);
            border-radius: var(--radius-lg);
            transition: var(--transition-all);
            position: relative;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-700);
            background-color: var(--primary-50);
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-500), var(--secondary-500));
            transition: var(--transition-all);
            border-radius: var(--radius-full);
        }
        
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 70%;
        }
        
        .btn-primary-navbar {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);
            border: none;
            color: white;
            font-weight: var(--font-weight-bold);
            padding: var(--spacing-2) var(--spacing-4);
            border-radius: var(--radius-full);
            transition: var(--transition-all);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary-navbar:hover {
            background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-900) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-secondary-navbar {
            background: linear-gradient(135deg, var(--secondary-500) 0%, var(--secondary-700) 100%);
            border: none;
            color: var(--neutral-900);
            font-weight: var(--font-weight-bold);
            padding: var(--spacing-2) var(--spacing-4);
            border-radius: var(--radius-full);
            transition: var(--transition-all);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-secondary-navbar:hover {
            background: linear-gradient(135deg, var(--secondary-600) 0%, var(--secondary-800) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            object-fit: cover;
            border: 2px solid var(--primary-200);
        }
        
        .dashboard-link {
            background: linear-gradient(135deg, var(--primary-50) 0%, var(--secondary-50) 100%);
            border: 1px solid var(--primary-200);
            color: var(--primary-700);
            font-weight: var(--font-weight-semibold);
            padding: var(--spacing-2) var(--spacing-3);
            border-radius: var(--radius-full);
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .dashboard-link:hover {
            background: linear-gradient(135deg, var(--primary-100) 0%, var(--secondary-100) 100%);
            border-color: var(--primary-300);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>/">
                <img src="<?php echo $baseUrl; ?>/includes/jobsmtn.png" alt="<?php echo $appName; ?>">
                <?php echo $appName; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/contact.php">Contact</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item me-2">
                            <a class="dashboard-link" href="<?php 
                                switch($userRole) {
                                    case 'superadmin':
                                    case 'admin':
                                        echo $baseUrl . '/admin/';
                                        break;
                                    case 'service_provider':
                                        echo $baseUrl . '/provider/';
                                        break;
                                    case 'customer':
                                        echo $baseUrl . '/customer/';
                                        break;
                                    default:
                                        echo $baseUrl . '/dashboard.php';
                                }
                            ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <?php if (!empty($_SESSION['profile_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile" class="user-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fs-4"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php 
                                    switch($userRole) {
                                        case 'superadmin':
                                        case 'admin':
                                            echo $baseUrl . '/admin/';
                                            break;
                                        case 'service_provider':
                                            echo $baseUrl . '/provider/';
                                            break;
                                        case 'customer':
                                            echo $baseUrl . '/customer/';
                                            break;
                                        default:
                                            echo $baseUrl . '/dashboard.php';
                                    }
                                ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-navbar ms-2" href="<?php echo $baseUrl; ?>/register.php">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main>