<?php
session_start();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Service.php';

$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$database = new Database();
$db = $database->getConnection();
$serviceModel = new Service($db);

// Fetch single service details (basic)
$stmt = $db->prepare("SELECT s.*, c.name AS category_name, CONCAT(u.first_name,' ',u.last_name) AS provider_name
                      FROM services s
                      LEFT JOIN service_categories c ON s.category_id = c.id
                      LEFT JOIN users u ON u.id = s.provider_id
                      WHERE s.id = :id LIMIT 1");
$stmt->bindParam(':id', $serviceId, PDO::PARAM_INT);
$stmt->execute();
$service = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    /* Booking Page Styles */
    .booking-container {
        background: linear-gradient(135deg, var(--primary-50) 0%, var(--secondary-50) 100%);
        min-height: 100vh;
        padding: var(--spacing-8) 0;
    }
    
    .booking-card {
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
        height: 300px;
        object-fit: cover;
    }
    
    .service-description {
        padding: var(--spacing-6);
        border-bottom: 1px solid var(--border);
    }
    
    .booking-form-section {
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
    
    .form-group {
        margin-bottom: var(--spacing-5);
    }
    
    .form-label {
        display: block;
        margin-bottom: var(--spacing-2);
        font-weight: var(--font-weight-medium);
        color: var(--text-primary);
    }
    
    .form-control {
        width: 100%;
        padding: var(--spacing-3) var(--spacing-4);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-base);
        transition: var(--transition-all);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-400);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .form-select {
        width: 100%;
        padding: var(--spacing-3) var(--spacing-4);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-base);
        background-color: var(--surface);
        transition: var(--transition-all);
    }
    
    .form-select:focus {
        outline: none;
        border-color: var(--primary-400);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .btn-booking {
        width: 100%;
        padding: var(--spacing-4);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }
    
    .price-summary {
        background-color: var(--primary-50);
        border-radius: var(--radius-lg);
        padding: var(--spacing-5);
        margin-top: var(--spacing-5);
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: var(--spacing-3);
    }
    
    .price-total {
        display: flex;
        justify-content: space-between;
        font-weight: var(--font-weight-bold);
        font-size: var(--font-size-lg);
        padding-top: var(--spacing-3);
        border-top: 1px solid var(--border);
        margin-top: var(--spacing-3);
    }
    
    .alert {
        border-radius: var(--radius-lg);
        padding: var(--spacing-4);
        margin-bottom: var(--spacing-5);
    }
    
    .alert-warning {
        background-color: #fef3c7;
        border-color: #fde68a;
        color: var(--accent-warning);
    }
    
    @media (max-width: 768px) {
        .booking-container {
            padding: var(--spacing-4) 0;
        }
        
        .service-title {
            font-size: var(--font-size-2xl);
        }
        
        .booking-card {
            border-radius: var(--radius-xl);
        }
    }
</style>

<main class="booking-container">
    <div class="container">
        <?php if (!$service): ?>
            <div class="alert alert-warning">Service not found.</div>
        <?php else: ?>
            <div class="booking-card">
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
                        <div class="meta-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>KSh <?php echo number_format($service['price']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="service-description">
                    <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    <img src="<?php echo '/public/abstract-service.png'; ?>" class="service-image rounded" alt="Service">
                </div>
                
                <div class="booking-form-section">
                    <h2 class="section-title">Book this service</h2>
                    <form method="post" action="#">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="booking_date" class="form-label">Preferred Date</label>
                                    <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="booking_time" class="form-label">Preferred Time</label>
                                    <input type="time" class="form-control" id="booking_time" name="booking_time" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="location_type" class="form-label">Location Type</label>
                            <select class="form-select" id="location_type" name="location_type" required>
                                <option value="">Select location type</option>
                                <option value="at_customer">At customer's location</option>
                                <option value="at_provider">At provider's location</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_address" class="form-label">Service Address</label>
                            <textarea class="form-control" id="service_address" rows="3" name="service_address" placeholder="Your address (if at customer)"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_notes" class="form-label">Special Notes</label>
                            <textarea class="form-control" id="customer_notes" rows="3" name="customer_notes" placeholder="Any special requirements or details for the provider"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-booking">
                            <i class="fas fa-calendar-check me-2"></i>Request Booking
                        </button>
                        
                        <div class="price-summary">
                            <h3 class="mb-4">Price Summary</h3>
                            <div class="price-row">
                                <span>Service Price</span>
                                <span>KSh <?php echo number_format($service['price']); ?></span>
                            </div>
                            <div class="price-row">
                                <span>Platform Fee</span>
                                <span>KSh <?php echo number_format($service['price'] * 0.1); ?></span>
                            </div>
                            <div class="price-row">
                                <span>Tax</span>
                                <span>KSh <?php echo number_format($service['price'] * 0.16); ?></span>
                            </div>
                            <div class="price-total">
                                <span>Total Amount</span>
                                <span>KSh <?php echo number_format($service['price'] * 1.26); ?></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>