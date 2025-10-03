<?php
session_start();
require_once 'includes/app_settings.php';
require_once 'classes/Subscription.php';
require_once 'classes/Auth.php';

$appName = AppSettings::get('app_name', 'JobsMtaani');
$subscriptionObj = new Subscription();
$plans = $subscriptionObj->getPlans();

$currentUser = Auth::getCurrentUser();
$currentSubscription = null;

if ($currentUser) {
    $currentSubscription = $subscriptionObj->getUserSubscription($currentUser['id']);
}

include 'includes/header.php';
?>

<style>
    /* Pricing Page Styles */
    .hero-section {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
        padding: var(--spacing-12) 0;
        color: white;
    }
    
    .hero-title {
        font-size: var(--font-size-4xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--spacing-3);
    }
    
    .hero-subtitle {
        font-size: var(--font-size-xl);
        opacity: 0.9;
        margin-bottom: var(--spacing-6);
    }
    
    .billing-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-3);
        margin-bottom: var(--spacing-8);
    }
    
    .form-check-input:checked {
        background-color: var(--primary-600);
        border-color: var(--primary-600);
    }
    
    .badge-success {
        background-color: var(--accent-success);
    }
    
    .section-padding {
        padding: var(--spacing-12) 0;
    }
    
    .pricing-card {
        background-color: var(--surface);
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        height: 100%;
        transition: var(--transition-all);
        position: relative;
    }
    
    .pricing-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
    }
    
    .pricing-card.featured {
        border-color: var(--primary-300);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.25);
    }
    
    .featured-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--primary-600);
        color: white;
        padding: var(--spacing-2) var(--spacing-4);
        border-radius: var(--radius-full);
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        box-shadow: var(--shadow-md);
    }
    
    .card-body {
        padding: var(--spacing-8);
    }
    
    .plan-header {
        text-align: center;
        margin-bottom: var(--spacing-6);
    }
    
    .plan-title {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-600);
        margin-bottom: var(--spacing-2);
    }
    
    .plan-description {
        color: var(--text-secondary);
        margin-bottom: 0;
    }
    
    .plan-price {
        text-align: center;
        margin-bottom: var(--spacing-6);
    }
    
    .price-amount {
        font-size: var(--font-size-4xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-600);
        margin-bottom: var(--spacing-2);
    }
    
    .price-period {
        color: var(--text-secondary);
    }
    
    .features-list {
        list-style: none;
        padding: 0;
        margin-bottom: var(--spacing-6);
    }
    
    .features-list li {
        padding: var(--spacing-3) 0;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
    }
    
    .features-list li:last-child {
        border-bottom: none;
    }
    
    .feature-icon {
        color: var(--accent-success);
        margin-right: var(--spacing-3);
        width: 20px;
        text-align: center;
    }
    
    .btn-pricing {
        width: 100%;
        padding: var(--spacing-3) var(--spacing-4);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
    }
    
    .btn-success {
        background-color: var(--accent-success);
        border-color: var(--accent-success);
    }
    
    .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }
    
    /* FAQ Section */
    .faq-section {
        background-color: var(--neutral-50);
    }
    
    .section-title {
        text-align: center;
        margin-bottom: var(--spacing-10);
    }
    
    .section-title h2 {
        font-size: var(--font-size-3xl);
        margin-bottom: var(--spacing-3);
        color: var(--text-primary);
    }
    
    .accordion {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .accordion-item {
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-3);
        overflow: hidden;
    }
    
    .accordion-button {
        font-weight: var(--font-weight-semibold);
        color: var(--text-primary);
        padding: var(--spacing-4) var(--spacing-5);
        background-color: var(--surface);
    }
    
    .accordion-button:not(.collapsed) {
        color: var(--primary-600);
        background-color: var(--primary-50);
        box-shadow: none;
    }
    
    .accordion-body {
        padding: var(--spacing-4) var(--spacing-5);
        color: var(--text-secondary);
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: var(--radius-xl);
        border: none;
        box-shadow: var(--shadow-xl);
    }
    
    .modal-header {
        border-bottom: 1px solid var(--border);
        padding: var(--spacing-5);
    }
    
    .modal-title {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
    }
    
    .modal-body {
        padding: var(--spacing-5);
    }
    
    .modal-footer {
        border-top: 1px solid var(--border);
        padding: var(--spacing-5);
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: var(--font-size-3xl);
        }
        
        .hero-subtitle {
            font-size: var(--font-size-base);
        }
        
        .card-body {
            padding: var(--spacing-6);
        }
        
        .price-amount {
            font-size: var(--font-size-3xl);
        }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h1 class="hero-title">Choose Your Plan</h1>
                    <p class="hero-subtitle">Unlock more features and grow your business with our flexible pricing plans</p>
                    
                    <!-- Billing Toggle -->
                    <div class="billing-toggle">
                        <span>Monthly</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="billingToggle">
                            <label class="form-check-label" for="billingToggle"></label>
                        </div>
                        <span>Yearly <span class="badge badge-success">Save 20%</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <?php foreach ($plans as $plan): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card <?php echo $plan['featured'] ? 'featured' : ''; ?>">
                            <?php if ($plan['featured']): ?>
                                <div class="featured-badge">Most Popular</div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <div class="plan-header">
                                    <h3 class="plan-title"><?php echo htmlspecialchars($plan['name']); ?></h3>
                                    <p class="plan-description"><?php echo htmlspecialchars($plan['description']); ?></p>
                                </div>

                                <div class="plan-price">
                                    <div class="price-amount">
                                        <?php if ($plan['price'] == 0): ?>
                                            Free
                                        <?php else: ?>
                                            <span class="monthly-price">KSh <?php echo number_format($plan['price']); ?></span>
                                            <span class="yearly-price d-none">KSh <?php echo number_format($plan['price'] * 12 * 0.8); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($plan['price'] > 0): ?>
                                        <div class="price-period">
                                            <span class="monthly-billing">per month</span>
                                            <span class="yearly-billing d-none">per year</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Features List -->
                                <div class="mb-4">
                                    <?php 
                                    $features = json_decode($plan['features'], true);
                                    $featureLabels = [
                                        'basic_listing' => 'Basic service listing',
                                        'premium_listing' => 'Premium listing with priority',
                                        'unlimited_services' => 'Unlimited services',
                                        'advanced_analytics' => 'Advanced analytics',
                                        'priority_support' => 'Priority customer support',
                                        'custom_branding' => 'Custom branding',
                                        'api_access' => 'API access',
                                        'white_label' => 'White label solution'
                                    ];
                                    ?>
                                    <ul class="features-list">
                                        <?php foreach ($features as $feature): ?>
                                            <li>
                                                <i class="fas fa-check feature-icon"></i>
                                                <?php echo $featureLabels[$feature] ?? ucfirst(str_replace('_', ' ', $feature)); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <!-- Action Button -->
                                <div class="d-grid">
                                    <?php if ($currentSubscription && $currentSubscription['plan_id'] == $plan['id']): ?>
                                        <button class="btn btn-success btn-pricing" disabled>
                                            <i class="fas fa-check me-2"></i>Current Plan
                                        </button>
                                    <?php elseif ($plan['price'] == 0): ?>
                                        <a href="<?php echo $currentUser ? 'customer/' : 'register.php'; ?>" class="btn btn-outline btn-pricing">
                                            Get Started Free
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-pricing subscribe-btn" 
                                                data-plan-id="<?php echo $plan['id']; ?>" 
                                                data-plan-name="<?php echo htmlspecialchars($plan['name']); ?>"
                                                data-plan-price="<?php echo $plan['price']; ?>">
                                            <?php echo $currentUser ? 'Upgrade Now' : 'Get Started'; ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about our pricing plans</p>
            </div>
            
            <div class="accordion" id="pricingFAQ">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Can I change my plan anytime?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#pricingFAQ">
                        <div class="accordion-body">
                            Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate any billing differences.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            What payment methods do you accept?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                        <div class="accordion-body">
                            We accept M-Pesa, credit/debit cards, and bank transfers. All payments are processed securely through our payment partners.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Is there a free trial?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                        <div class="accordion-body">
                            Our Free plan gives you access to basic features with no time limit. You can upgrade to premium features anytime.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            What happens if I cancel my subscription?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                        <div class="accordion-body">
                            You can cancel your subscription at any time. You'll continue to have access to your plan features until the end of your billing period.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Subscription Modal -->
<div class="modal fade" id="subscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subscribe to <span id="modalPlanName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-primary" id="modalPlanPrice"></div>
                    <div class="text-muted">per month</div>
                </div>
                
                <form id="subscriptionForm">
                    <input type="hidden" id="planId" name="plan_id">
                    <!-- Rest of the form would go here -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Subscribe</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Billing toggle functionality
    document.getElementById('billingToggle').addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.monthly-price, .monthly-billing').forEach(el => {
                el.classList.add('d-none');
            });
            document.querySelectorAll('.yearly-price, .yearly-billing').forEach(el => {
                el.classList.remove('d-none');
            });
        } else {
            document.querySelectorAll('.monthly-price, .monthly-billing').forEach(el => {
                el.classList.remove('d-none');
            });
            document.querySelectorAll('.yearly-price, .yearly-billing').forEach(el => {
                el.classList.add('d-none');
            });
        }
    });
</script>
</body>
</html>