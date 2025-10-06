<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Service.php';
require_once '../classes/Booking.php';
require_once '../classes/Settings.php';

// Require service provider access
Auth::requireRole(['service_provider']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$service = new Service($db);
$booking = new Booking($db);
$settings = new Settings($db);

$current_user = Auth::getCurrentUser();
$app_settings = $settings->getAll(true);

// Get provider statistics
$stmt = $db->prepare("SELECT COUNT(*) FROM services WHERE provider_id = ?");
$stmt->execute([$current_user['id']]);
$total_services = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM services WHERE provider_id = ? AND is_active = 1");
$stmt->execute([$current_user['id']]);
$active_services = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE provider_id = ?");
$stmt->execute([$current_user['id']]);
$total_bookings = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE provider_id = ? AND status = 'pending'");
$stmt->execute([$current_user['id']]);
$pending_bookings = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT SUM(total_amount) FROM bookings WHERE provider_id = ? AND status = 'completed'");
$stmt->execute([$current_user['id']]);
$total_earnings = $stmt->fetchColumn() ?: 0;

$stmt = $db->prepare("SELECT COUNT(*) FROM reviews WHERE reviewee_id = ?");
$stmt->execute([$current_user['id']]);
$total_reviews = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT AVG(rating) FROM reviews WHERE reviewee_id = ?");
$stmt->execute([$current_user['id']]);
$avg_rating = $stmt->fetchColumn() ?: 0;

// Get recent bookings
$stmt = $db->prepare("SELECT b.*, s.title as service_title, u.first_name as customer_first_name, u.last_name as customer_last_name 
                      FROM bookings b 
                      JOIN services s ON b.service_id = s.id 
                      JOIN users u ON b.customer_id = u.id 
                      WHERE b.provider_id = ? 
                      ORDER BY b.created_at DESC 
                      LIMIT 5");
$stmt->execute([$current_user['id']]);
$recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active services
$stmt = $db->prepare("SELECT * FROM services WHERE provider_id = ? AND is_active = 1 LIMIT 4");
$stmt->execute([$current_user['id']]);
$active_services_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent reviews
$stmt = $db->prepare("SELECT r.*, u.first_name as reviewer_first_name, u.last_name as reviewer_last_name 
                      FROM reviews r 
                      JOIN users u ON r.reviewer_id = u.id 
                      WHERE r.reviewee_id = ? 
                      ORDER BY r.created_at DESC 
                      LIMIT 3");
$stmt->execute([$current_user['id']]);
$recent_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get earnings by month for the last 6 months
$earnings_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $stmt = $db->prepare("SELECT SUM(total_amount) as earnings 
                          FROM bookings 
                          WHERE provider_id = ? 
                          AND status = 'completed' 
                          AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$current_user['id'], $month]);
    $earnings = $stmt->fetchColumn() ?: 0;
    $earnings_data[] = [
        'month' => date('M', strtotime("-$i months")),
        'earnings' => $earnings
    ];
}

$provider_stats = [
    'total_services' => $total_services,
    'active_services' => $active_services,
    'total_bookings' => $total_bookings,
    'pending_bookings' => $pending_bookings,
    'total_earnings' => $total_earnings,
    'total_reviews' => $total_reviews,
    'avg_rating' => round($avg_rating, 1)
];

$pageTitle = "Enhanced Provider Dashboard";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Provider Dashboard Styles */
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
            border: 1px solid var(--border);
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
        
        /* Service Cards */
        .service-card {
            background: var(--surface);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: var(--transition-all);
            border: 1px solid var(--border);
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .service-image {
            height: 150px;
            object-fit: cover;
        }
        
        .service-content {
            padding: var(--spacing-4);
        }
        
        .service-title {
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-2);
        }
        
        .service-price {
            font-weight: var(--font-weight-bold);
            color: var(--primary-600);
            margin-bottom: var(--spacing-3);
        }
        
        .service-description {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-3);
        }
        
        /* Booking Items */
        .booking-item {
            display: flex;
            padding: var(--spacing-4);
            border-bottom: 1px solid var(--border);
        }
        
        .booking-item:last-child {
            border-bottom: none;
        }
        
        .booking-content {
            flex: 1;
        }
        
        .booking-title {
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-1);
        }
        
        .booking-customer {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-2);
        }
        
        .booking-date {
            color: var(--text-tertiary);
            font-size: var(--font-size-sm);
        }
        
        .booking-status {
            padding: var(--spacing-1) var(--spacing-2);
            border-radius: var(--radius-full);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-medium);
        }
        
        .status-pending {
            background: var(--warning-100);
            color: var(--warning-800);
        }
        
        .status-confirmed {
            background: var(--primary-100);
            color: var(--primary-800);
        }
        
        .status-completed {
            background: var(--success-100);
            color: var(--success-800);
        }
        
        .status-cancelled {
            background: var(--danger-100);
            color: var(--danger-800);
        }
        
        /* Review Items */
        .review-item {
            padding: var(--spacing-4);
            border-bottom: 1px solid var(--border);
        }
        
        .review-item:last-child {
            border-bottom: none;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-2);
        }
        
        .reviewer {
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
        }
        
        .rating {
            color: var(--warning-500);
        }
        
        .review-text {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
        
        /* Charts */
        .chart-container {
            background: var(--surface);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-6);
            margin-bottom: var(--spacing-6);
            border: 1px solid var(--border);
        }
        
        .chart-header {
            margin-bottom: var(--spacing-4);
        }
        
        .chart-title {
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-2);
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
        
        /* Animation classes */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--neutral-100);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-400);
            border-radius: var(--radius-full);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-600);
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
                    <a href="enhanced_index.php" class="nav-link active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="services.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span>My Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="bookings.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="availability.php" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <span>Availability</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="earnings.php" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Earnings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reviews.php" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
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
                <h1 class="topbar-title">Enhanced Provider Dashboard</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($current_user['first_name']); ?>!</span>
                    <img src="../assets/images/default-avatar.png" alt="User Avatar" class="user-avatar">
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Stats Cards -->
                <div class="row fade-in">
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary text-white">
                                <i class="fas fa-concierge-bell"></i>
                            </div>
                            <div class="stats-number"><?php echo $provider_stats['active_services']; ?></div>
                            <div class="stats-label">Active Services</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-success text-white">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-number"><?php echo $provider_stats['total_bookings']; ?></div>
                            <div class="stats-label">Total Bookings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-info text-white">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stats-number">KES <?php echo number_format($provider_stats['total_earnings'], 2); ?></div>
                            <div class="stats-label">Total Earnings</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning text-white">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stats-number"><?php echo $provider_stats['avg_rating']; ?></div>
                            <div class="stats-label">Avg Rating</div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Recent Activities -->
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <div class="chart-container fade-in">
                            <div class="chart-header">
                                <h3 class="chart-title">Earnings Overview</h3>
                            </div>
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card fade-in">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0">Recent Reviews</h3>
                                <a href="reviews.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <?php if (empty($recent_reviews)): ?>
                                <p class="text-center text-muted">No reviews yet.</p>
                            <?php else: ?>
                                <?php foreach ($recent_reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer"><?php echo htmlspecialchars($review['reviewer_first_name'] . ' ' . $review['reviewer_last_name']); ?></div>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings and Active Services -->
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <div class="stats-card fade-in">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0">Recent Bookings</h3>
                                <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <?php if (empty($recent_bookings)): ?>
                                <p class="text-center text-muted">No bookings yet.</p>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <div class="booking-item">
                                    <div class="booking-content">
                                        <div class="booking-title"><?php echo htmlspecialchars($booking['service_title']); ?></div>
                                        <div class="booking-customer">by <?php echo htmlspecialchars($booking['customer_first_name'] . ' ' . $booking['customer_last_name']); ?></div>
                                        <div class="booking-date"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?> at <?php echo date('g:i A', strtotime($booking['booking_time'])); ?></div>
                                    </div>
                                    <div>
                                        <span class="booking-status status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card fade-in">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0">Active Services</h3>
                                <a href="services.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <?php if (empty($active_services_list)): ?>
                                <p class="text-center text-muted">No active services. <a href="services.php">Create a service</a> to get started.</p>
                            <?php else: ?>
                                <?php foreach ($active_services_list as $service): ?>
                                <div class="mb-3">
                                    <div class="fw-semibold"><?php echo htmlspecialchars($service['title']); ?></div>
                                    <div class="text-primary fw-bold">KES <?php echo number_format($service['price'], 2); ?></div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Earnings Chart
        const earningsCtx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(earningsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($earnings_data, 'month')); ?>,
                datasets: [{
                    label: 'Earnings (KES)',
                    data: <?php echo json_encode(array_column($earnings_data, 'earnings')); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Fade in animation for elements
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>