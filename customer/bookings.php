<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Booking.php';
require_once '../classes/Service.php';

// Require customer access
Auth::requireRole(['customer']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$booking = new Booking($db);
$service = new Service($db);

$current_user = Auth::getCurrentUser();

// Get customer bookings
$stmt = $db->prepare("SELECT b.*, s.title AS service_title, CONCAT(u.first_name, ' ', u.last_name) AS provider_name, c.name AS category_name
                      FROM bookings b
                      LEFT JOIN services s ON b.service_id = s.id
                      LEFT JOIN users u ON b.provider_id = u.id
                      LEFT JOIN service_categories c ON s.category_id = c.id
                      WHERE b.customer_id = ?
                      ORDER BY b.created_at DESC");
$stmt->execute([$current_user['id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Bookings";
include '../includes/unified_header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">My Bookings</h1>
            
            <?php if (empty($bookings)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h4>No bookings yet</h4>
                    <p class="text-muted">You haven't made any bookings. <a href="../services.php">Browse services</a> to get started.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($bookings as $booking_item): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($booking_item['service_title']); ?></h5>
                                        <p class="text-muted mb-1"><?php echo htmlspecialchars($booking_item['provider_name']); ?></p>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($booking_item['category_name']); ?></span>
                                    </div>
                                    <span class="badge bg-<?php 
                                        switch ($booking_item['status']) {
                                            case 'pending': echo 'warning';
                                            case 'confirmed': echo 'info';
                                            case 'in_progress': echo 'primary';
                                            case 'completed': echo 'success';
                                            case 'cancelled': echo 'danger';
                                            case 'refunded': echo 'secondary';
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $booking_item['status'])); ?>
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1"><i class="fas fa-calendar me-2"></i> <?php echo date('M j, Y', strtotime($booking_item['booking_date'])); ?> at <?php echo date('g:i A', strtotime($booking_item['booking_time'])); ?></p>
                                    <p class="mb-1"><i class="fas fa-money-bill-wave me-2"></i> KES <?php echo number_format($booking_item['total_amount']); ?></p>
                                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> <?php echo ucfirst(str_replace('_', ' ', $booking_item['location_type'])); ?></p>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Booked on <?php echo date('M j, Y', strtotime($booking_item['created_at'])); ?></small>
                                    <div>
                                        <?php if ($booking_item['status'] == 'completed'): ?>
                                            <a href="reviews.php?booking_id=<?php echo $booking_item['id']; ?>" class="btn btn-sm btn-outline-primary">Leave Review</a>
                                        <?php endif; ?>
                                        <?php if (in_array($booking_item['status'], ['pending', 'confirmed'])): ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="cancelBooking(<?php echo $booking_item['id']; ?>)">Cancel</button>
                                        <?php endif; ?>
                                    </div>
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
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // In a real implementation, this would make an AJAX call to cancel the booking
        alert('Booking cancellation functionality would be implemented here.');
    }
}
</script>

<?php include '../includes/unified_footer.php'; ?>