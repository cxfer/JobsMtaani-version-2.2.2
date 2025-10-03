<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Service.php';
require_once '../classes/Booking.php';
require_once '../classes/Settings.php';

// Require customer access
Auth::requireRole(['customer']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$service = new Service($db);
$booking = new Booking($db);
$settings = new Settings($db);

$current_user = Auth::getCurrentUser();
$app_settings = $settings->getAll(true);

// Get customer statistics
$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ?");
$stmt->execute([$current_user['id']]);
$total_bookings = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status = 'pending'");
$stmt->execute([$current_user['id']]);
$pending_bookings = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status = 'completed'");
$stmt->execute([$current_user['id']]);
$completed_bookings = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
$stmt->execute([$current_user['id']]);
$favorite_services = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT SUM(total_amount) FROM bookings WHERE customer_id = ? AND status = 'completed'");
$stmt->execute([$current_user['id']]);
$total_spent = $stmt->fetchColumn() ?: 0;

$customer_stats = [
    'total_bookings' => $total_bookings,
    'pending_bookings' => $pending_bookings,
    'completed_bookings' => $completed_bookings,
    'favorite_services' => $favorite_services,
    'total_spent' => $total_spent
];

$pageTitle = "Customer Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . ($app_settings['app_name'] ?? 'JobsMtaani'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Ultimate Premium Color Palette */
            --ultimate-primary-50: #e6f0ff;
            --ultimate-primary-100: #cce0ff;
            --ultimate-primary-200: #99c0ff;
            --ultimate-primary-300: #66a1ff;
            --ultimate-primary-400: #3381ff;
            --ultimate-primary-500: #0061ff;
            --ultimate-primary-600: #004ecf;
            --ultimate-primary-700: #003ba0;
            --ultimate-primary-800: #002870;
            --ultimate-primary-900: #001540;
            
            /* Ultimate Premium Platinum Accent */
            --ultimate-accent-50: #f0f9ff;
            --ultimate-accent-100: #e0f2fe;
            --ultimate-accent-200: #bae6fd;
            --ultimate-accent-300: #7dd3fc;
            --ultimate-accent-400: #38bdf8;
            --ultimate-accent-500: #0ea5e9;
            --ultimate-accent-600: #0284c7;
            --ultimate-accent-700: #0369a1;
            --ultimate-accent-800: #075985;
            --ultimate-accent-900: #0c4a6e;
            
            /* Ultimate Premium Neutrals */
            --ultimate-neutral-0: #ffffff;
            --ultimate-neutral-50: #f8fafc;
            --ultimate-neutral-100: #f1f5f9;
            --ultimate-neutral-200: #e2e8f0;
            --ultimate-neutral-300: #cbd5e1;
            --ultimate-neutral-400: #94a3b8;
            --ultimate-neutral-500: #64748b;
            --ultimate-neutral-600: #475569;
            --ultimate-neutral-700: #334155;
            --ultimate-neutral-800: #1e293b;
            --ultimate-neutral-900: #0f172a;
            --ultimate-neutral-950: #020617;
            
            /* Semantic Colors */
            --ultimate-success: #10b981;
            --ultimate-warning: #f59e0b;
            --ultimate-danger: #ef4444;
            --ultimate-info: #3b82f6;
            
            /* Ultimate Premium Spacing Scale */
            --ultimate-spacing-0: 0rem;
            --ultimate-spacing-1: 0.25rem;
            --ultimate-spacing-2: 0.5rem;
            --ultimate-spacing-3: 0.75rem;
            --ultimate-spacing-4: 1rem;
            --ultimate-spacing-5: 1.25rem;
            --ultimate-spacing-6: 1.5rem;
            --ultimate-spacing-7: 1.75rem;
            --ultimate-spacing-8: 2rem;
            --ultimate-spacing-9: 2.25rem;
            --ultimate-spacing-10: 2.5rem;
            --ultimate-spacing-11: 2.75rem;
            --ultimate-spacing-12: 3rem;
            --ultimate-spacing-14: 3.5rem;
            --ultimate-spacing-16: 4rem;
            --ultimate-spacing-20: 5rem;
            --ultimate-spacing-24: 6rem;
            --ultimate-spacing-28: 7rem;
            --ultimate-spacing-32: 8rem;
            --ultimate-spacing-36: 9rem;
            --ultimate-spacing-40: 10rem;
            --ultimate-spacing-44: 11rem;
            --ultimate-spacing-48: 12rem;
            --ultimate-spacing-52: 13rem;
            --ultimate-spacing-56: 14rem;
            --ultimate-spacing-60: 15rem;
            --ultimate-spacing-64: 16rem;
            --ultimate-spacing-72: 18rem;
            --ultimate-spacing-80: 20rem;
            --ultimate-spacing-96: 24rem;
            
            /* Typography Scale */
            --ultimate-font-size-xs: 0.75rem;
            --ultimate-font-size-sm: 0.875rem;
            --ultimate-font-size-base: 1rem;
            --ultimate-font-size-lg: 1.125rem;
            --ultimate-font-size-xl: 1.25rem;
            --ultimate-font-size-2xl: 1.5rem;
            --ultimate-font-size-3xl: 1.875rem;
            --ultimate-font-size-4xl: 2.25rem;
            --ultimate-font-size-5xl: 3rem;
            --ultimate-font-size-6xl: 3.75rem;
            --ultimate-font-size-7xl: 4.5rem;
            --ultimate-font-size-8xl: 6rem;
            --ultimate-font-size-9xl: 8rem;
            
            /* Font Weights */
            --ultimate-font-weight-thin: 100;
            --ultimate-font-weight-extralight: 200;
            --ultimate-font-weight-light: 300;
            --ultimate-font-weight-normal: 400;
            --ultimate-font-weight-medium: 500;
            --ultimate-font-weight-semibold: 600;
            --ultimate-font-weight-bold: 700;
            --ultimate-font-weight-extrabold: 800;
            --ultimate-font-weight-black: 900;
            
            /* Border Radius */
            --ultimate-radius-none: 0;
            --ultimate-radius-sm: 0.125rem;
            --ultimate-radius: 0.25rem;
            --ultimate-radius-md: 0.375rem;
            --ultimate-radius-lg: 0.5rem;
            --ultimate-radius-xl: 0.75rem;
            --ultimate-radius-2xl: 1rem;
            --ultimate-radius-3xl: 1.5rem;
            --ultimate-radius-4xl: 2rem;
            --ultimate-radius-5xl: 2.5rem;
            --ultimate-radius-full: 9999px;
            
            /* Ultimate Premium Shadows */
            --ultimate-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --ultimate-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --ultimate-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --ultimate-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --ultimate-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --ultimate-shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --ultimate-shadow-3xl: 0 35px 60px -15px rgb(0 0 0 / 0.3);
            --ultimate-shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Ultimate Premium Transitions */
            --ultimate-transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-colors: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-opacity: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-transform: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-shadow: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-background: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ultimate-transition-glow: box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: var(--ultimate-font-size-base);
            line-height: 1.6;
            color: var(--ultimate-neutral-800);
            background-color: var(--ultimate-neutral-50);
            overflow-x: hidden;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--ultimate-font-weight-bold);
            line-height: 1.2;
            color: var(--ultimate-neutral-900);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: var(--ultimate-font-size-4xl);
            font-weight: var(--ultimate-font-weight-black);
        }

        h2 {
            font-size: var(--ultimate-font-size-3xl);
            font-weight: var(--ultimate-font-weight-extrabold);
        }

        h3 {
            font-size: var(--ultimate-font-size-2xl);
            font-weight: var(--ultimate-font-weight-bold);
        }

        h4 {
            font-size: var(--ultimate-font-size-xl);
            font-weight: var(--ultimate-font-weight-semibold);
        }

        p {
            margin-bottom: 1rem;
            color: var(--ultimate-neutral-700);
        }

        a {
            text-decoration: none;
            color: var(--ultimate-primary-600);
            transition: var(--ultimate-transition-colors);
        }

        a:hover {
            color: var(--ultimate-primary-800);
        }

        /* Ultimate Premium Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--ultimate-font-weight-semibold);
            border-radius: var(--ultimate-radius-lg);
            transition: var(--ultimate-transition-all);
            cursor: pointer;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: var(--ultimate-font-size-base);
            line-height: 1.5;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.3));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .btn:hover::before {
            opacity: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--ultimate-primary-600) 0%, var(--ultimate-primary-800) 100%);
            color: white;
            box-shadow: var(--ultimate-shadow);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover::after {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--ultimate-primary-700) 0%, var(--ultimate-primary-900) 100%);
            box-shadow: var(--ultimate-shadow-lg);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--ultimate-accent-500) 0%, var(--ultimate-accent-700) 100%);
            color: var(--ultimate-neutral-900);
            box-shadow: var(--ultimate-shadow);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--ultimate-accent-600) 0%, var(--ultimate-accent-800) 100%);
            box-shadow: var(--ultimate-shadow-lg);
            transform: translateY(-2px);
        }

        /* Ultimate Premium Cards */
        .card {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            box-shadow: var(--ultimate-shadow-md);
            border: 1px solid var(--ultimate-neutral-200);
            transition: var(--ultimate-transition-all);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .card:hover {
            box-shadow: var(--ultimate-shadow-xl);
            transform: translateY(-5px);
        }

        /* Dashboard Container */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Ultimate Premium Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--ultimate-primary-800) 0%, var(--ultimate-primary-900) 100%);
            color: white;
            padding: var(--ultimate-spacing-6) 0;
            transition: var(--ultimate-transition-all);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--ultimate-shadow-2xl);
        }

        .sidebar-header {
            padding: 0 var(--ultimate-spacing-6) var(--ultimate-spacing-6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: var(--ultimate-spacing-4);
        }

        .sidebar-header h3 {
            color: white;
            margin-bottom: 0;
            font-weight: var(--ultimate-font-weight-black);
            font-size: var(--ultimate-font-size-2xl);
            display: flex;
            align-items: center;
            gap: var(--ultimate-spacing-2);
        }

        .sidebar-header h3::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background: var(--ultimate-accent-500);
            border-radius: var(--ultimate-radius-full);
            box-shadow: 0 0 10px var(--ultimate-accent-500);
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: var(--ultimate-spacing-2);
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: var(--ultimate-spacing-3) var(--ultimate-spacing-6);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--ultimate-transition-all);
            border-radius: 0 var(--ultimate-radius-full) var(--ultimate-radius-full) 0;
            margin-right: var(--ultimate-spacing-4);
            font-weight: var(--ultimate-font-weight-medium);
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            opacity: 1;
        }

        .nav-link i {
            margin-right: var(--ultimate-spacing-3);
            width: 24px;
            text-align: center;
            font-size: var(--ultimate-font-size-lg);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: var(--ultimate-transition-all);
        }

        .topbar {
            background: var(--ultimate-neutral-0);
            box-shadow: var(--ultimate-shadow-sm);
            padding: var(--ultimate-spacing-4) var(--ultimate-spacing-6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-title {
            font-size: var(--ultimate-font-size-2xl);
            font-weight: var(--ultimate-font-weight-black);
            color: var(--ultimate-neutral-900);
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: var(--ultimate-spacing-3);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: var(--ultimate-font-weight-bold);
            color: var(--ultimate-neutral-900);
            font-size: var(--ultimate-font-size-base);
            margin-bottom: 0;
        }

        .user-role {
            font-size: var(--ultimate-font-size-sm);
            color: var(--ultimate-neutral-500);
            margin-bottom: 0;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: var(--ultimate-radius-full);
            object-fit: cover;
            border: 3px solid var(--ultimate-primary-200);
            box-shadow: var(--ultimate-shadow);
        }

        .content {
            padding: var(--ultimate-spacing-6);
        }

        /* Stats Cards */
        .stats-card {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            box-shadow: var(--ultimate-shadow-md);
            padding: var(--ultimate-spacing-6);
            height: 100%;
            transition: var(--ultimate-transition-all);
            border: 1px solid var(--ultimate-neutral-200);
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--ultimate-shadow-xl);
            border-color: var(--ultimate-primary-300);
        }

        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: var(--ultimate-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--ultimate-spacing-4);
            font-size: var(--ultimate-font-size-2xl);
            transition: var(--ultimate-transition-all);
            position: relative;
            z-index: 1;
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1);
        }

        .stats-number {
            font-size: var(--ultimate-font-size-4xl);
            font-weight: var(--ultimate-font-weight-black);
            margin-bottom: var(--ultimate-spacing-2);
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, var(--ultimate-primary-600) 0%, var(--ultimate-primary-800) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 1;
        }

        .stats-label {
            color: var(--ultimate-neutral-600);
            font-size: var(--ultimate-font-size-base);
            font-weight: var(--ultimate-font-weight-semibold);
            position: relative;
            z-index: 1;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--ultimate-primary-800) 0%, var(--ultimate-primary-900) 100%);
            color: white;
            border-radius: var(--ultimate-radius-2xl);
            padding: var(--ultimate-spacing-6);
            margin-bottom: var(--ultimate-spacing-6);
            box-shadow: var(--ultimate-shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 20%),
                linear-gradient(135deg, rgba(0, 97, 255, 0.2) 0%, rgba(14, 165, 233, 0.2) 100%);
            z-index: 1;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--ultimate-font-weight-black);
            font-size: var(--ultimate-font-size-3xl);
            margin-bottom: var(--ultimate-spacing-3);
            background: linear-gradient(to right, #ffffff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            animation: text-glow 3s infinite alternate;
        }

        @keyframes text-glow {
            0% {
                text-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
            }
            100% {
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.4), 0 0 30px rgba(14, 165, 233, 0.3);
            }
        }

        .welcome-subtitle {
            font-size: var(--ultimate-font-size-lg);
            opacity: 0.9;
            max-width: 700px;
            font-weight: var(--ultimate-font-weight-medium);
            font-family: 'Poppins', sans-serif;
            margin-bottom: 0;
        }

        /* Recent Bookings */
        .bookings-table thead {
            background: var(--ultimate-primary-50);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: var(--ultimate-radius-full);
            font-size: var(--ultimate-font-size-sm);
            font-weight: var(--ultimate-font-weight-semibold);
        }

        .status-pending {
            background: var(--ultimate-warning-100);
            color: var(--ultimate-warning-800);
        }

        .status-confirmed {
            background: var(--ultimate-info-100);
            color: var(--ultimate-info-800);
        }

        .status-completed {
            background: var(--ultimate-success-100);
            color: var(--ultimate-success-800);
        }

        .status-cancelled {
            background: var(--ultimate-danger-100);
            color: var(--ultimate-danger-800);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
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
                margin-left: 80px;
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
                width: 280px;
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
            
            .topbar {
                padding: var(--ultimate-spacing-3) var(--ultimate-spacing-4);
            }
            
            .content {
                padding: var(--ultimate-spacing-4);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Ultimate Premium Sidebar -->
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
                    <a href="bookings.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="favorites.php" class="nav-link">
                        <i class="fas fa-heart"></i>
                        <span>Favorites</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reviews.php" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>My Reviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
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
                <h2 class="topbar-title">Customer Dashboard</h2>
                <div class="user-menu">
                    <div class="user-info">
                        <p class="user-name"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></p>
                        <p class="user-role">Customer</p>
                    </div>
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
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="welcome-content">
                        <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($current_user['first_name']); ?>!</h1>
                        <p class="welcome-subtitle">Here's what's happening with your account today.</p>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-5">
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary text-white">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($customer_stats['total_bookings']); ?></div>
                            <div class="stats-label">Total Bookings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning text-dark">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($customer_stats['pending_bookings']); ?></div>
                            <div class="stats-label">Pending Bookings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-success text-white">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div class="stats-number"><?php echo number_format($customer_stats['completed_bookings']); ?></div>
                            <div class="stats-label">Completed Bookings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-info text-white">
                                <i class="fas fa-money-bill-wave fa-lg"></i>
                            </div>
                            <div class="stats-number">KES <?php echo number_format($customer_stats['total_spent']); ?></div>
                            <div class="stats-label">Total Spent</div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Bookings and Favorites -->
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mb-0">Recent Bookings</h3>
                                    <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover bookings-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Booking #</th>
                                                <th>Service</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>BK-2025-001</strong></td>
                                                <td>Plumbing Repair</td>
                                                <td>Oct 5, 2025<br>10:00 AM</td>
                                                <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                                <td>KES 3,500</td>
                                            </tr>
                                            <tr>
                                                <td><strong>BK-2025-002</strong></td>
                                                <td>House Cleaning</td>
                                                <td>Oct 3, 2025<br>2:00 PM</td>
                                                <td><span class="status-badge status-completed">Completed</span></td>
                                                <td>KES 2,800</td>
                                            </tr>
                                            <tr>
                                                <td><strong>BK-2025-003</strong></td>
                                                <td>Electrical Work</td>
                                                <td>Oct 1, 2025<br>9:00 AM</td>
                                                <td><span class="status-badge status-pending">Pending</span></td>
                                                <td>KES 4,200</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mb-0">Favorite Services</h3>
                                    <a href="favorites.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-concierge-bell text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Plumbing Services</h5>
                                        <p class="text-muted mb-0">4.8 ★ (120 reviews)</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-home text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">House Cleaning</h5>
                                        <p class="text-muted mb-0">4.6 ★ (98 reviews)</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-bolt text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Electrical Work</h5>
                                        <p class="text-muted mb-0">4.9 ★ (156 reviews)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to stats cards on hover
        document.addEventListener('DOMContentLoaded', function() {
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.stats-icon');
                    icon.style.boxShadow = '0 10px 15px -3px rgba(0, 97, 255, 0.2), 0 4px 6px -4px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.stats-icon');
                    icon.style.boxShadow = 'none';
                });
            });
            
            // Add glow effect to cards on hover
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 20px 25px -5px rgba(0, 97, 255, 0.2), 0 8px 10px -6px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--ultimate-shadow-md)';
                });
            });
        });
    </script>
</body>
</html>