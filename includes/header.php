<?php
require_once __DIR__ . '/app_settings.php';
require_once __DIR__ . '/../config/config.php';
$baseUrl = rtrim(APP_URL, '/');
$appName = AppSettings::get('app_name', 'JobsMtaani');
$appLogo = AppSettings::get('app_logo', '/jobsmtn.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appName; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="/assets/css/professional-styles.css" rel="stylesheet">
    <style>
        /* Navbar Styles */
        .navbar {
            background-color: var(--surface);
            box-shadow: var(--shadow-sm);
            padding: var(--spacing-3) 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: var(--font-weight-bold);
            color: var(--primary-600) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
        
        .navbar-nav .nav-link {
            font-weight: var(--font-weight-medium);
            color: var(--text-secondary);
            padding: var(--spacing-3) var(--spacing-4);
            border-radius: var(--radius-lg);
            transition: var(--transition-all);
            position: relative;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-600);
            background-color: var(--primary-50);
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background-color: var(--primary-600);
            transition: var(--transition-all);
        }
        
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 70%;
        }
        
        .btn-primary-navbar {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
            border: none;
            color: white;
            font-weight: var(--font-weight-semibold);
            padding: var(--spacing-2) var(--spacing-4);
            border-radius: var(--radius-full);
            transition: var(--transition-all);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary-navbar:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>/">
                <img src="<?php echo $baseUrl; ?>/includes/jobsmtn.png" alt="<?php echo $appName; ?>">
                <?php echo $appName; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/contact.php">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> Account
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/dashboard.php">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/logout.php">Logout</a></li>
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
</body>
</html>
