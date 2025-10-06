<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Service.php';

// Require customer access
Auth::requireRole(['customer']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$service = new Service($db);

$current_user = Auth::getCurrentUser();

// Get favorite services
$stmt = $db->prepare("SELECT f.*, s.title AS service_title, s.price, s.images, c.name AS category_name, 
                      CONCAT(u.first_name, ' ', u.last_name) AS provider_name
                      FROM favorites f
                      LEFT JOIN services s ON f.service_id = s.id
                      LEFT JOIN service_categories c ON s.category_id = c.id
                      LEFT JOIN users u ON s.provider_id = u.id
                      WHERE f.user_id = ?
                      ORDER BY f.created_at DESC");
$stmt->execute([$current_user['id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process images for favorites
foreach ($favorites as &$favorite) {
    if (!empty($favorite['images'])) {
        $images = json_decode($favorite['images'], true);
        if (is_array($images) && !empty($images)) {
            $favorite['image'] = $images[0];
        } else {
            $favorite['image'] = '/public/abstract-service.png';
        }
    } else {
        $favorite['image'] = '/public/abstract-service.png';
    }
}

$pageTitle = "My Favorites";
include '../includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">My Favorite Services</h1>
            
            <?php if (empty($favorites)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                    <h4>No favorites yet</h4>
                    <p class="text-muted">You haven't added any services to your favorites. <a href="../services.php">Browse services</a> to get started.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($favorites as $favorite): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($favorite['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($favorite['service_title']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?php echo htmlspecialchars($favorite['service_title']); ?></h5>
                                </div>
                                <p class="card-text flex-grow-1 text-muted"><?php echo htmlspecialchars($favorite['category_name']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span class="h5 text-primary">KES <?php echo number_format($favorite['price']); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-muted">by <?php echo htmlspecialchars($favorite['provider_name']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="d-flex justify-content-between">
                                    <a href="../service-details.php?id=<?php echo $favorite['service_id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                    <button class="btn btn-outline-danger btn-sm" onclick="removeFavorite(<?php echo $favorite['id']; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function removeFavorite(favoriteId) {
    if (confirm('Are you sure you want to remove this service from your favorites?')) {
        // In a real implementation, this would make an AJAX call to remove the favorite
        alert('Favorite removal functionality would be implemented here.');
    }
}
</script>

<?php include '../includes/unified_footer.php'; ?>