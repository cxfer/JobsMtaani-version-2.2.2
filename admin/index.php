<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Service.php';
require_once '../classes/Booking.php';
require_once '../classes/Settings.php';

// Require superadmin access
Auth::requireRole(['superadmin']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$service = new Service($db);
$booking = new Booking($db);
$settings = new Settings($db);

// Get dashboard statistics
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_services' => $db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
    'total_bookings' => $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'pending_bookings' => $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
    'total_revenue' => $db->query("SELECT SUM(total_amount) FROM bookings WHERE status = 'completed'")->fetchColumn() ?: 0
];

$current_user = Auth::getCurrentUser();
$app_settings = $settings->getAll(true);
$pageTitle = "Superadmin Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . ($app_settings['app_name'] ?? 'JobsMtaani'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        /* Admin Dashboard Styles */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary-600) 0%, var(--secondary-600) 100%);
            color: white;
            padding: var(--spacing-6) 0;
            transition: var(--transition-all);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
        }
        
        .sidebar-header {
            padding: 0 var(--spacing-6) var(--spacing-6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: var(--spacing-4);
        }
        
        .sidebar-header h3 {
            color: white;
            margin-bottom: 0;
            font-weight: var(--font-weight-bold);
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: var(--spacing-2);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: var(--spacing-3) var(--spacing-6);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition-all);
            border-radius: 0 var(--radius-full) var(--radius-full) 0;
            margin-right: var(--spacing-4);
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link i {
            margin-right: var(--spacing-3);
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            transition: var(--transition-all);
        }
        
        .topbar {
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            padding: var(--spacing-4) var(--spacing-6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .topbar-title {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin: 0;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            object-fit: cover;
        }
        
        .content {
            padding: var(--spacing-6);
        }
        
        /* Stats Cards */
        .stats-card {
            background: var(--surface);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-6);
            height: 100%;
            transition: var(--transition-all);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-4);
        }
        
        .stats-number {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
            margin-bottom: var(--spacing-2);
        }
        
        .stats-label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar-header h3 span {
                display: none;
            }
            
            .sidebar-header h3::before {
                content: "JM";
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                width: 250px;
            }
            
            .sidebar.active .nav-link span {
                display: inline;
            }
            
            .sidebar.active .sidebar-header h3 span {
                display: inline;
            }
            
            .sidebar.active .sidebar-header h3::before {
                content: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Jobs<span>Mtaani</span></h3>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="services.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="bookings.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reviews.php" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h2 class="topbar-title">Superadmin Dashboard</h2>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></span>
                    <?php if (!empty($current_user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($current_user['profile_image']); ?>" alt="Profile" class="user-avatar">
                    <?php else: ?>
                        <div class="user-avatar bg-primary d-flex align-items-center justify-content-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Content -->
            <div class="content">
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h3>Welcome back, <?php echo htmlspecialchars($current_user['first_name']); ?>!</h3>
                        <p class="text-muted">Here's what's happening with your platform today.</p>
                    </div>
                </div>
                
                <div class="row mb-5">
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary text-white">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($stats['total_users']); ?></div>
                            <div class="stats-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-success text-white">
                                <i class="fas fa-concierge-bell fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($stats['total_services']); ?></div>
                            <div class="stats-label">Active Services</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-info text-white">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($stats['total_bookings']); ?></div>
                            <div class="stats-label">Total Bookings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning text-dark">
                                <i class="fas fa-money-bill-wave fa-lg"></i>
                            </div>
                            <div class="stats-number">KES <?php echo number_format($stats['total_revenue']); ?></div>
                            <div class="stats-label">Total Revenue</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Latest booking activities will be displayed here.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Platform Analytics</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Platform performance metrics will be displayed here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>