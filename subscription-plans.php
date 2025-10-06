<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Service.php';

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);

// Get premium categories
$stmt = $db->prepare("SELECT * FROM service_categories WHERE is_premium = 1 AND is_active = 1 ORDER BY sort_order");
$stmt->execute();
$premiumCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Subscription Plans";
include 'includes/unified_header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Premium Subscription Plans</h1>
        <p class="lead text-muted">Unlock exclusive access to premium services and features</p>
    </div>
    
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                        <h2 class="card-title">Premium Membership</h2>
                        <p class="card-text text-muted">Get unlimited access to exclusive services and premium features</p>
                    </div>
                    
                    <div class="pricing-card text-center mb-4">
                        <div class="price-display">
                            <span class="display-3 fw-bold">KES 499</span>
                            <span class="text-muted">/month</span>
                        </div>
                        <p class="text-muted">Billed monthly</p>
                    </div>
                    
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Access to all premium service categories</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Priority customer support</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Exclusive discounts and offers</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Early access to new features</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Premium service provider matching</li>
                    </ul>
                    
                    <div class="d-grid">
                        <button class="btn btn-warning btn-lg" id="subscribeBtn">
                            <i class="fas fa-crown me-2"></i>Subscribe Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mb-5">
        <h2 class="mb-4">Premium Service Categories</h2>
        <p class="text-muted">As a premium member, you'll get access to these exclusive categories</p>
    </div>
    
    <div class="row">
        <?php foreach ($premiumCategories as $category): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-warning border-2">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="<?php echo htmlspecialchars($category['icon']); ?> text-warning fs-2"></i>
                    </div>
                    <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                    <p class="card-text text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
        <h3 class="mb-4">Frequently Asked Questions</h3>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                What is included in the premium subscription?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                The premium subscription gives you unlimited access to all premium service categories, priority customer support, exclusive discounts, and early access to new features.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Can I cancel my subscription anytime?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, you can cancel your subscription at any time. You'll continue to have access to premium features until the end of your billing period.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                How do I access premium services?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once you subscribe, you'll immediately gain access to all premium service categories. Simply browse to the category you're interested in and book services as usual.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pricing-card {
    background: linear-gradient(135deg, #fff8e6 0%, #ffecb3 100%);
    border-radius: 15px;
    padding: 2rem;
    margin: 1rem 0;
}

.price-display .display-3 {
    color: #ff9800;
}

.accordion-button:not(.collapsed) {
    background-color: #fff8e6;
    color: #ff9800;
}
</style>

<script>
document.getElementById('subscribeBtn').addEventListener('click', function() {
    alert('Subscription functionality would be implemented here with payment processing integration.');
});
</script>

<?php include 'includes/unified_footer.php'; ?>