<?php
require_once 'config/config.php';

// Process form submission
$message_sent = false;
if ($_POST) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    
    // In a real application, you would send an email here
    // For now, we'll just set a success flag
    $message_sent = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - JobsMtaani Ultimate Premium</title>
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
        .form-control, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--ultimate-neutral-300);
            border-radius: var(--ultimate-radius-lg);
            font-size: var(--ultimate-font-size-base);
            transition: var(--ultimate-transition-all);
            background-color: var(--ultimate-neutral-0);
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--ultimate-primary-500);
            box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.1);
        }

        .form-label {
            font-weight: var(--ultimate-font-weight-medium);
            color: var(--ultimate-neutral-800);
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Ultimate Premium Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--ultimate-primary-900) 0%, var(--ultimate-primary-700) 100%);
            color: white;
            padding: var(--ultimate-spacing-16) 0;
            text-align: center;
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
            z-index: 2;
            position: relative;
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
            margin-bottom: 0;
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: var(--ultimate-font-weight-medium);
            font-family: 'Poppins', sans-serif;
            position: relative;
            z-index: 2;
        }

        /* Ultimate Premium Contact Section */
        .contact-section {
            padding: var(--ultimate-spacing-16) 0;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: var(--ultimate-font-weight-extrabold);
            font-size: var(--ultimate-font-size-3xl);
            margin-bottom: var(--ultimate-spacing-6);
            color: var(--ultimate-neutral-900);
            position: relative;
            padding-bottom: var(--ultimate-spacing-3);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--ultimate-primary-500), var(--ultimate-accent-500));
            border-radius: var(--ultimate-radius-full);
        }

        /* Ultimate Premium Contact Info */
        .contact-info {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            padding: var(--ultimate-spacing-6);
            box-shadow: var(--ultimate-shadow-md);
            border: 1px solid var(--ultimate-neutral-200);
            height: 100%;
            transition: var(--ultimate-transition-all);
            position: relative;
            overflow: hidden;
        }

        .contact-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.03) 0%, rgba(0, 97, 255, 0.01) 100%);
            z-index: 0;
        }

        .contact-info:hover {
            transform: translateY(-5px);
            box-shadow: var(--ultimate-shadow-xl);
            border-color: var(--ultimate-primary-300);
        }

        .contact-item {
            display: flex;
            margin-bottom: var(--ultimate-spacing-5);
            transition: var(--ultimate-transition-all);
            position: relative;
            z-index: 1;
        }

        .contact-item:hover {
            transform: translateX(5px);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--ultimate-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--ultimate-spacing-4);
            font-size: var(--ultimate-font-size-lg);
            background: linear-gradient(135deg, var(--ultimate-primary-50) 0%, var(--ultimate-primary-100) 100%);
            color: var(--ultimate-primary-700);
            transition: var(--ultimate-transition-all);
            flex-shrink: 0;
            box-shadow: var(--ultimate-shadow);
        }

        .contact-item:hover .contact-icon {
            background: linear-gradient(135deg, var(--ultimate-primary-100) 0%, var(--ultimate-primary-200) 100%);
            transform: scale(1.1);
            box-shadow: var(--ultimate-shadow-lg);
        }

        .contact-details h4 {
            color: var(--ultimate-neutral-900);
            margin-bottom: var(--ultimate-spacing-1);
            font-weight: var(--ultimate-font-weight-bold);
        }

        .contact-details p {
            color: var(--ultimate-neutral-600);
            margin-bottom: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Ultimate Premium Map */
        .map-container {
            height: 300px;
            border-radius: var(--ultimate-radius-xl);
            overflow: hidden;
            box-shadow: var(--ultimate-shadow-md);
            margin-bottom: var(--ultimate-spacing-6);
            position: relative;
        }

        .map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 97, 255, 0.1), rgba(14, 165, 233, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: var(--ultimate-spacing-4);
        }

        /* Ultimate Premium Alert */
        .alert {
            border-radius: var(--ultimate-radius-lg);
        }

        /* Contact Form Styles */
        .contact-form {
            background: var(--ultimate-neutral-0);
            border-radius: var(--ultimate-radius-2xl);
            padding: var(--ultimate-spacing-6);
            box-shadow: var(--ultimate-shadow-md);
            border: 1px solid var(--ultimate-neutral-200);
        }

        .form-group {
            margin-bottom: var(--ultimate-spacing-4);
        }

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

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* FAQ Section */
        .faq-section {
            padding: var(--ultimate-spacing-16) 0;
            background-color: var(--ultimate-neutral-50);
        }

        .faq-card {
            margin-bottom: var(--ultimate-spacing-4);
            border: 1px solid var(--ultimate-neutral-200);
            border-radius: var(--ultimate-radius-xl);
            overflow: hidden;
            transition: var(--ultimate-transition-all);
        }

        .faq-card:hover {
            border-color: var(--ultimate-primary-300);
        }

        .faq-question {
            padding: var(--ultimate-spacing-4) var(--ultimate-spacing-5);
            background: var(--ultimate-neutral-0);
            font-weight: var(--ultimate-font-weight-bold);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--ultimate-transition-all);
        }

        .faq-question:hover {
            background: var(--ultimate-primary-50);
        }

        .faq-answer {
            padding: 0 var(--ultimate-spacing-5) var(--ultimate-spacing-4);
            background: var(--ultimate-neutral-0);
            border-top: 1px solid var(--ultimate-neutral-200);
            display: none;
        }

        .faq-answer.show {
            display: block;
        }

        .faq-icon {
            transition: var(--ultimate-transition-transform);
        }

        .faq-card.active .faq-icon {
            transform: rotate(180deg);
        }

        /* Ultimate Premium Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--ultimate-font-size-4xl);
            }
            
            .hero-subtitle {
                font-size: var(--ultimate-font-size-lg);
            }
            
            .section-title {
                font-size: var(--ultimate-font-size-2xl);
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: var(--ultimate-spacing-12) 0;
            }
            
            .hero-title {
                font-size: var(--ultimate-font-size-3xl);
            }
            
            .contact-item {
                flex-direction: column;
                text-align: center;
            }
            
            .contact-icon {
                margin-right: 0;
                margin-bottom: var(--ultimate-spacing-3);
            }
        }
    </style>
</head>
<body>
    <!-- Ultimate Premium Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Contact Us</h1>
            <p class="hero-subtitle">We'd love to hear from you. Get in touch with our team.</p>
        </div>
    </section>

    <!-- Ultimate Premium Contact Section -->
    <section class="contact-section">
        <div class="container">
            <?php if ($message_sent): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you soon.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="section-title">Get in Touch</h2>
                    
                    <div class="contact-form">
                        <form method="POST">
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <h2 class="section-title">Contact Information</h2>
                    
                    <div class="contact-info mb-5">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Our Location</h4>
                                <p>Nairobi, Kenya</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone Number</h4>
                                <p>+254 700 000 000</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email Address</h4>
                                <p>info@jobsmtaani.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Working Hours</h4>
                                <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                                <p>Saturday: 9:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-container">
                        <div class="map-overlay">
                            <i class="fas fa-map-marked-alt fa-3x mb-3 text-primary"></i>
                            <h4 class="text-primary">Our Location</h4>
                            <p class="text-muted">Nairobi, Kenya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title d-inline-block">Frequently Asked Questions</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-card">
                        <div class="faq-question">
                            How do I book a service?
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>You can book a service by searching for it on our platform, selecting a provider, and following the booking process. You'll receive confirmation via email and SMS.</p>
                        </div>
                    </div>
                    
                    <div class="faq-card">
                        <div class="faq-question">
                            How do I become a service provider?
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Register as a provider on our platform, complete your profile, and submit the required documentation for verification. Once approved, you can start offering services.</p>
                        </div>
                    </div>
                    
                    <div class="faq-card">
                        <div class="faq-question">
                            What payment methods do you accept?
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept M-Pesa, bank transfers, and major credit cards. All payments are securely processed through our payment partners.</p>
                        </div>
                    </div>
                    
                    <div class="faq-card">
                        <div class="faq-question">
                            How do you ensure service quality?
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>All our providers are verified and rated by customers. We also have a quality assurance team that monitors service delivery and addresses any issues promptly.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FAQ accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const faqCard = this.parentElement;
                    const answer = faqCard.querySelector('.faq-answer');
                    const icon = this.querySelector('.faq-icon');
                    
                    // Toggle active class
                    faqCard.classList.toggle('active');
                    
                    // Toggle answer visibility
                    if (answer.classList.contains('show')) {
                        answer.classList.remove('show');
                    } else {
                        answer.classList.add('show');
                    }
                });
            });
            
            // Add animation to contact items on hover
            const contactItems = document.querySelectorAll('.contact-item');
            contactItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.contact-icon');
                    icon.style.boxShadow = '0 10px 15px -3px rgba(0, 97, 255, 0.2), 0 4px 6px -4px rgba(0, 97, 255, 0.2)';
                });
                
                item.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.contact-icon');
                    icon.style.boxShadow = 'var(--ultimate-shadow)';
                });
            });
            
            // Add glow effect to cards on hover
            const cards = document.querySelectorAll('.card, .contact-info');
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