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

// Check if we're leaving a review for a specific booking
$booking_id = $_GET['booking_id'] ?? null;

if ($booking_id) {
    // Get booking details
    $stmt = $db->prepare("SELECT b.*, s.title AS service_title, CONCAT(u.first_name, ' ', u.last_name) AS provider_name
                          FROM bookings b
                          LEFT JOIN services s ON b.service_id = s.id
                          LEFT JOIN users u ON b.provider_id = u.id
                          WHERE b.id = ? AND b.customer_id = ? AND b.status = 'completed'
                          LIMIT 1");
    $stmt->execute([$booking_id, $current_user['id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        header('Location: bookings.php');
        exit;
    }
}

// Get customer reviews
$stmt = $db->prepare("SELECT r.*, s.title AS service_title, CONCAT(u.first_name, ' ', u.last_name) AS provider_name
                      FROM reviews r
                      LEFT JOIN bookings b ON r.booking_id = b.id
                      LEFT JOIN services s ON b.service_id = s.id
                      LEFT JOIN users u ON r.reviewee_id = u.id
                      WHERE r.reviewer_id = ?
                      ORDER BY r.created_at DESC");
$stmt->execute([$current_user['id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = $booking_id ? "Leave a Review" : "My Reviews";
include '../includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <?php if ($booking_id): ?>
                <h1 class="mb-4">Leave a Review</h1>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($booking['service_title']); ?></h5>
                        <p class="text-muted">Provider: <?php echo htmlspecialchars($booking['provider_name']); ?></p>
                        <p>Booking completed on <?php echo date('M j, Y', strtotime($booking['completed_at'])); ?></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="#">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star rating-star" data-rating="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                    <input type="hidden" name="rating" id="rating-input" value="0" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="review_text" class="form-label">Review</label>
                                <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Share your experience with this service..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                            <a href="bookings.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="mb-4">My Reviews</h1>
                
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h4>No reviews yet</h4>
                        <p class="text-muted">You haven't left any reviews. Completed bookings will appear here when ready for review.</p>
                        <a href="bookings.php" class="btn btn-primary">View My Bookings</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($reviews as $review): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title"><?php echo htmlspecialchars($review['service_title']); ?></h5>
                                            <p class="text-muted mb-1">Provider: <?php echo htmlspecialchars($review['provider_name']); ?></p>
                                        </div>
                                        <div class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                                    
                                    <small class="text-muted">Reviewed on <?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.rating-stars {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
}

.rating-star:hover,
.rating-star.active {
    color: #ffc107;
}
</style>

<script>
// Rating star functionality
document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-rating');
        document.getElementById('rating-input').value = rating;
        
        // Update star display
        document.querySelectorAll('.rating-star').forEach(s => {
            if (s.getAttribute('data-rating') <= rating) {
                s.classList.add('active');
                s.classList.remove('far');
                s.classList.add('fas');
            } else {
                s.classList.remove('active');
                s.classList.remove('fas');
                s.classList.add('far');
            }
        });
    });
    
    star.addEventListener('mouseover', function() {
        const rating = this.getAttribute('data-rating');
        document.querySelectorAll('.rating-star').forEach(s => {
            if (s.getAttribute('data-rating') <= rating) {
                s.style.color = '#ffc107';
            }
        });
    });
    
    star.addEventListener('mouseout', function() {
        document.querySelectorAll('.rating-star').forEach(s => {
            if (!s.classList.contains('active')) {
                s.style.color = '#ddd';
            }
        });
    });
});
</script>

<?php include '../includes/unified_footer.php'; ?>