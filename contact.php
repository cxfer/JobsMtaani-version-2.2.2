<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = "Contact Us";
include 'includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
                <p class="lead">We'd love to hear from you. Reach out to us anytime.</p>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-map-marker-alt text-primary fs-3"></i>
                            </div>
                            <h5 class="card-title">Our Office</h5>
                            <p class="card-text text-muted">123 Business Avenue<br>Nairobi, Kenya<br>P.O. Box 12345-00100</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-phone-alt text-primary fs-3"></i>
                            </div>
                            <h5 class="card-title">Phone Numbers</h5>
                            <p class="card-text text-muted">+254 700 123 456<br>+254 733 987 654<br>Mon-Fri, 8:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-envelope text-primary fs-3"></i>
                            </div>
                            <h5 class="card-title">Email Addresses</h5>
                            <p class="card-text text-muted">support@jobsmtaani.com<br>info@jobsmtaani.com<br>partners@jobsmtaani.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title mb-4">Send Us a Message</h2>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" placeholder="Enter subject">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Enter your message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Frequently Asked Questions</h2>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    How do I book a service?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Simply browse our services, select what you need, and follow the booking process. You'll receive confirmation and details about your service provider.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    How are service providers verified?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    All our providers go through a rigorous verification process including background checks, skill assessments, and reference verification before joining our platform.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We accept M-Pesa, credit/debit cards, and bank transfers. All payments are securely processed through our platform.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    What is your refund policy?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We offer a satisfaction guarantee. If you're not happy with a service, contact us within 48 hours and we'll work to resolve the issue or provide a refund.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/unified_footer.php'; ?>
