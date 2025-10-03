<?php
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobsMtaani - Ultimate Premium Local Services Marketplace</title>
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
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 97, 255, 0.03) 0%, transparent 15%),
                radial-gradient(circle at 90% 80%, rgba(14, 165, 233, 0.03) 0%, transparent 15%);
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
            font-size: var(--ultimate-font-size-5xl);
            font-weight: var(--ultimate-font-weight-black);
        }

        h2 {
            font-size: var(--ultimate-font-size-4xl);
            font-weight: var(--ultimate-font-weight-extrabold);
        }

        h3 {
            font-size: var(--ultimate-font-size-3xl);
            font-weight: var(--ultimate-font-weight-bold);
        }

        h4 {
            font-size: var(--ultimate-font-size-2xl);
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

        .btn-outline {
            background: transparent;
            color: var(--ultimate-primary-600);
            border: 2px solid var(--ultimate-primary-600);
        }

        .btn-outline:hover {
            background: var(--ultimate-primary-50);
            color: var(--ultimate-primary-800);
            border-color: var(--ultimate-primary-800);
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

        /* Ultimate Premium Forms */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--ultimate-neutral-300);
            border-radius: var(--ultimate-radius-lg);
            font-size: var(--ultimate-font-size-base);
            transition: var(--ultimate-transition-all);
            background-color: var(--ultimate-neutral-0);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--ultimate-primary-500);
            box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.1);
        }

        /* Ultimate Premium Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--ultimate-primary-900) 0%, var(--ultimate-primary-700) 100%);
            color: white;
            padding: var(--ultimate-spacing-16) 0;
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
            font-weight: var(--ultimate-font-weight-black);
            font-size: var(--ultimate-font-size-6xl);
            margin-bottom: var(--ultimate-spacing-4);
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
            font-size: var(--ultimate-font-size-xl);
            margin-bottom: var(--ultimate-spacing-8);
            opacity: 0.9;
            max-width: 700px;
            font-weight: var(--ultimate-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        /* Ultimate Premium Search Section */
        .search-container {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-3xl);
            box-shadow: var(--ultimate-shadow-2xl);
            padding: var(--ultimate-spacing-6);
            margin-top: var(--ultimate-spacing-6);
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
            background: linear-gradient(90deg, var(--ultimate-primary-500), var(--ultimate-accent-500));
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
            gap: var(--ultimate-spacing-3);
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
            border: 2px solid var(--ultimate-neutral-200);
            border-radius: var(--ultimate-radius-full);
            font-size: var(--ultimate-font-size-base);
            transition: var(--ultimate-transition-all);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--ultimate-primary-500);
            box-shadow: 0 0 0 4px rgba(0, 97, 255, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ultimate-neutral-400);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--ultimate-primary-600) 0%, var(--ultimate-primary-800) 100%);
            color: white;
            border: none;
            border-radius: var(--ultimate-radius-full);
            padding: 0 2rem;
            font-weight: var(--ultimate-font-weight-bold);
            font-size: var(--ultimate-font-size-base);
            transition: var(--ultimate-transition-all);
            box-shadow: var(--ultimate-shadow-md);
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
            background: linear-gradient(135deg, var(--ultimate-primary-700) 0%, var(--ultimate-primary-900) 100%);
            box-shadow: var(--ultimate-shadow-lg);
            transform: translateY(-2px);
        }

        /* Ultimate Premium Categories Section */
        .categories-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-0);
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--ultimate-spacing-12);
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--ultimate-font-weight-extrabold);
            font-size: var(--ultimate-font-size-4xl);
            margin-bottom: var(--ultimate-spacing-4);
            color: var(--ultimate-neutral-900);
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
            background: linear-gradient(90deg, var(--ultimate-primary-500), var(--ultimate-accent-500));
            border-radius: var(--ultimate-radius-full);
        }

        .section-subtitle {
            font-size: var(--ultimate-font-size-lg);
            color: var(--ultimate-neutral-600);
            max-width: 700px;
            margin: 1rem auto 0;
            font-family: 'Poppins', sans-serif;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: var(--ultimate-spacing-6);
            margin-top: var(--ultimate-spacing-8);
        }

        .category-card {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            padding: var(--ultimate-spacing-6) var(--ultimate-spacing-4);
            text-align: center;
            transition: var(--ultimate-transition-all);
            border: 1px solid var(--ultimate-neutral-200);
            box-shadow: var(--ultimate-shadow-sm);
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
            box-shadow: var(--ultimate-shadow-lg);
            border-color: var(--ultimate-primary-300);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--ultimate-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--ultimate-spacing-4);
            font-size: var(--ultimate-font-size-2xl);
            background: linear-gradient(135deg, var(--ultimate-primary-50) 0%, var(--ultimate-primary-100) 100%);
            color: var(--ultimate-primary-700);
            transition: var(--ultimate-transition-all);
            position: relative;
            z-index: 1;
            box-shadow: var(--ultimate-shadow);
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
            background: linear-gradient(135deg, var(--ultimate-primary-100) 0%, var(--ultimate-primary-200) 100%);
            transform: scale(1.1);
            box-shadow: var(--ultimate-shadow-lg);
        }

        .category-name {
            font-weight: var(--ultimate-font-weight-bold);
            color: var(--ultimate-neutral-800);
            margin-bottom: 0;
            font-size: var(--ultimate-font-size-base);
        }

        /* Ultimate Premium Services Section */
        .services-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-50);
        }

        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--ultimate-spacing-8);
        }

        .view-all-link {
            color: var(--ultimate-primary-600);
            text-decoration: none;
            font-weight: var(--ultimate-font-weight-bold);
            font-size: var(--ultimate-font-size-base);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--ultimate-transition-all);
        }

        .view-all-link:hover {
            color: var(--ultimate-primary-800);
            transform: translateX(5px);
        }

        /* Ultimate Premium Service Card */
        .service-card {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            overflow: hidden;
            box-shadow: var(--ultimate-shadow-md);
            border: 1px solid var(--ultimate-neutral-200);
            transition: var(--ultimate-transition-all);
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
            box-shadow: var(--ultimate-shadow-xl);
            border-color: var(--ultimate-primary-300);
        }

        .service-image-container {
            position: relative;
            overflow: hidden;
        }

        .service-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--ultimate-transition-transform);
        }

        .service-card:hover .service-image {
            transform: scale(1.05);
        }

        .service-badge {
            position: absolute;
            top: var(--ultimate-spacing-4);
            left: var(--ultimate-spacing-4);
            background: linear-gradient(135deg, var(--ultimate-accent-500) 0%, var(--ultimate-accent-700) 100%);
            color: var(--ultimate-neutral-900);
            padding: 0.25rem 1rem;
            border-radius: var(--ultimate-radius-full);
            font-size: var(--ultimate-font-size-xs);
            font-weight: var(--ultimate-font-weight-bold);
            z-index: 2;
            box-shadow: var(--ultimate-shadow);
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
            padding: var(--ultimate-spacing-5);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .service-provider {
            display: flex;
            align-items: center;
            margin-bottom: var(--ultimate-spacing-3);
        }

        .provider-avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--ultimate-radius-full);
            margin-right: var(--ultimate-spacing-3);
            object-fit: cover;
            border: 2px solid var(--ultimate-primary-200);
        }

        .provider-name {
            font-size: var(--ultimate-font-size-sm);
            color: var(--ultimate-neutral-600);
            margin-bottom: 0;
            font-weight: var(--ultimate-font-weight-medium);
        }

        .service-title {
            font-size: var(--ultimate-font-size-lg);
            font-weight: var(--ultimate-font-weight-bold);
            margin-bottom: var(--ultimate-spacing-3);
            color: var(--ultimate-neutral-900);
            line-height: 1.3;
        }

        .service-description {
            color: var(--ultimate-neutral-600);
            font-size: var(--ultimate-font-size-sm);
            margin-bottom: var(--ultimate-spacing-4);
            flex-grow: 1;
            line-height: 1.5;
        }

        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: var(--ultimate-spacing-4);
            padding-top: var(--ultimate-spacing-4);
            border-top: 1px solid var(--ultimate-neutral-200);
        }

        .service-price {
            font-size: var(--ultimate-font-size-xl);
            font-weight: var(--ultimate-font-weight-extrabold);
            color: var(--ultimate-primary-700);
        }

        .service-rating {
            display: flex;
            align-items: center;
            color: var(--ultimate-warning);
            font-weight: var(--ultimate-font-weight-semibold);
        }

        /* Ultimate Premium CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--ultimate-primary-800) 0%, var(--ultimate-primary-900) 100%);
            color: white;
            padding: var(--ultimate-spacing-16) 0;
            text-align: center;
            margin: var(--ultimate-spacing-16) 0;
            border-radius: var(--ultimate-radius-3xl);
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
            font-weight: var(--ultimate-font-weight-black);
            font-size: var(--ultimate-font-size-5xl);
            margin-bottom: var(--ultimate-spacing-4);
            line-height: 1.1;
            background: linear-gradient(to right, #ffffff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            animation: text-glow 3s infinite alternate;
        }

        .cta-subtitle {
            font-size: var(--ultimate-font-size-xl);
            margin-bottom: var(--ultimate-spacing-8);
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: var(--ultimate-font-weight-medium);
            font-family: 'Poppins', sans-serif;
        }

        .cta-btn {
            background: linear-gradient(135deg, var(--ultimate-accent-500) 0%, var(--ultimate-accent-700) 100%);
            color: var(--ultimate-neutral-900);
            border: none;
            border-radius: var(--ultimate-radius-full);
            padding: 1rem 2.5rem;
            font-weight: var(--ultimate-font-weight-extrabold);
            font-size: var(--ultimate-font-size-lg);
            transition: var(--ultimate-transition-all);
            box-shadow: var(--ultimate-shadow-lg);
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
            background: linear-gradient(135deg, var(--ultimate-accent-600) 0%, var(--ultimate-accent-800) 100%);
            box-shadow: var(--ultimate-shadow-2xl);
            transform: translateY(-3px);
        }

        /* Ultimate Premium Footer */
        .footer {
            background: var(--ultimate-neutral-900);
            color: var(--ultimate-neutral-300);
            padding: var(--ultimate-spacing-16) 0 var(--ultimate-spacing-8);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--ultimate-spacing-8);
            margin-bottom: var(--ultimate-spacing-12);
        }

        .footer-title {
            color: white;
            font-weight: var(--ultimate-font-weight-bold);
            margin-bottom: var(--ultimate-spacing-5);
            font-size: var(--ultimate-font-size-lg);
            position: relative;
            padding-bottom: var(--ultimate-spacing-3);
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--ultimate-primary-500);
            border-radius: var(--ultimate-radius-full);
        }

        .footer-link {
            color: var(--ultimate-neutral-400);
            text-decoration: none;
            display: block;
            margin-bottom: var(--ultimate-spacing-3);
            transition: var(--ultimate-transition-all);
            font-size: var(--ultimate-font-size-base);
        }

        .footer-link:hover {
            color: white;
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: var(--ultimate-spacing-3);
            margin-top: var(--ultimate-spacing-4);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: var(--ultimate-neutral-800);
            color: white;
            border-radius: var(--ultimate-radius-full);
            transition: var(--ultimate-transition-all);
        }

        .social-link:hover {
            background: var(--ultimate-primary-600);
            transform: translateY(-5px);
            box-shadow: var(--ultimate-shadow);
        }

        .copyright {
            border-top: 1px solid var(--ultimate-neutral-800);
            padding-top: var(--ultimate-spacing-8);
            margin-top: var(--ultimate-spacing-8);
            text-align: center;
            font-size: var(--ultimate-font-size-sm);
            color: var(--ultimate-neutral-500);
        }

        /* Ultimate Premium Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-title {
                font-size: var(--ultimate-font-size-5xl);
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--ultimate-font-size-4xl);
            }
            
            .hero-subtitle {
                font-size: var(--ultimate-font-size-lg);
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-btn {
                padding: 1rem;
                justify-content: center;
            }
            
            .section-header-flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .view-all-link {
                align-self: flex-end;
            }
            
            .cta-title {
                font-size: var(--ultimate-font-size-4xl);
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: var(--ultimate-spacing-12) 0;
            }
            
            .hero-title {
                font-size: var(--ultimate-font-size-3xl);
            }
            
            .section-title {
                font-size: var(--ultimate-font-size-3xl);
            }
            
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .cta-title {
                font-size: var(--ultimate-font-size-3xl);
            }
            
            .cta-subtitle {
                font-size: var(--ultimate-font-size-base);
            }
        }
        
        /* Advanced animations */
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .rotating {
            animation: rotate 10s linear infinite;
        }
        
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }
        
        .shimmer {
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }
    </style>
</head>
<body>
    <!-- Ultimate Premium Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Find Trusted Local Services</h1>
            <p class="hero-subtitle">Connect with skilled professionals in your area for all your home, business, and personal needs.</p>
            
            <!-- Ultimate Premium Search Container -->
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
                        <i class="fas fa-search"></i>
                        <span>Find Services</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium Categories Section -->
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
        </div>
    </section>

    <!-- Ultimate Premium Featured Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-header-flex">
                <div>
                    <h2 class="section-title">Featured Services</h2>
                    <p class="section-subtitle">Handpicked services from our top-rated providers</p>
                </div>
                <a href="services.php" class="view-all-link">
                    View All Services 
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="row">
                <?php foreach ($featured_services as $service_item): ?>
                <div class="col-md-6 col-lg-4 mb-5">
                    <div class="service-card">
                        <?php if ($service_item['featured']): ?>
                        <span class="service-badge">FEATURED</span>
                        <?php endif; ?>
                        <div class="service-image-container">
                            <img src="<?php echo htmlspecialchars($service_item['image']); ?>" alt="<?php echo htmlspecialchars($service_item['title']); ?>" class="service-image">
                        </div>
                        <div class="service-body">
                            <div class="service-provider">
                                <img src="<?php echo !empty($service_item['profile_image']) ? $service_item['profile_image'] : '/public/placeholder-user.jpg'; ?>" alt="Provider" class="provider-avatar">
                                <p class="provider-name"><?php echo htmlspecialchars($service_item['first_name'] . ' ' . $service_item['last_name']); ?></p>
                            </div>
                            <h3 class="service-title"><?php echo htmlspecialchars($service_item['title']); ?></h3>
                            <p class="service-description"><?php echo htmlspecialchars(substr($service_item['description'], 0, 100)) . '...'; ?></p>
                            <div class="service-meta">
                                <span class="service-price">KES <?php echo number_format($service_item['price']); ?></span>
                                <div class="service-rating">
                                    <i class="fas fa-star me-1"></i>
                                    <span><?php echo $service_item['avg_rating'] ? number_format($service_item['avg_rating'], 1) : 'New'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium CTA Section -->
    <section class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2 class="cta-title">Become a Service Provider</h2>
                <p class="cta-subtitle">Join thousands of professionals earning money by offering their services on JobsMtaani.</p>
                <a href="register.php?user_type=provider" class="cta-btn">
                    <i class="fas fa-user-plus"></i>
                    Join as Provider
                </a>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3 class="footer-title">JobsMtaani</h3>
                    <p>Connecting customers with trusted local service providers across Kenya.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="footer-title">For Customers</h4>
                    <a href="services.php" class="footer-link">Browse Services</a>
                    <a href="login.php" class="footer-link">Login</a>
                    <a href="register.php" class="footer-link">Register</a>
                </div>
                
                <div>
                    <h4 class="footer-title">For Providers</h4>
                    <a href="register.php?user_type=provider" class="footer-link">Join as Provider</a>
                    <a href="login.php" class="footer-link">Provider Login</a>
                    <a href="#" class="footer-link">Provider Resources</a>
                </div>
                
                <div>
                    <h4 class="footer-title">Company</h4>
                    <a href="about.php" class="footer-link">About Us</a>
                    <a href="contact.php" class="footer-link">Contact</a>
                    <a href="#" class="footer-link">Careers</a>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 JobsMtaani. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add advanced animations to category cards
        document.addEventListener('DOMContentLoaded', function() {
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach((card, index) => {
                // Add slight delay to each card for staggered animation
                card.style.animation = `float 6s ease-in-out infinite ${index * 0.3}s`;
            });
            
            // Add glow effect to featured services on hover
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 25px 50px -12px rgba(0, 97, 255, 0.25), 0 10px 15px -6px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--ultimate-shadow-md)';
                });
            });
            
            // Add advanced animations to search container
            const searchContainer = document.querySelector('.search-container');
            setInterval(() => {
                searchContainer.style.boxShadow = '0 25px 50px -12px rgba(0, 97, 255, 0.25)';
                setTimeout(() => {
                    searchContainer.style.boxShadow = 'var(--ultimate-shadow-2xl)';
                }, 500);
            }, 3000);
        });
    </script>
</body>
</html>