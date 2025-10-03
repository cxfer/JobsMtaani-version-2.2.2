<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Service.php';

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);

// Get services for display
$featured_services = $service->getAllServices(6, 0, null); // Get 6 featured services
$categories = $service->getCategories(); // Get all categories

// Process images for featured services
foreach ($featured_services as &$service_item) {
    if (!empty($service_item['images']) && is_array($service_item['images'])) {
        $service_item['image'] = $service_item['images'][0];
    } else {
        $service_item['image'] = '/public/abstract-service.png';
    }
}

$pageTitle = "Find Local Services in Kenya";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - JobsMtaani</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Premium Color Palette */
            --premium-primary-50: #e6f0ff;
            --premium-primary-100: #cce0ff;
            --premium-primary-200: #99c0ff;
            --premium-primary-300: #66a1ff;
            --premium-primary-400: #3381ff;
            --premium-primary-500: #0061ff;
            --premium-primary-600: #004ecf;
            --premium-primary-700: #003ba0;
            --premium-primary-800: #002870;
            --premium-primary-900: #001540;
            
            /* Premium Platinum Accent */
            --premium-accent-50: #f0f9ff;
            --premium-accent-100: #e0f2fe;
            --premium-accent-200: #bae6fd;
            --premium-accent-300: #7dd3fc;
            --premium-accent-400: #38bdf8;
            --premium-accent-500: #0ea5e9;
            --premium-accent-600: #0284c7;
            --premium-accent-700: #0369a1;
            --premium-accent-800: #075985;
            --premium-accent-900: #0c4a6e;
            
            /* Premium Neutrals */
            --premium-neutral-0: #ffffff;
            --premium-neutral-50: #f8fafc;
            --premium-neutral-100: #f1f5f9;
            --premium-neutral-200: #e2e8f0;
            --premium-neutral-300: #cbd5e1;
            --premium-neutral-400: #94a3b8;
            --premium-neutral-500: #64748b;
            --premium-neutral-600: #475569;
            --premium-neutral-700: #334155;
            --premium-neutral-800: #1e293b;
            --premium-neutral-900: #0f172a;
            --premium-neutral-950: #020617;
            
            /* Semantic Colors */
            --premium-success: #10b981;
            --premium-warning: #f59e0b;
            --premium-danger: #ef4444;
            --premium-info: #3b82f6;
            
            /* Premium Spacing Scale */
            --premium-spacing-0: 0rem;
            --premium-spacing-1: 0.25rem;
            --premium-spacing-2: 0.5rem;
            --premium-spacing-3: 0.75rem;
            --premium-spacing-4: 1rem;
            --premium-spacing-5: 1.25rem;
            --premium-spacing-6: 1.5rem;
            --premium-spacing-7: 1.75rem;
            --premium-spacing-8: 2rem;
            --premium-spacing-9: 2.25rem;
            --premium-spacing-10: 2.5rem;
            --premium-spacing-11: 2.75rem;
            --premium-spacing-12: 3rem;
            --premium-spacing-14: 3.5rem;
            --premium-spacing-16: 4rem;
            --premium-spacing-20: 5rem;
            --premium-spacing-24: 6rem;
            --premium-spacing-28: 7rem;
            --premium-spacing-32: 8rem;
            --premium-spacing-36: 9rem;
            --premium-spacing-40: 10rem;
            --premium-spacing-44: 11rem;
            --premium-spacing-48: 12rem;
            --premium-spacing-52: 13rem;
            --premium-spacing-56: 14rem;
            --premium-spacing-60: 15rem;
            --premium-spacing-64: 16rem;
            --premium-spacing-72: 18rem;
            --premium-spacing-80: 20rem;
            --premium-spacing-96: 24rem;
            
            /* Typography Scale */
            --premium-font-size-xs: 0.75rem;
            --premium-font-size-sm: 0.875rem;
            --premium-font-size-base: 1rem;
            --premium-font-size-lg: 1.125rem;
            --premium-font-size-xl: 1.25rem;
            --premium-font-size-2xl: 1.5rem;
            --premium-font-size-3xl: 1.875rem;
            --premium-font-size-4xl: 2.25rem;
            --premium-font-size-5xl: 3rem;
            --premium-font-size-6xl: 3.75rem;
            --premium-font-size-7xl: 4.5rem;
            --premium-font-size-8xl: 6rem;
            --premium-font-size-9xl: 8rem;
            
            /* Font Weights */
            --premium-font-weight-thin: 100;
            --premium-font-weight-extralight: 200;
            --premium-font-weight-light: 300;
            --premium-font-weight-normal: 400;
            --premium-font-weight-medium: 500;
            --premium-font-weight-semibold: 600;
            --premium-font-weight-bold: 700;
            --premium-font-weight-extrabold: 800;
            --premium-font-weight-black: 900;
            
            /* Border Radius */
            --premium-radius-none: 0;
            --premium-radius-sm: 0.125rem;
            --premium-radius: 0.25rem;
            --premium-radius-md: 0.375rem;
            --premium-radius-lg: 0.5rem;
            --premium-radius-xl: 0.75rem;
            --premium-radius-2xl: 1rem;
            --premium-radius-3xl: 1.5rem;
            --premium-radius-4xl: 2rem;
            --premium-radius-5xl: 2.5rem;
            --premium-radius-full: 9999px;
            
            /* Premium Shadows */
            --premium-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --premium-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --premium-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --premium-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --premium-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --premium-shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --premium-shadow-3xl: 0 35px 60px -15px rgb(0 0 0 / 0.3);
            --premium-shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Premium Transitions */
            --premium-transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-colors: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-opacity: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-transform: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-shadow: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-background: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --premium-transition-glow: box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: var(--premium-font-size-base);
            line-height: 1.6;
            color: var(--premium-neutral-800);
            background-color: var(--premium-neutral-50);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 97, 255, 0.03) 0%, transparent 15%),
                radial-gradient(circle at 90% 80%, rgba(14, 165, 233, 0.03) 0%, transparent 15%);
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--premium-font-weight-bold);
            line-height: 1.2;
            color: var(--premium-neutral-900);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: var(--premium-font-size-5xl);
            font-weight: var(--premium-font-weight-black);
        }

        h2 {
            font-size: var(--premium-font-size-4xl);
            font-weight: var(--premium-font-weight-extrabold);
        }

        h3 {
            font-size: var(--premium-font-size-3xl);
            font-weight: var(--premium-font-weight-bold);
        }

        h4 {
            font-size: var(--premium-font-size-2xl);
            font-weight: var(--premium-font-weight-semibold);
        }

        p {
            margin-bottom: 1rem;
            color: var(--premium-neutral-700);
        }

        a {
            text-decoration: none;
            color: var(--premium-primary-600);
            transition: var(--premium-transition-colors);
        }

        a:hover {
            color: var(--premium-primary-800);
        }

        /* Premium Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--premium-font-weight-semibold);
            border-radius: var(--premium-radius-lg);
            transition: var(--premium-transition-all);
            cursor: pointer;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: var(--premium-font-size-base);
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
            background: linear-gradient(135deg, var(--premium-primary-600) 0%, var(--premium-primary-800) 100%);
            color: white;
            box-shadow: var(--premium-shadow);
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
            background: linear-gradient(135deg, var(--premium-primary-700) 0%, var(--premium-primary-900) 100%);
            box-shadow: var(--premium-shadow-lg);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--premium-accent-500) 0%, var(--premium-accent-700) 100%);
            color: var(--premium-neutral-900);
            box-shadow: var(--premium-shadow);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--premium-accent-600) 0%, var(--premium-accent-800) 100%);
            box-shadow: var(--premium-shadow-lg);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--premium-primary-600);
            border: 2px solid var(--premium-primary-600);
        }

        .btn-outline:hover {
            background: var(--premium-primary-50);
            color: var(--premium-primary-800);
            border-color: var(--premium-primary-800);
        }

        /* Premium Cards */
        .card {
            background: var(--premium-neutral-0);
            border-radius: var(--premium-radius-2xl);
            box-shadow: var(--premium-shadow-md);
            border: 1px solid var(--premium-neutral-200);
            transition: var(--premium-transition-all);
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
            box-shadow: var(--premium-shadow-xl);
            transform: translateY(-5px);
        }

        /* Premium Forms */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--premium-neutral-300);
            border-radius: var(--premium-radius-lg);
            font-size: var(--premium-font-size-base);
            transition: var(--premium-transition-all);
            background-color: var(--premium-neutral-0);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--premium-primary-500);
            box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.1);
        }

        /* Premium Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--premium-primary-900) 0%, var(--premium-primary-700) 100%);
            color: white;
            padding: var(--premium-spacing-16) 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
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

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--premium-font-weight-black);
            font-size: var(--premium-font-size-6xl);
            margin-bottom: var(--premium-spacing-4);
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            line-height: 1.1;
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

        .hero-subtitle {
            font-size: var(--premium-font-size-xl);
            margin-bottom: var(--premium-spacing-8);
            opacity: 0.9;
            max-width: 700px;
            font-weight: var(--premium-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        /* Premium Search Section */
        .search-container {
            background: var(--premium-neutral-0);
            border-radius: var(--premium-radius-3xl);
            box-shadow: var(--premium-shadow-2xl);
            padding: var(--premium-spacing-6);
            margin-top: var(--premium-spacing-6);
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
            animation: float 6s ease-in-out infinite;
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--premium-primary-500), var(--premium-accent-500));
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .search-form {
            display: flex;
            gap: var(--premium-spacing-3);
            flex-wrap: wrap;
        }

        .search-input-group {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--premium-neutral-200);
            border-radius: var(--premium-radius-full);
            font-size: var(--premium-font-size-base);
            transition: var(--premium-transition-all);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--premium-primary-500);
            box-shadow: 0 0 0 4px rgba(0, 97, 255, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--premium-neutral-400);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--premium-primary-600) 0%, var(--premium-primary-800) 100%);
            color: white;
            border: none;
            border-radius: var(--premium-radius-full);
            padding: 0 2rem;
            font-weight: var(--premium-font-weight-bold);
            font-size: var(--premium-font-size-base);
            transition: var(--premium-transition-all);
            box-shadow: var(--premium-shadow-md);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .search-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .search-btn:hover::before {
            left: 100%;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, var(--premium-primary-700) 0%, var(--premium-primary-900) 100%);
            box-shadow: var(--premium-shadow-lg);
            transform: translateY(-2px);
        }

        /* Premium Categories Section */
        .categories-section {
            padding: var(--premium-spacing-16) 0;
            background-color: var(--premium-neutral-0);
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--premium-spacing-12);
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--premium-font-weight-extrabold);
            font-size: var(--premium-font-size-4xl);
            margin-bottom: var(--premium-spacing-4);
            color: var(--premium-neutral-900);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--premium-primary-500), var(--premium-accent-500));
            border-radius: var(--premium-radius-full);
        }

        .section-subtitle {
            font-size: var(--premium-font-size-lg);
            color: var(--premium-neutral-600);
            max-width: 700px;
            margin: 1rem auto 0;
            font-family: 'Poppins', sans-serif;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: var(--premium-spacing-6);
            margin-top: var(--premium-spacing-8);
        }

        .category-card {
            background: var(--premium-neutral-0);
            border-radius: var(--premium-radius-2xl);
            padding: var(--premium-spacing-6) var(--premium-spacing-4);
            text-align: center;
            transition: var(--premium-transition-all);
            border: 1px solid var(--premium-neutral-200);
            box-shadow: var(--premium-shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .category-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--premium-shadow-lg);
            border-color: var(--premium-primary-300);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--premium-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--premium-spacing-4);
            font-size: var(--premium-font-size-2xl);
            background: linear-gradient(135deg, var(--premium-primary-50) 0%, var(--premium-primary-100) 100%);
            color: var(--premium-primary-700);
            transition: var(--premium-transition-all);
            position: relative;
            z-index: 1;
            box-shadow: var(--premium-shadow);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 97, 255, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 97, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 97, 255, 0);
            }
        }

        .category-card:hover .category-icon {
            background: linear-gradient(135deg, var(--premium-primary-100) 0%, var(--premium-primary-200) 100%);
            transform: scale(1.1);
            box-shadow: var(--premium-shadow-lg);
        }

        .category-name {
            font-weight: var(--premium-font-weight-bold);
            color: var(--premium-neutral-800);
            margin-bottom: 0;
            font-size: var(--premium-font-size-base);
        }

        /* Premium Services Section */
        .services-section {
            padding: var(--premium-spacing-16) 0;
            background-color: var(--premium-neutral-50);
        }

        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--premium-spacing-8);
        }

        .view-all-link {
            color: var(--premium-primary-600);
            text-decoration: none;
            font-weight: var(--premium-font-weight-bold);
            font-size: var(--premium-font-size-base);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--premium-transition-all);
        }

        .view-all-link:hover {
            color: var(--premium-primary-800);
            transform: translateX(5px);
        }

        /* Premium Service Card */
        .service-card {
            background: var(--premium-neutral-0);
            border-radius: var(--premium-radius-2xl);
            overflow: hidden;
            box-shadow: var(--premium-shadow-md);
            border: 1px solid var(--premium-neutral-200);
            transition: var(--premium-transition-all);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .service-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--premium-shadow-xl);
            border-color: var(--premium-primary-300);
        }

        .service-image-container {
            position: relative;
            overflow: hidden;
        }

        .service-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--premium-transition-transform);
        }

        .service-card:hover .service-image {
            transform: scale(1.05);
        }

        .service-badge {
            position: absolute;
            top: var(--premium-spacing-4);
            left: var(--premium-spacing-4);
            background: linear-gradient(135deg, var(--premium-accent-500) 0%, var(--premium-accent-700) 100%);
            color: var(--premium-neutral-900);
            padding: 0.25rem 1rem;
            border-radius: var(--premium-radius-full);
            font-size: var(--premium-font-size-xs);
            font-weight: var(--premium-font-weight-bold);
            z-index: 2;
            box-shadow: var(--premium-shadow);
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .service-body {
            padding: var(--premium-spacing-5);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .service-provider {
            display: flex;
            align-items: center;
            margin-bottom: var(--premium-spacing-3);
        }

        .provider-avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--premium-radius-full);
            margin-right: var(--premium-spacing-3);
            object-fit: cover;
            border: 2px solid var(--premium-primary-200);
        }

        .provider-name {
            font-size: var(--premium-font-size-sm);
            color: var(--premium-neutral-600);
            margin-bottom: 0;
            font-weight: var(--premium-font-weight-medium);
        }

        .service-title {
            font-size: var(--premium-font-size-lg);
            font-weight: var(--premium-font-weight-bold);
            margin-bottom: var(--premium-spacing-3);
            color: var(--premium-neutral-900);
            line-height: 1.3;
        }

        .service-description {
            color: var(--premium-neutral-600);
            font-size: var(--premium-font-size-sm);
            margin-bottom: var(--premium-spacing-4);
            flex-grow: 1;
            line-height: 1.5;
        }

        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: var(--premium-spacing-4);
            padding-top: var(--premium-spacing-4);
            border-top: 1px solid var(--premium-neutral-200);
        }

        .service-price {
            font-size: var(--premium-font-size-xl);
            font-weight: var(--premium-font-weight-extrabold);
            color: var(--premium-primary-700);
        }

        .service-rating {
            display: flex;
            align-items: center;
            color: var(--premium-warning);
            font-weight: var(--premium-font-weight-semibold);
        }

        /* Premium How It Works Section */
        .how-it-works-section {
            padding: var(--premium-spacing-16) 0;
            background-color: var(--premium-neutral-0);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--premium-spacing-8);
            margin-top: var(--premium-spacing-8);
        }

        .step-card {
            background: var(--premium-neutral-0);
            border-radius: var(--premium-radius-2xl);
            padding: var(--premium-spacing-8);
            text-align: center;
            transition: var(--premium-transition-all);
            border: 1px solid var(--premium-neutral-200);
            box-shadow: var(--premium-shadow-sm);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--premium-shadow-lg);
            border-color: var(--premium-primary-300);
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: var(--premium-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--premium-spacing-4);
            font-weight: var(--premium-font-weight-bold);
            font-size: var(--premium-font-size-lg);
            background: linear-gradient(135deg, var(--premium-primary-500) 0%, var(--premium-primary-700) 100%);
            color: white;
            box-shadow: var(--premium-shadow);
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--premium-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--premium-spacing-4);
            font-size: var(--premium-font-size-2xl);
            background: linear-gradient(135deg, var(--premium-accent-100) 0%, var(--premium-accent-200) 100%);
            color: var(--premium-accent-700);
            box-shadow: var(--premium-shadow);
            position: relative;
            z-index: 1;
        }

        .step-title {
            font-weight: var(--premium-font-weight-bold);
            color: var(--premium-neutral-900);
            margin-bottom: var(--premium-spacing-3);
            font-size: var(--premium-font-size-lg);
        }

        .step-description {
            color: var(--premium-neutral-600);
            margin-bottom: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Premium CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--premium-primary-800) 0%, var(--premium-primary-900) 100%);
            color: white;
            padding: var(--premium-spacing-16) 0;
            text-align: center;
            margin: var(--premium-spacing-16) 0;
            border-radius: var(--premium-radius-3xl);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
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

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .cta-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--premium-font-weight-black);
            font-size: var(--premium-font-size-5xl);
            margin-bottom: var(--premium-spacing-4);
            line-height: 1.1;
            background: linear-gradient(to right, #ffffff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            animation: text-glow 3s infinite alternate;
        }

        .cta-subtitle {
            font-size: var(--premium-font-size-xl);
            margin-bottom: var(--premium-spacing-8);
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: var(--premium-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        .cta-btn {
            background: linear-gradient(135deg, var(--premium-accent-500) 0%, var(--premium-accent-700) 100%);
            color: var(--premium-neutral-900);
            border: none;
            border-radius: var(--premium-radius-full);
            padding: 1rem 2.5rem;
            font-weight: var(--premium-font-weight-extrabold);
            font-size: var(--premium-font-size-lg);
            transition: var(--premium-transition-all);
            box-shadow: var(--premium-shadow-lg);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .cta-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }

        .cta-btn:hover::before {
            left: 100%;
        }

        .cta-btn:hover {
            background: linear-gradient(135deg, var(--premium-accent-600) 0%, var(--premium-accent-800) 100%);
            box-shadow: var(--premium-shadow-2xl);
            transform: translateY(-3px);
        }

        /* Premium Footer */
        .footer {
            background: var(--premium-neutral-900);
            color: var(--premium-neutral-300);
            padding: var(--premium-spacing-16) 0 var(--premium-spacing-8);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--premium-spacing-8);
            margin-bottom: var(--premium-spacing-12);
        }

        .footer-title {
            color: white;
            font-weight: var(--premium-font-weight-bold);
            margin-bottom: var(--premium-spacing-5);
            font-size: var(--premium-font-size-lg);
            position: relative;
            padding-bottom: var(--premium-spacing-3);
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--premium-primary-500);
            border-radius: var(--premium-radius-full);
        }

        .footer-link {
            color: var(--premium-neutral-400);
            text-decoration: none;
            display: block;
            margin-bottom: var(--premium-spacing-3);
            transition: var(--premium-transition-all);
            font-size: var(--premium-font-size-base);
        }

        .footer-link:hover {
            color: white;
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: var(--premium-spacing-3);
            margin-top: var(--premium-spacing-4);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: var(--premium-neutral-800);
            color: white;
            border-radius: var(--premium-radius-full);
            transition: var(--premium-transition-all);
        }

        .social-link:hover {
            background: var(--premium-primary-600);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: var(--premium-spacing-8);
            border-top: 1px solid var(--premium-neutral-800);
            font-size: var(--premium-font-size-sm);
            color: var(--premium-neutral-500);
        }

        /* Premium Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--premium-font-size-4xl);
            }
            
            .hero-subtitle {
                font-size: var(--premium-font-size-lg);
            }
            
            .section-title {
                font-size: var(--premium-font-size-3xl);
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-btn {
                padding: 1rem;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: var(--premium-spacing-12) 0;
            }
            
            .hero-title {
                font-size: var(--premium-font-size-3xl);
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
                font-size: var(--premium-font-size-lg);
            }
        }
    </style>
</head>
<body>
    <!-- Premium Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 1.75rem;">
                <span class="text-primary">Jobs</span><span class="text-accent">Mtaani</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> Account
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Premium Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Find Trusted Local Services</h1>
            <p class="hero-subtitle">Connect with skilled professionals in your area for all your home, business, and personal needs.</p>
            
            <!-- Premium Search Section -->
            <div class="search-container">
                <form class="search-form" method="GET" action="services.php">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" name="search" placeholder="What service are you looking for?">
                    </div>
                    <div class="search-input-group">
                        <i class="fas fa-map-marker-alt search-icon"></i>
                        <input type="text" class="search-input" name="location" placeholder="Your location (e.g. Nairobi, Mombasa)">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Premium Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Popular Categories</h2>
                <p class="section-subtitle">Browse services by category to find exactly what you need</p>
            </div>
            
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="services.php" class="btn btn-outline">
                    <i class="fas fa-th-large me-2"></i>View All Categories
                </a>
            </div>
        </div>
    </section>

    <!-- Premium Featured Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-header-flex">
                <div class="section-header">
                    <h2 class="section-title">Featured Services</h2>
                    <p class="section-subtitle">Handpicked services from our top-rated providers</p>
                </div>
                <a href="services.php" class="view-all-link">
                    View All Services <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="row">
                <?php foreach ($featured_services as $service_item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-image-container">
                            <img src="<?php echo htmlspecialchars($service_item['image']); ?>" class="service-image" alt="<?php echo htmlspecialchars($service_item['title']); ?>">
                            <?php if ($service_item['featured']): ?>
                            <span class="service-badge">FEATURED</span>
                            <?php endif; ?>
                        </div>
                        <div class="service-body">
                            <div class="service-provider">
                                <img src="<?php echo !empty($service_item['profile_image']) ? $service_item['profile_image'] : '/public/placeholder-user.jpg'; ?>" alt="Provider" class="provider-avatar">
                                <p class="provider-name"><?php echo htmlspecialchars($service_item['first_name'] . ' ' . $service_item['last_name']); ?></p>
                            </div>
                            <h3 class="service-title"><?php echo htmlspecialchars($service_item['title']); ?></h3>
                            <p class="service-description"><?php echo htmlspecialchars(substr($service_item['description'], 0, 100)) . '...'; ?></p>
                            <div class="service-meta">
                                <div class="service-price">KES <?php echo number_format($service_item['price']); ?></div>
                                <div class="service-rating">
                                    <?php if ($service_item['avg_rating']): ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $service_item['avg_rating']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i - 0.5 <= $service_item['avg_rating']): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="ms-1">(<?php echo $service_item['review_count'] ?? 0; ?>)</span>
                                    <?php else: ?>
                                        <span class="text-muted">No reviews</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Premium How It Works Section -->
    <section class="how-it-works-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Getting quality services has never been easier</p>
            </div>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="step-title">Find Services</h3>
                    <p class="step-description">Browse our extensive collection of verified local service providers.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="step-title">Book & Schedule</h3>
                    <p class="step-description">Select your preferred provider and schedule a convenient time.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="step-title">Enjoy & Review</h3>
                    <p class="step-description">Get quality service and leave a review for other customers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Premium CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-subtitle">Join thousands of satisfied customers who trust JobsMtaani for their service needs.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="services.php" class="cta-btn">
                    <i class="fas fa-concierge-bell me-2"></i>Browse Services
                </a>
                <a href="register.php?user_type=provider" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-user-tie me-2"></i>Become a Provider
                </a>
            </div>
        </div>
    </section>

    <!-- Premium Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">JobsMtaani</h3>
                    <p>Connecting customers with trusted local service providers across Kenya.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-title">For Customers</h3>
                    <a href="services.php" class="footer-link">Browse Services</a>
                    <a href="how-it-works.php" class="footer-link">How It Works</a>
                    <a href="safety.php" class="footer-link">Safety Tips</a>
                    <a href="support.php" class="footer-link">Customer Support</a>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-title">For Providers</h3>
                    <a href="register.php?user_type=provider" class="footer-link">Become a Provider</a>
                    <a href="provider-benefits.php" class="footer-link">Provider Benefits</a>
                    <a href="provider-resources.php" class="footer-link">Resources</a>
                    <a href="provider-support.php" class="footer-link">Provider Support</a>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-title">Company</h3>
                    <a href="about.php" class="footer-link">About Us</a>
                    <a href="careers.php" class="footer-link">Careers</a>
                    <a href="blog.php" class="footer-link">Blog</a>
                    <a href="contact.php" class="footer-link">Contact Us</a>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 JobsMtaani. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to category cards on hover
        document.addEventListener('DOMContentLoaded', function() {
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.category-icon');
                    icon.style.boxShadow = '0 10px 15px -3px rgba(0, 97, 255, 0.2), 0 4px 6px -4px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.category-icon');
                    icon.style.boxShadow = 'var(--premium-shadow)';
                });
            });
            
            // Add glow effect to cards on hover
            const cards = document.querySelectorAll('.card, .service-card, .step-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 20px 25px -5px rgba(0, 97, 255, 0.2), 0 8px 10px -6px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--premium-shadow-md)';
                });
            });
        });
    </script>
</body>
</html>