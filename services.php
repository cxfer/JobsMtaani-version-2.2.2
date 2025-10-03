<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Service.php';

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);

// Get search parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$rating = $_GET['rating'] ?? '';

// Get services with pagination
$limit = 12;
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

// Get all services
$all_services = $service->getServices($search, $category, $location, $limit, $offset, $min_price, $max_price, $rating);
$total_services = $service->getServicesCount($search, $category, $location, $min_price, $max_price, $rating);
$total_pages = ceil($total_services / $limit);

// Get categories for filter
$categories = $service->getCategories();

$pageTitle = "Browse Services";
include 'includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="services.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Service name">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="City or area">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($min_price); ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($max_price); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Minimum Rating</label>
                            <select class="form-select" name="rating">
                                <option value="">Any Rating</option>
                                <option value="4" <?php echo $rating == '4' ? 'selected' : ''; ?>>4+ Stars</option>
                                <option value="3" <?php echo $rating == '3' ? 'selected' : ''; ?>>3+ Stars</option>
                                <option value="2" <?php echo $rating == '2' ? 'selected' : ''; ?>>2+ Stars</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        
                        <a href="services.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Services Listing -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Services</h2>
                <p class="mb-0 text-muted">Showing <?php echo count($all_services); ?> of <?php echo $total_services; ?> services</p>
            </div>
            
            <?php if (empty($all_services)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>No services found</h4>
                <p class="text-muted">Try adjusting your filters or search terms</p>
                <a href="services.php" class="btn btn-primary">View All Services</a>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($all_services as $service_item): ?>
                <div class="col-md-6 col-lg-4 mb-4">
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
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Services pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&rating=<?php echo urlencode($rating); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&rating=<?php echo urlencode($rating); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&rating=<?php echo urlencode($rating); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/unified_footer.php'; ?>