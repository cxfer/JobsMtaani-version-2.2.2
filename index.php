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
include 'includes/unified_header.php';
?>

<!-- Hero Section 
<section class="bg-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Find Trusted Local Services</h1>
                <p class="lead mb-4">Connect with skilled professionals in your area for all your home, business, and personal needs.</p>
                <div class="d-flex gap-3">
                    <a href="services.php" class="btn btn-light btn-lg">Browse Services</a>
                    <a href="register.php?user_type=provider" class="btn btn-outline-light btn-lg">Become a Provider</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Services" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>-->

<!-- Search Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form class="bg-white p-4 rounded shadow" method="GET" action="services.php">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-lg" name="search" placeholder="What service are you looking for?">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-lg" name="location" placeholder="Your location (e.g. Nairobi, Mombasa)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Popular Categories</h2>
            <p class="text-muted">Browse services by category to find exactly what you need</p>
        </div>
        <div class="row">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-concierge-bell text-primary fs-2"></i>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-outline-primary">View All Categories</a>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Featured Services</h2>
            <p class="text-muted">Handpicked services from our top-rated providers</p>
        </div>
        <div class="row">
            <?php foreach ($featured_services as $service_item): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo htmlspecialchars($service_item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service_item['title']); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title"><?php echo htmlspecialchars($service_item['title']); ?></h5>
                            <?php if ($service_item['featured']): ?>
                            <span class="badge bg-warning">FEATURED</span>
                            <?php endif; ?>
                        </div>
                        <p class="card-text flex-grow-1"><?php echo htmlspecialchars(substr($service_item['description'], 0, 100)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="h5 text-primary">KES <?php echo number_format($service_item['price']); ?></span>
                            </div>
                            <div>
                                <?php if ($service_item['avg_rating']): ?>
                                <span class="text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $service_item['avg_rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif ($i - 0.5 <= $service_item['avg_rating']): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>
                                <span class="text-muted">(<?php echo $service_item['review_count'] ?? 0; ?>)</span>
                                <?php else: ?>
                                <span class="text-muted">No reviews</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo !empty($service_item['profile_image']) ? $service_item['profile_image'] : '/public/placeholder-user.jpg'; ?>" alt="Provider" class="rounded-circle me-2" width="32" height="32">
                                <small class="text-muted"><?php echo htmlspecialchars($service_item['first_name'] . ' ' . $service_item['last_name']); ?></small>
                            </div>
                            <a href="service-details.php?id=<?php echo $service_item['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">How It Works</h2>
            <p class="text-muted">Getting quality services has never been easier</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-search text-primary fs-1"></i>
                    </div>
                    <h5>Find Services</h5>
                    <p class="text-muted">Browse our extensive collection of verified local service providers.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-calendar-check text-primary fs-1"></i>
                    </div>
                    <h5>Book & Schedule</h5>
                    <p class="text-muted">Select your preferred provider and schedule a convenient time.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-star text-primary fs-1"></i>
                    </div>
                    <h5>Enjoy & Review</h5>
                    <p class="text-muted">Get quality service and leave a review for other customers.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/unified_footer.php'; ?>