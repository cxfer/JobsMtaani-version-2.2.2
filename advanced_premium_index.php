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
    <title><?php echo $pageTitle; ?> - JobsMtaani Advanced Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Advanced Premium Color Palette */
            --advanced-primary-50: #e6f0ff;
            --advanced-primary-100: #cce0ff;
            --advanced-primary-200: #99c0ff;
            --advanced-primary-300: #66a1ff;
            --advanced-primary-400: #3381ff;
            --advanced-primary-500: #0061ff;
            --advanced-primary-600: #004ecf;
            --advanced-primary-700: #003ba0;
            --advanced-primary-800: #002870;
            --advanced-primary-900: #001540;
            
            /* Advanced Premium Gold Accent */
            --advanced-accent-50: #fff8e6;
            --advanced-accent-100: #ffefcc;
            --advanced-accent-200: #ffe099;
            --advanced-accent-300: #ffd166;
            --advanced-accent-400: #ffc233;
            --advanced-accent-500: #ffb300;
            --advanced-accent-600: #cc8f00;
            --advanced-accent-700: #996b00;
            --advanced-accent-800: #664700;
            --advanced-accent-900: #332400;
            
            /* Advanced Premium Neutrals */
            --advanced-neutral-0: #ffffff;
            --advanced-neutral-50: #f8fafc;
            --advanced-neutral-100: #f1f5f9;
            --advanced-neutral-200: #e2e8f0;
            --advanced-neutral-300: #cbd5e1;
            --advanced-neutral-400: #94a3b8;
            --advanced-neutral-500: #64748b;
            --advanced-neutral-600: #475569;
            --advanced-neutral-700: #334155;
            --advanced-neutral-800: #1e293b;
            --advanced-neutral-900: #0f172a;
            --advanced-neutral-950: #020617;
            
            /* Semantic Colors */
            --advanced-success: #10b981;
            --advanced-warning: #f59e0b;
            --advanced-danger: #ef4444;
            --advanced-info: #3b82f6;
            
            /* Advanced Premium Spacing Scale */
            --advanced-spacing-0: 0rem;
            --advanced-spacing-1: 0.25rem;
            --advanced-spacing-2: 0.5rem;
            --advanced-spacing-3: 0.75rem;
            --advanced-spacing-4: 1rem;
            --advanced-spacing-5: 1.25rem;
            --advanced-spacing-6: 1.5rem;
            --advanced-spacing-7: 1.75rem;
            --advanced-spacing-8: 2rem;
            --advanced-spacing-9: 2.25rem;
            --advanced-spacing-10: 2.5rem;
            --advanced-spacing-11: 2.75rem;
            --advanced-spacing-12: 3rem;
            --advanced-spacing-14: 3.5rem;
            --advanced-spacing-16: 4rem;
            --advanced-spacing-20: 5rem;
            --advanced-spacing-24: 6rem;
            --advanced-spacing-28: 7rem;
            --advanced-spacing-32: 8rem;
            --advanced-spacing-36: 9rem;
            --advanced-spacing-40: 10rem;
            --advanced-spacing-44: 11rem;
            --advanced-spacing-48: 12rem;
            --advanced-spacing-52: 13rem;
            --advanced-spacing-56: 14rem;
            --advanced-spacing-60: 15rem;
            --advanced-spacing-64: 16rem;
            --advanced-spacing-72: 18rem;
            --advanced-spacing-80: 20rem;
            --advanced-spacing-96: 24rem;
            
            /* Typography Scale */
            --advanced-font-size-xs: 0.75rem;
            --advanced-font-size-sm: 0.875rem;
            --advanced-font-size-base: 1rem;
            --advanced-font-size-lg: 1.125rem;
            --advanced-font-size-xl: 1.25rem;
            --advanced-font-size-2xl: 1.5rem;
            --advanced-font-size-3xl: 1.875rem;
            --advanced-font-size-4xl: 2.25rem;
            --advanced-font-size-5xl: 3rem;
            --advanced-font-size-6xl: 3.75rem;
            --advanced-font-size-7xl: 4.5rem;
            --advanced-font-size-8xl: 6rem;
            --advanced-font-size-9xl: 8rem;
            
            /* Font Weights */
            --advanced-font-weight-thin: 100;
            --advanced-font-weight-extralight: 200;
            --advanced-font-weight-light: 300;
            --advanced-font-weight-normal: 400;
            --advanced-font-weight-medium: 500;
            --advanced-font-weight-semibold: 600;
            --advanced-font-weight-bold: 700;
            --advanced-font-weight-extrabold: 800;
            --advanced-font-weight-black: 900;
            
            /* Border Radius */
            --advanced-radius-none: 0;
            --advanced-radius-sm: 0.125rem;
            --advanced-radius: 0.25rem;
            --advanced-radius-md: 0.375rem;
            --advanced-radius-lg: 0.5rem;
            --advanced-radius-xl: 0.75rem;
            --advanced-radius-2xl: 1rem;
            --advanced-radius-3xl: 1.5rem;
            --advanced-radius-4xl: 2rem;
            --advanced-radius-5xl: 2.5rem;
            --advanced-radius-full: 9999px;
            
            /* Advanced Premium Shadows */
            --advanced-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --advanced-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --advanced-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --advanced-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --advanced-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --advanced-shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --advanced-shadow-3xl: 0 35px 60px -15px rgb(0 0 0 / 0.3);
            --advanced-shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Advanced Premium Transitions */
            --advanced-transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-colors: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-opacity: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-transform: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-shadow: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-background: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --advanced-transition-glow: box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: var(--advanced-font-size-base);
            line-height: 1.6;
            color: var(--advanced-neutral-800);
            background-color: var(--advanced-neutral-50);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 97, 255, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 179, 0, 0.05) 0%, transparent 20%);
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--advanced-font-weight-bold);
            line-height: 1.2;
            color: var(--advanced-neutral-900);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: var(--advanced-font-size-5xl);
            font-weight: var(--advanced-font-weight-black);
        }

        h2 {
            font-size: var(--advanced-font-size-4xl);
            font-weight: var(--advanced-font-weight-extrabold);
        }

        h3 {
            font-size: var(--advanced-font-size-3xl);
            font-weight: var(--advanced-font-weight-bold);
        }

        h4 {
            font-size: var(--advanced-font-size-2xl);
            font-weight: var(--advanced-font-weight-semibold);
        }

        p {
            margin-bottom: 1rem;
            color: var(--advanced-neutral-700);
        }

        a {
            text-decoration: none;
            color: var(--advanced-primary-600);
            transition: var(--advanced-transition-colors);
        }

        a:hover {
            color: var(--advanced-primary-800);
        }

        /* Advanced Premium Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--advanced-font-weight-semibold);
            border-radius: var(--advanced-radius-lg);
            transition: var(--advanced-transition-all);
            cursor: pointer;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: var(--advanced-font-size-base);
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
            background: linear-gradient(135deg, var(--advanced-primary-600) 0%, var(--advanced-primary-800) 100%);
            color: white;
            box-shadow: var(--advanced-shadow);
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
            background: linear-gradient(135deg, var(--advanced-primary-700) 0%, var(--advanced-primary-900) 100%);
            box-shadow: var(--advanced-shadow-lg);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--advanced-accent-500) 0%, var(--advanced-accent-700) 100%);
            color: var(--advanced-neutral-900);
            box-shadow: var(--advanced-shadow);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--advanced-accent-600) 0%, var(--advanced-accent-800) 100%);
            box-shadow: var(--advanced-shadow-lg);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--advanced-primary-600);
            border: 2px solid var(--advanced-primary-600);
        }

        .btn-outline:hover {
            background: var(--advanced-primary-50);
            color: var(--advanced-primary-800);
            border-color: var(--advanced-primary-800);
        }

        /* Advanced Premium Cards */
        .card {
            background: var(--advanced-neutral-0);
            border-radius: var(--advanced-radius-2xl);
            box-shadow: var(--advanced-shadow-md);
            border: 1px solid var(--advanced-neutral-200);
            transition: var(--advanced-transition-all);
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
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.05) 0%, rgba(255, 179, 0, 0.02) 100%);
            z-index: 0;
        }

        .card:hover {
            box-shadow: var(--advanced-shadow-xl);
            transform: translateY(-5px);
        }

        /* Advanced Premium Forms */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--advanced-neutral-300);
            border-radius: var(--advanced-radius-lg);
            font-size: var(--advanced-font-size-base);
            transition: var(--advanced-transition-all);
            background-color: var(--advanced-neutral-0);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--advanced-primary-500);
            box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.1);
        }

        /* Advanced Premium Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--advanced-primary-900) 0%, var(--advanced-primary-700) 100%);
            color: white;
            padding: var(--advanced-spacing-20) 0;
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
                radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 25%),
                radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 25%),
                linear-gradient(135deg, rgba(0, 97, 255, 0.3) 0%, rgba(255, 179, 0, 0.3) 100%);
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
            font-weight: var(--advanced-font-weight-black);
            font-size: var(--advanced-font-size-7xl);
            margin-bottom: var(--advanced-spacing-4);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            line-height: 1.1;
            background: linear-gradient(to right, #ffffff, #ffd166);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            animation: text-glow 3s infinite alternate;
        }

        @keyframes text-glow {
            0% {
                text-shadow: 0 0 10px rgba(255, 255, 255, 0.3), 0 0 20px rgba(255, 255, 255, 0.2);
            }
            100% {
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.5), 0 0 30px rgba(255, 210, 102, 0.4), 0 0 40px rgba(255, 210, 102, 0.3);
            }
        }

        .hero-subtitle {
            font-size: var(--advanced-font-size-xl);
            margin-bottom: var(--advanced-spacing-8);
            opacity: 0.95;
            max-width: 700px;
            font-weight: var(--advanced-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        /* Advanced Premium Search Section */
        .search-container {
            background: var(--advanced-neutral-0);
            border-radius: var(--advanced-radius-3xl);
            box-shadow: var(--advanced-shadow-2xl);
            padding: var(--advanced-spacing-6);
            margin-top: var(--advanced-spacing-8);
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
            animation: float 6s ease-in-out infinite;
            border: 1px solid var(--advanced-neutral-200);
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--advanced-primary-500), var(--advanced-accent-500));
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .search-form {
            display: flex;
            gap: var(--advanced-spacing-3);
            flex-wrap: wrap;
        }

        .search-input-group {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1.25rem 1rem 1.25rem 3.5rem;
            border: 2px solid var(--advanced-neutral-200);
            border-radius: var(--advanced-radius-full);
            font-size: var(--advanced-font-size-base);
            transition: var(--advanced-transition-all);
            font-weight: var(--advanced-font-weight-medium);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--advanced-primary-500);
            box-shadow: 0 0 0 4px rgba(0, 97, 255, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--advanced-neutral-400);
            font-size: var(--advanced-font-size-lg);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--advanced-primary-600) 0%, var(--advanced-primary-800) 100%);
            color: white;
            border: none;
            border-radius: var(--advanced-radius-full);
            padding: 0 2.5rem;
            font-weight: var(--advanced-font-weight-bold);
            font-size: var(--advanced-font-size-base);
            transition: var(--advanced-transition-all);
            box-shadow: var(--advanced-shadow-md);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .search-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }

        .search-btn:hover::before {
            left: 100%;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, var(--advanced-primary-700) 0%, var(--advanced-primary-900) 100%);
            box-shadow: var(--advanced-shadow-lg);
            transform: translateY(-2px);
        }

        /* Advanced Premium Categories Section */
        .categories-section {
            padding: var(--advanced-spacing-20) 0;
            background-color: var(--advanced-neutral-0);
            position: relative;
        }

        .categories-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 5% 10%, rgba(0, 97, 255, 0.03) 0%, transparent 15%),
                radial-gradient(circle at 95% 90%, rgba(255, 179, 0, 0.03) 0%, transparent 15%);
            z-index: 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--advanced-spacing-12);
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--advanced-font-weight-black);
            font-size: var(--advanced-font-size-5xl);
            margin-bottom: var(--advanced-spacing-4);
            color: var(--advanced-neutral-900);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--advanced-primary-500), var(--advanced-accent-500));
            border-radius: var(--advanced-radius-full);
        }

        .section-subtitle {
            font-size: var(--advanced-font-size-xl);
            color: var(--advanced-neutral-600);
            max-width: 700px;
            margin: 1.5rem auto 0;
            font-family: 'Poppins', sans-serif;
            font-weight: var(--advanced-font-weight-medium);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: var(--advanced-spacing-6);
            margin-top: var(--advanced-spacing-8);
            position: relative;
            z-index: 1;
        }

        .category-card {
            background: var(--advanced-neutral-0);
            border-radius: var(--advanced-radius-2xl);
            padding: var(--advanced-spacing-7) var(--advanced-spacing-5);
            text-align: center;
            transition: var(--advanced-transition-all);
            border: 1px solid var(--advanced-neutral-200);
            box-shadow: var(--advanced-shadow-sm);
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
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.05) 0%, rgba(255, 179, 0, 0.02) 100%);
            z-index: 0;
        }

        .category-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--advanced-shadow-lg);
            border-color: var(--advanced-primary-300);
        }

        .category-icon {
            width: 90px;
            height: 90px;
            border-radius: var(--advanced-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--advanced-spacing-4);
            font-size: var(--advanced-font-size-3xl);
            background: linear-gradient(135deg, var(--advanced-primary-50) 0%, var(--advanced-primary-100) 100%);
            color: var(--advanced-primary-700);
            transition: var(--advanced-transition-all);
            position: relative;
            z-index: 1;
            box-shadow: var(--advanced-shadow);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 97, 255, 0.5);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(0, 97, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 97, 255, 0);
            }
        }

        .category-card:hover .category-icon {
            background: linear-gradient(135deg, var(--advanced-primary-100) 0%, var(--advanced-primary-200) 100%);
            transform: scale(1.15);
            box-shadow: var(--advanced-shadow-lg);
        }

        .category-name {
            font-weight: var(--advanced-font-weight-bold);
            color: var(--advanced-neutral-800);
            margin-bottom: 0;
            font-size: var(--advanced-font-size-lg);
            font-family: 'Montserrat', sans-serif;
        }

        /* Advanced Premium Services Section */
        .services-section {
            padding: var(--advanced-spacing-20) 0;
            background-color: var(--advanced-neutral-50);
        }

        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--advanced-spacing-8);
        }

        .view-all-link {
            color: var(--advanced-primary-600);
            text-decoration: none;
            font-weight: var(--advanced-font-weight-bold);
            font-size: var(--advanced-font-size-base);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--advanced-transition-all);
            padding: 0.75rem 1.5rem;
            border-radius: var(--advanced-radius-full);
            border: 2px solid var(--advanced-primary-200);
        }

        .view-all-link:hover {
            color: var(--advanced-primary-800);
            background: var(--advanced-primary-50);
            transform: translateX(5px);
            border-color: var(--advanced-primary-300);
        }

        /* Advanced Premium Service Card */
        .service-card {
            background: var(--advanced-neutral-0);
            border-radius: var(--advanced-radius-2xl);
            overflow: hidden;
            box-shadow: var(--advanced-shadow-md);
            border: 1px solid var(--advanced-neutral-200);
            transition: var(--advanced-transition-all);
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
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.05) 0%, rgba(255, 179, 0, 0.02) 100%);
            z-index: 0;
        }

        .service-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--advanced-shadow-xl);
            border-color: var(--advanced-primary-300);
        }

        .service-image-container {
            position: relative;
            overflow: hidden;
        }

        .service-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: var(--advanced-transition-transform);
        }

        .service-card:hover .service-image {
            transform: scale(1.08);
        }

        .service-badge {
            position: absolute;
            top: var(--advanced-spacing-4);
            left: var(--advanced-spacing-4);
            background: linear-gradient(135deg, var(--advanced-accent-500) 0%, var(--advanced-accent-700) 100%);
            color: var(--advanced-neutral-900);
            padding: 0.35rem 1.25rem;
            border-radius: var(--advanced-radius-full);
            font-size: var(--advanced-font-size-sm);
            font-weight: var(--advanced-font-weight-bold);
            z-index: 2;
            box-shadow: var(--advanced-shadow);
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 179, 0, 0.4);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 179, 0, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 179, 0, 0);
            }
        }

        .service-body {
            padding: var(--advanced-spacing-6);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .service-provider {
            display: flex;
            align-items: center;
            margin-bottom: var(--advanced-spacing-3);
        }

        .provider-avatar {
            width: 40px;
            height: 40px;
            border-radius: var(--advanced-radius-full);
            margin-right: var(--advanced-spacing-3);
            object-fit: cover;
            border: 2px solid var(--advanced-primary-200);
            box-shadow: var(--advanced-shadow-sm);
        }

        .provider-name {
            font-size: var(--advanced-font-size-base);
            color: var(--advanced-neutral-700);
            margin-bottom: 0;
            font-weight: var(--advanced-font-weight-semibold);
            font-family: 'Poppins', sans-serif;
        }

        .service-title {
            font-size: var(--advanced-font-size-xl);
            font-weight: var(--advanced-font-weight-extrabold);
            margin-bottom: var(--advanced-spacing-3);
            color: var(--advanced-neutral-900);
            line-height: 1.3;
            font-family: 'Montserrat', sans-serif;
        }

        .service-description {
            color: var(--advanced-neutral-600);
            font-size: var(--advanced-font-size-sm);
            margin-bottom: var(--advanced-spacing-4);
            flex-grow: 1;
            line-height: 1.6;
            font-family: 'Poppins', sans-serif;
        }

        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: var(--advanced-spacing-4);
            padding-top: var(--advanced-spacing-4);
            border-top: 1px solid var(--advanced-neutral-200);
        }

        .service-price {
            font-size: var(--advanced-font-size-2xl);
            font-weight: var(--advanced-font-weight-black);
            color: var(--advanced-primary-700);
            font-family: 'Montserrat', sans-serif;
        }

        .service-rating {
            display: flex;
            align-items: center;
            color: var(--advanced-warning);
            font-weight: var(--advanced-font-weight-semibold);
        }

        /* Advanced Premium How It Works Section */
        .how-it-works-section {
            padding: var(--advanced-spacing-20) 0;
            background-color: var(--advanced-neutral-0);
            position: relative;
        }

        .how-it-works-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 5% 10%, rgba(255, 179, 0, 0.03) 0%, transparent 15%),
                radial-gradient(circle at 95% 90%, rgba(0, 97, 255, 0.03) 0%, transparent 15%);
            z-index: 0;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--advanced-spacing-8);
            margin-top: var(--advanced-spacing-8);
            position: relative;
            z-index: 1;
        }

        .step-card {
            background: var(--advanced-neutral-0);
            border-radius: var(--advanced-radius-2xl);
            padding: var(--advanced-spacing-9);
            text-align: center;
            transition: var(--advanced-transition-all);
            border: 1px solid var(--advanced-neutral-200);
            box-shadow: var(--advanced-shadow-sm);
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
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.05) 0%, rgba(255, 179, 0, 0.02) 100%);
            z-index: 0;
        }

        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--advanced-shadow-lg);
            border-color: var(--advanced-primary-300);
        }

        .step-number {
            width: 60px;
            height: 60px;
            border-radius: var(--advanced-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--advanced-spacing-5);
            font-weight: var(--advanced-font-weight-black);
            font-size: var(--advanced-font-size-xl);
            background: linear-gradient(135deg, var(--advanced-primary-500) 0%, var(--advanced-primary-700) 100%);
            color: white;
            box-shadow: var(--advanced-shadow);
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 90px;
            height: 90px;
            border-radius: var(--advanced-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--advanced-spacing-5);
            font-size: var(--advanced-font-size-3xl);
            background: linear-gradient(135deg, var(--advanced-accent-100) 0%, var(--advanced-accent-200) 100%);
            color: var(--advanced-accent-700);
            box-shadow: var(--advanced-shadow);
            position: relative;
            z-index: 1;
        }

        .step-title {
            font-weight: var(--advanced-font-weight-extrabold);
            color: var(--advanced-neutral-900);
            margin-bottom: var(--advanced-spacing-4);
            font-size: var(--advanced-font-size-xl);
            font-family: 'Montserrat', sans-serif;
        }

        .step-description {
            color: var(--advanced-neutral-600);
            margin-bottom: 0;
            font-family: 'Poppins', sans-serif;
            font-size: var(--advanced-font-size-base);
            line-height: 1.7;
        }

        /* Advanced Premium CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--advanced-primary-800) 0%, var(--advanced-primary-900) 100%);
            color: white;
            padding: var(--advanced-spacing-20) 0;
            text-align: center;
            margin: var(--advanced-spacing-20) 0;
            border-radius: var(--advanced-radius-4xl);
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
                radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 25%),
                radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 25%),
                linear-gradient(135deg, rgba(0, 97, 255, 0.3) 0%, rgba(255, 179, 0, 0.3) 100%);
            z-index: 1;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .cta-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--advanced-font-weight-black);
            font-size: var(--advanced-font-size-6xl);
            margin-bottom: var(--advanced-spacing-5);
            line-height: 1.1;
            background: linear-gradient(to right, #ffffff, #ffd166);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            animation: text-glow 3s infinite alternate;
        }

        .cta-subtitle {
            font-size: var(--advanced-font-size-xl);
            margin-bottom: var(--advanced-spacing-10);
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: var(--advanced-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        .cta-btn {
            background: linear-gradient(135deg, var(--advanced-accent-500) 0%, var(--advanced-accent-700) 100%);
            color: var(--advanced-neutral-900);
            border: none;
            border-radius: var(--advanced-radius-full);
            padding: 1.25rem 3rem;
            font-weight: var(--advanced-font-weight-black);
            font-size: var(--advanced-font-size-lg);
            transition: var(--advanced-transition-all);
            box-shadow: var(--advanced-shadow-lg);
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }

        .cta-btn:hover::before {
            left: 100%;
        }

        .cta-btn:hover {
            background: linear-gradient(135deg, var(--advanced-accent-600) 0%, var(--advanced-accent-800) 100%);
            box-shadow: var(--advanced-shadow-2xl);
            transform: translateY(-5px);
        }

        /* Advanced Premium Footer */
        .footer {
            background: var(--advanced-neutral-900);
            color: var(--advanced-neutral-300);
            padding: var(--advanced-spacing-20) 0 var(--advanced-spacing-10);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--advanced-spacing-10);
            margin-bottom: var(--advanced-spacing-12);
        }

        .footer-title {
            color: white;
            font-weight: var(--advanced-font-weight-extrabold);
            margin-bottom: var(--advanced-spacing-6);
            font-size: var(--advanced-font-size-xl);
            position: relative;
            padding-bottom: var(--advanced-spacing-4);
            font-family: 'Montserrat', sans-serif;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 4px;
            background: var(--advanced-primary-500);
            border-radius: var(--advanced-radius-full);
        }

        .footer-link {
            color: var(--advanced-neutral-400);
            text-decoration: none;
            display: block;
            margin-bottom: var(--advanced-spacing-3);
            transition: var(--advanced-transition-all);
            font-size: var(--advanced-font-size-base);
            font-weight: var(--advanced-font-weight-medium);
        }

        .footer-link:hover {
            color: white;
            transform: translateX(8px);
        }

        .social-links {
            display: flex;
            gap: var(--advanced-spacing-4);
            margin-top: var(--advanced-spacing-5);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: var(--advanced-neutral-800);
            color: white;
            border-radius: var(--advanced-radius-full);
            transition: var(--advanced-transition-all);
            font-size: var(--advanced-font-size-lg);
        }

        .social-link:hover {
            background: var(--advanced-primary-600);
            transform: translateY(-8px) rotate(10deg);
        }

        .copyright {
            text-align: center;
            padding-top: var(--advanced-spacing-10);
            border-top: 1px solid var(--advanced-neutral-800);
            font-size: var(--advanced-font-size-sm);
            color: var(--advanced-neutral-500);
            font-weight: var(--advanced-font-weight-medium);
        }

        /* Advanced Premium Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-title {
                font-size: var(--advanced-font-size-6xl);
            }
            
            .section-title {
                font-size: var(--advanced-font-size-4xl);
            }
            
            .cta-title {
                font-size: var(--advanced-font-size-5xl);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--advanced-font-size-5xl);
            }
            
            .hero-subtitle {
                font-size: var(--advanced-font-size-lg);
            }
            
            .section-title {
                font-size: var(--advanced-font-size-3xl);
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-btn {
                padding: 1.25rem;
                justify-content: center;
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .category-icon {
                width: 70px;
                height: 70px;
                font-size: var(--advanced-font-size-2xl);
            }
            
            .cta-title {
                font-size: var(--advanced-font-size-4xl);
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: var(--advanced-spacing-16) 0;
            }
            
            .hero-title {
                font-size: var(--advanced-font-size-4xl);
            }
            
            .search-container {
                padding: var(--advanced-spacing-4);
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
                font-size: var(--advanced-font-size-lg);
            }
            
            .step-card {
                padding: var(--advanced-spacing-6);
            }
            
            .cta-section {
                padding: var(--advanced-spacing-16) 0;
                margin: var(--advanced-spacing-16) 0;
            }
        }
    </style>
</head>
<body>
    <!-- Advanced Premium Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 2rem;">
                <span class="text-primary">Jobs</span><span style="color: #ffb300;">Mtaani</span>
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

    <!-- Advanced Premium Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Find Trusted Local Services</h1>
            <p class="hero-subtitle">Connect with skilled professionals in your area for all your home, business, and personal needs.</p>
            
            <!-- Advanced Premium Search Section -->
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
                        <i class="fas fa-search me-2"></i> Search Services
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Advanced Premium Categories Section -->
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

    <!-- Advanced Premium Featured Services Section -->
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
                <div class="col-lg-4 col-md-6 mb-5">
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
                            <p class="service-description"><?php echo htmlspecialchars(substr($service_item['description'], 0, 120)) . '...'; ?></p>
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

    <!-- Advanced Premium How It Works Section -->
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

    <!-- Advanced Premium CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-subtitle">Join thousands of satisfied customers who trust JobsMtaani for their service needs.</p>
            <div class="d-flex flex-wrap justify-content-center gap-4">
                <a href="services.php" class="cta-btn">
                    <i class="fas fa-concierge-bell me-2"></i>Browse Services
                </a>
                <a href="register.php?user_type=provider" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-user-tie me-2"></i>Become a Provider
                </a>
            </div>
        </div>
    </section>

    <!-- Advanced Premium Footer -->
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
        // Add advanced animations to category cards on hover
        document.addEventListener('DOMContentLoaded', function() {
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.category-icon');
                    icon.style.boxShadow = '0 15px 20px -5px rgba(0, 97, 255, 0.3), 0 8px 10px -6px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.category-icon');
                    icon.style.boxShadow = 'var(--advanced-shadow)';
                });
            });
            
            // Add glow effect to cards on hover
            const cards = document.querySelectorAll('.card, .service-card, .step-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 25px 50px -12px rgba(0, 97, 255, 0.25), 0 10px 15px -5px rgba(255, 179, 0, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--advanced-shadow-md)';
                });
            });
            
            // Add floating animation to search container on scroll
            window.addEventListener('scroll', function() {
                const searchContainer = document.querySelector('.search-container');
                const scrollPosition = window.scrollY;
                const rotation = scrollPosition * 0.1;
                searchContainer.style.transform = `translateY(${Math.sin(scrollPosition * 0.01) * 10}px) rotate(${rotation % 360}deg)`;
            });
        });
    </script>
</body>
</html>