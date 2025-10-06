<?php
session_start();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Service.php';
require_once __DIR__ . '/classes/Auth.php';

$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$database = new Database();
$db = $database->getConnection();
$serviceModel = new Service($db);

// Fetch single service details with provider info and reviews
$stmt = $db->prepare("SELECT s.*, c.name AS category_name, CONCAT(u.first_name,' ',u.last_name) AS provider_name, u.id AS provider_id, up.bio AS provider_bio, up.address AS provider_address, up.city AS provider_city,
                      (SELECT COUNT(*) FROM reviews r WHERE r.reviewee_id = u.id) AS total_reviews,
                      (SELECT AVG(rating) FROM reviews r WHERE r.reviewee_id = u.id) AS avg_rating
                      FROM services s
                      LEFT JOIN service_categories c ON s.category_id = c.id
                      LEFT JOIN users u ON u.id = s.provider_id
                      LEFT JOIN user_profiles up ON u.id = up.user_id
                      WHERE s.id = :id AND s.is_active = 1 LIMIT 1");
$stmt->bindParam(':id', $serviceId, PDO::PARAM_INT);
$stmt->execute();
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch provider's other services
$providerServices = [];
if ($service) {
    $stmt = $db->prepare("SELECT id, title, price, images FROM services WHERE provider_id = :provider_id AND id != :service_id AND is_active = 1 LIMIT 3");
    $stmt->bindParam(':provider_id', $service['provider_id'], PDO::PARAM_INT);
    $stmt->bindParam(':service_id', $serviceId, PDO::PARAM_INT);
    $stmt->execute();
    $providerServices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process images for provider services
    foreach ($providerServices as &$providerService) {
        if (!empty($providerService['images'])) {
            $images = json_decode($providerService['images'], true);
            if (is_array($images) && !empty($images)) {
                $providerService['image'] = $images[0];
            } else {
                $providerService['image'] = '/public/abstract-service.png';
            }
        } else {
            $providerService['image'] = '/public/abstract-service.png';
        }
    }
}

// Fetch recent reviews for this service
$reviews = [];
if ($service) {
    $stmt = $db->prepare("SELECT r.*, CONCAT(u.first_name,' ',u.last_name) AS reviewer_name 
                          FROM reviews r
                          LEFT JOIN users u ON r.reviewer_id = u.id
                          WHERE r.reviewee_id = :provider_id
                          ORDER BY r.created_at DESC
                          LIMIT 5");
    $stmt->bindParam(':provider_id', $service['provider_id'], PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = $service ? htmlspecialchars($service['title']) : "Service Not Found";
?>
<style>
    /* Service Details Page Styles */
    .service-details-container {
        background: linear-gradient(135deg, var(--primary-50) 0%, var(--secondary-50) 100%);
        min-height: 100vh;
        padding: var(--spacing-8) 0;
    }
    
    .service-card {
        background-color: var(--surface);
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        margin-bottom: var(--spacing-6);
    }
    
    .service-header {
        padding: var(--spacing-6);
        border-bottom: 1px solid var(--border);
    }
    
    .service-title {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--spacing-2);
        color: var(--text-primary);
    }
    
    .service-meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-4);
        color: var(--text-secondary);
        margin-bottom: var(--spacing-4);
    }
    
    .meta-item {
        display: flex;
        align-items: center;
    }
    
    .meta-item i {
        margin-right: var(--spacing-2);
        color: var(--primary-600);
    }
    
    .service-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }
    
    .service-description {
        padding: var(--spacing-6);
        border-bottom: 1px solid var(--border);
    }
    
    .service-content {
        padding: var(--spacing-6);
    }
    
    .section-title {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--spacing-5);
        color: var(--text-primary);
        padding-bottom: var(--spacing-3);
        border-bottom: 1px solid var(--border);
    }
    
    .provider-card {
        background-color: var(--surface);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        padding: var(--spacing-5);
        margin-bottom: var(--spacing-5);
    }
    
    .provider-header {
        display: flex;
        align-items: center;
        margin-bottom: var(--spacing-4);
    }
    
    .provider-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: var(--spacing-4);
    }
    
    .provider-name {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--spacing-1);
    }
    
    .provider-meta {
        display: flex;
        gap: var(--spacing-3);
        color: var(--text-secondary);
        margin-bottom: var(--spacing-2);
    }
    
    .provider-bio {
        color: var(--text-secondary);
        line-height: 1.6;
    }
    
    .related-services {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: var(--spacing-4);
        margin-top: var(--spacing-4);
    }
    
    .related-service-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition-all);
    }
    
    .related-service-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .related-service-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    
    .related-service-content {
        padding: var(--spacing-3);
    }
    
    .related-service-title {
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--spacing-2);
    }
    
    .related-service-price {
        color: var(--primary-600);
        font-weight: var(--font-weight-bold);
    }
    
    .reviews-section {
        margin-top: var(--spacing-6);
    }
    
    .review-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: var(--spacing-4);
        margin-bottom: var(--spacing-4);
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: var(--spacing-3);
    }
    
    .reviewer-name {
        font-weight: var(--font-weight-semibold);
    }
    
    .review-date {
        color: var(--text-secondary);
        font-size: var(--font-size-sm);
    }
    
    .review-rating {
        color: #f59e0b;
        margin-bottom: var(--spacing-2);
    }
    
    .review-text {
        color: var(--text-secondary);
        line-height: 1.6;
    }
    
    .booking-cta {
        position: sticky;
        top: var(--spacing-8);
    }
    
    .btn-booking {
        width: 100%;
        padding: var(--spacing-4);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--spacing-3);
    }
    
    .price-display {
        text-align: center;
        padding: var(--spacing-4);
        background-color: var(--primary-50);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-4);
    }
    
    .price-amount {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-600);
    }
    
    .price-label {
        color: var(--text-secondary);
    }
    
    @media (max-width: 768px) {
        .service-details-container {
            padding: var(--spacing-4) 0;
        }
        
        .service-title {
            font-size: var(--font-size-2xl);
        }
        
        .service-card {
            border-radius: var(--radius-xl);
        }
        
        .service-image {
            height: 250px;
        }
    }
</style>

<main class="service-details-container">
    <div class="container">
        <?php if (!$service): ?>
            <div class="alert alert-warning">Service not found.</div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="service-card">
                        <div class="service-header">
                            <h1 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h1>
                            <div class="service-meta">
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span><?php echo htmlspecialchars($service['category_name']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo htmlspecialchars($service['provider_name']); ?></span>
                                </div>
                                <?php if ($service['avg_rating']): ?>
                                <div class="meta-item">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo number_format($service['avg_rating'], 1); ?> (<?php echo $service['total_reviews']; ?> reviews)</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php 
                        // Process service image
                        $serviceImage = '/public/abstract-service.png';
                        if (!empty($service['images'])) {
                            $images = json_decode($service['images'], true);
                            if (is_array($images) && !empty($images)) {
                                $serviceImage = $images[0];
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($serviceImage); ?>" class="service-image" alt="<?php echo htmlspecialchars($service['title']); ?>">
                        
                        <div class="service-description">
                            <h2 class="section-title">Service Description</h2>
                            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                        </div>
                        
                        <div class="service-content">
                            <h2 class="section-title">Service Details</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>Price:</strong> KSh <?php echo number_format($service['price']); ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Duration:</strong> <?php echo $service['duration'] ? $service['duration'] . ' minutes' : 'Variable'; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Location:</strong> <?php echo ucfirst(str_replace('_', ' ', $service['location_type'])); ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>Category:</strong> <?php echo htmlspecialchars($service['category_name']); ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Provider:</strong> <?php echo htmlspecialchars($service['provider_name']); ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Status:</strong> 
                                            <span class="badge <?php echo $service['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($providerServices)): ?>
                    <div class="service-card">
                        <div class="service-content">
                            <h2 class="section-title">More from this provider</h2>
                            <div class="related-services">
                                <?php foreach ($providerServices as $providerService): ?>
                                <div class="related-service-card">
                                    <img src="<?php echo htmlspecialchars($providerService['image']); ?>" class="related-service-image" alt="<?php echo htmlspecialchars($providerService['title']); ?>">
                                    <div class="related-service-content">
                                        <h3 class="related-service-title"><?php echo htmlspecialchars($providerService['title']); ?></h3>
                                        <div class="related-service-price">KSh <?php echo number_format($providerService['price']); ?></div>
                                        <a href="service-details.php?id=<?php echo $providerService['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">View Details</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($reviews)): ?>
                    <div class="service-card reviews-section">
                        <div class="service-content">
                            <h2 class="section-title">Customer Reviews</h2>
                            <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></div>
                                    <div class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="ms-2"><?php echo $review['rating']; ?>/5</span>
                                </div>
                                <div class="review-text">
                                    <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="booking-cta">
                        <div class="service-card">
                            <div class="price-display">
                                <div class="price-label">Starting at</div>
                                <div class="price-amount">KSh <?php echo number_format($service['price']); ?></div>
                            </div>
                            
                            <?php if (Auth::isLoggedIn()): ?>
                            <div class="service-content">
                                <a href="book-service.php?id=<?php echo $service['id']; ?>" class="btn btn-primary btn-booking">
                                    <i class="fas fa-calendar-check me-2"></i>Book This Service
                                </a>
                                <button class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-heart me-2"></i>Add to Favorites
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="service-content">
                                <a href="login.php" class="btn btn-primary btn-booking">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                </a>
                                <div class="alert alert-info mt-3">
                                    <small>You need to be logged in to book this service.</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="provider-card">
                            <div class="provider-header">
                                <img src="/public/placeholder-user.jpg" class="provider-avatar" alt="<?php echo htmlspecialchars($service['provider_name']); ?>">
                                <div>
                                    <h3 class="provider-name"><?php echo htmlspecialchars($service['provider_name']); ?></h3>
                                    <?php if ($service['avg_rating']): ?>
                                    <div class="provider-meta">
                                        <span>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $service['avg_rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            (<?php echo $service['total_reviews']; ?> reviews)
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($service['provider_bio'])): ?>
                            <p class="provider-bio"><?php echo nl2br(htmlspecialchars($service['provider_bio'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($service['provider_address']) || !empty($service['provider_city'])): ?>
                            <div class="mt-3">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?php echo htmlspecialchars(($service['provider_address'] ?? '') . ', ' . ($service['provider_city'] ?? '')); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-envelope me-2"></i>Contact Provider
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>