<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = "About Us";
include 'includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-4">About JobsMtaani</h1>
                <p class="lead">Connecting communities through trusted local services</p>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title mb-4">Our Mission</h2>
                    <p class="card-text">At JobsMtaani, we're on a mission to revolutionize how communities connect with local service providers. We believe that quality services should be accessible to everyone, and skilled professionals should be fairly compensated for their expertise.</p>
                    <p class="card-text">Our platform bridges the gap between customers seeking reliable services and talented providers, creating a thriving ecosystem that benefits everyone involved. We're committed to fostering trust, transparency, and excellence in every interaction.</p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-users text-primary fs-2"></i>
                            </div>
                            <h3 class="card-title">5000+</h3>
                            <p class="card-text text-muted">Service Providers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-smile text-primary fs-2"></i>
                            </div>
                            <h3 class="card-title">25000+</h3>
                            <p class="card-text text-muted">Happy Customers</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title mb-4">Why Choose JobsMtaani</h2>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fas fa-shield-alt text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Verified Professionals</h5>
                                    <p class="text-muted mb-0">All our service providers go through a rigorous verification process to ensure quality and reliability.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fas fa-star text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Quality Assurance</h5>
                                    <p class="text-muted mb-0">We maintain high standards through customer reviews and continuous provider evaluation.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fas fa-lock text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Secure Payments</h5>
                                    <p class="text-muted mb-0">Our secure payment system protects both customers and providers throughout the service process.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fas fa-headset text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">24/7 Support</h5>
                                    <p class="text-muted mb-0">Our dedicated support team is always ready to assist with any questions or concerns.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Our Team</h2>
                    <p class="card-text">We're a passionate team of professionals dedicated to transforming the local service industry in Kenya. Our diverse backgrounds in technology, business, and customer service enable us to create innovative solutions that benefit both customers and service providers.</p>
                    <p class="card-text">With years of experience in building digital platforms and understanding the unique needs of local communities, we're committed to creating a platform that truly serves everyone involved.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/unified_footer.php'; ?>