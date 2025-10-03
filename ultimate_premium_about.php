<?php
require_once 'config/config.php';
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - JobsMtaani Ultimate Premium</title>
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

        /* Ultimate Premium Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--ultimate-primary-900) 0%, var(--ultimate-primary-700) 100%);
            color: white;
            padding: var(--ultimate-spacing-16) 0;
            position: relative;
            overflow: hidden;
            margin-bottom: var(--ultimate-spacing-12);
        }

        .page-header::before {
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

        .page-header-content {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }

        .page-title {
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

        .page-subtitle {
            font-size: var(--ultimate-font-size-xl);
            margin-bottom: 0;
            opacity: 0.9;
            max-width: 700px;
            font-weight: var(--ultimate-font-weight-medium);
            font-family: 'Poppins', sans-serif;
            margin-left: auto;
            margin-right: auto;
        }

        /* Ultimate Premium Mission Section */
        .mission-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-0);
        }

        .mission-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .mission-icon {
            width: 100px;
            height: 100px;
            border-radius: var(--ultimate-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--ultimate-spacing-6);
            font-size: var(--ultimate-font-size-4xl);
            background: linear-gradient(135deg, var(--ultimate-primary-50) 0%, var(--ultimate-primary-100) 100%);
            color: var(--ultimate-primary-700);
            box-shadow: var(--ultimate-shadow-lg);
            animation: float 6s ease-in-out infinite;
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

        /* Ultimate Premium Stats Section */
        .stats-section {
            padding: var(--ultimate-spacing-16) 0;
            background: linear-gradient(135deg, var(--ultimate-primary-800) 0%, var(--ultimate-primary-900) 100%);
            color: white;
            position: relative;
            overflow: hidden;
            margin: var(--ultimate-spacing-16) 0;
            border-radius: var(--ultimate-radius-3xl);
        }

        .stats-section::before {
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

        .stats-content {
            position: relative;
            z-index: 2;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--ultimate-spacing-8);
            margin-top: var(--ultimate-spacing-8);
        }

        .stat-card {
            text-align: center;
            padding: var(--ultimate-spacing-6);
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--ultimate-radius-2xl);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--ultimate-transition-all);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: var(--ultimate-shadow-lg);
        }

        .stat-number {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--ultimate-font-weight-black);
            font-size: var(--ultimate-font-size-5xl);
            margin-bottom: var(--ultimate-spacing-2);
            background: linear-gradient(to right, #ffffff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: counter 3s ease-in-out infinite alternate;
        }

        @keyframes counter {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .stat-label {
            font-size: var(--ultimate-font-size-lg);
            font-weight: var(--ultimate-font-weight-semibold);
            opacity: 0.9;
        }

        /* Ultimate Premium Features Section */
        .features-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-50);
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

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--ultimate-spacing-8);
            margin-top: var(--ultimate-spacing-8);
        }

        .feature-card {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            padding: var(--ultimate-spacing-8);
            text-align: center;
            transition: var(--ultimate-transition-all);
            border: 1px solid var(--ultimate-neutral-200);
            box-shadow: var(--ultimate-shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--ultimate-shadow-lg);
            border-color: var(--ultimate-primary-300);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--ultimate-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--ultimate-spacing-6);
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
                box-shadow: 0 0 0 15px rgba(0, 97, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 97, 255, 0);
            }
        }

        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--ultimate-primary-100) 0%, var(--ultimate-primary-200) 100%);
            transform: scale(1.1);
            box-shadow: var(--ultimate-shadow-lg);
        }

        .feature-title {
            font-weight: var(--ultimate-font-weight-bold);
            color: var(--ultimate-neutral-800);
            margin-bottom: var(--ultimate-spacing-3);
            font-size: var(--ultimate-font-size-xl);
        }

        .feature-description {
            color: var(--ultimate-neutral-600);
            font-size: var(--ultimate-font-size-base);
            line-height: 1.6;
        }

        /* Ultimate Premium Team Section */
        .team-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-0);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--ultimate-spacing-8);
            margin-top: var(--ultimate-spacing-8);
        }

        .team-member {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            overflow: hidden;
            box-shadow: var(--ultimate-shadow-md);
            border: 1px solid var(--ultimate-neutral-200);
            transition: var(--ultimate-transition-all);
            text-align: center;
            position: relative;
        }

        .team-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .team-member:hover {
            transform: translateY(-12px);
            box-shadow: var(--ultimate-shadow-xl);
            border-color: var(--ultimate-primary-300);
        }

        .member-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .member-info {
            padding: var(--ultimate-spacing-6);
        }

        .member-name {
            font-weight: var(--ultimate-font-weight-bold);
            color: var(--ultimate-neutral-900);
            margin-bottom: var(--ultimate-spacing-2);
            font-size: var(--ultimate-font-size-lg);
        }

        .member-role {
            color: var(--ultimate-primary-600);
            font-weight: var(--ultimate-font-weight-semibold);
            margin-bottom: var(--ultimate-spacing-4);
            font-size: var(--ultimate-font-size-base);
        }

        .member-bio {
            color: var(--ultimate-neutral-600);
            font-size: var(--ultimate-font-size-sm);
            margin-bottom: var(--ultimate-spacing-4);
            line-height: 1.6;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: var(--ultimate-spacing-3);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: var(--ultimate-neutral-100);
            color: var(--ultimate-neutral-700);
            border-radius: var(--ultimate-radius-full);
            transition: var(--ultimate-transition-all);
            font-size: var(--ultimate-font-size-sm);
        }

        .social-link:hover {
            background: var(--ultimate-primary-600);
            color: white;
            transform: translateY(-5px);
            box-shadow: var(--ultimate-shadow);
        }

        /* Ultimate Premium CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--ultimate-accent-700) 0%, var(--ultimate-accent-900) 100%);
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
            .page-title {
                font-size: var(--ultimate-font-size-5xl);
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: var(--ultimate-font-size-4xl);
            }
            
            .page-subtitle {
                font-size: var(--ultimate-font-size-lg);
            }
            
            .stat-number {
                font-size: var(--ultimate-font-size-4xl);
            }
            
            .cta-title {
                font-size: var(--ultimate-font-size-4xl);
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: var(--ultimate-spacing-12) 0;
            }
            
            .page-title {
                font-size: var(--ultimate-font-size-3xl);
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-number {
                font-size: var(--ultimate-font-size-3xl);
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
    <!-- Ultimate Premium Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">About JobsMtaani</h1>
            <p class="page-subtitle">Connecting communities through trusted local services</p>
        </div>
    </section>

    <!-- Ultimate Premium Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-content">
                <div class="mission-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2 class="section-title">Our Mission</h2>
                <p class="section-subtitle">At JobsMtaani, we're on a mission to revolutionize how communities connect with local service providers. We believe that quality services should be accessible to everyone, and skilled professionals should be fairly compensated for their expertise.</p>
                <p class="mt-4">Our platform bridges the gap between customers seeking reliable services and talented providers, creating a thriving ecosystem that benefits everyone involved. We're committed to fostering trust, transparency, and excellence in every interaction.</p>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium Stats Section -->
    <section class="stats-section">
        <div class="stats-content">
            <div class="container">
                <div class="text-center">
                    <h2 class="section-title text-white">By The Numbers</h2>
                    <p class="section-subtitle text-white-50">Our growth and impact in numbers</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" data-count="5000">0</div>
                        <div class="stat-label">Service Providers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="25000">0</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="100000">0</div>
                        <div class="stat-label">Services Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="4.8">0</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why Choose JobsMtaani</h2>
                <p class="section-subtitle">We've built a platform that prioritizes trust, quality, and convenience</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Verified Professionals</h3>
                    <p class="feature-description">All our service providers go through a rigorous verification process to ensure quality and reliability.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="feature-title">Quality Assurance</h3>
                    <p class="feature-description">We maintain high standards through customer reviews and continuous provider evaluation.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="feature-title">Secure Payments</h3>
                    <p class="feature-description">Our secure payment system protects both customers and providers throughout the service process.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Our dedicated support team is always ready to assist with any questions or concerns.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Fast Matching</h3>
                    <p class="feature-description">Our intelligent matching system connects you with the right provider in minutes.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <h3 class="feature-title">Nationwide Reach</h3>
                    <p class="feature-description">Access quality services from trusted providers across Kenya, from Nairobi to Mombasa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Leadership Team</h2>
                <p class="section-subtitle">The passionate individuals driving JobsMtaani forward</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="member-image">
                    <div class="member-info">
                        <h3 class="member-name">James Mwangi</h3>
                        <p class="member-role">CEO & Founder</p>
                        <p class="member-bio">With over 15 years in tech and entrepreneurship, James leads our vision to transform local service delivery across Kenya.</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="member-image">
                    <div class="member-info">
                        <h3 class="member-name">Sarah Kimani</h3>
                        <p class="member-role">Chief Operations Officer</p>
                        <p class="member-bio">Sarah ensures seamless operations and exceptional customer experience across all our platforms and services.</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="member-image">
                    <div class="member-info">
                        <h3 class="member-name">David Ochieng</h3>
                        <p class="member-role">Chief Technology Officer</p>
                        <p class="member-bio">David leads our technology innovation, building cutting-edge solutions that power our platform.</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="member-image">
                    <div class="member-info">
                        <h3 class="member-name">Grace Njeri</h3>
                        <p class="member-role">Chief Marketing Officer</p>
                        <p class="member-bio">Grace drives our brand strategy and community engagement, connecting us with customers and providers.</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ultimate Premium CTA Section -->
    <section class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h2 class="cta-title">Join Our Community</h2>
                <p class="cta-subtitle">Whether you're looking for services or want to provide them, JobsMtaani is the platform for you.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-5">
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Sign Up as Customer
                    </a>
                    <a href="register.php?user_type=provider" class="btn btn-secondary">
                        <i class="fas fa-hands-helping me-2"></i>
                        Join as Provider
                    </a>
                </div>
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
        // Animated counters
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stat-number');
            const speed = 200; // The lower the faster
            
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-count');
                    const count = +counter.innerText;
                    
                    // Calculate increment
                    const increment = target / speed;
                    
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(updateCount, 10);
                    } else {
                        counter.innerText = target;
                    }
                };
                
                // Start counter when element is in viewport
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCount();
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.5
                });
                
                observer.observe(counter);
            });
            
            // Add glow effect to feature cards on hover
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 25px 50px -12px rgba(0, 97, 255, 0.25), 0 10px 15px -6px rgba(0, 97, 255, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--ultimate-shadow-md)';
                });
            });
            
            // Add advanced animations to team members
            const teamMembers = document.querySelectorAll('.team-member');
            teamMembers.forEach(member => {
                member.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-15px)';
                });
                
                member.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>