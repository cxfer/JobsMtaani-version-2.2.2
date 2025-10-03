<?php
/**
 * Bookings API
 * Handles booking operations for the JobsMtaani platform
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Booking.php';
require_once '../classes/Service.php';

header('Content-Type: application/json');

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if user is authenticated
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$current_user = Auth::getCurrentUser();
$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            handleGetRequest($booking, $current_user, $db);
            break;
            
        case 'POST':
            handlePostRequest($booking, $current_user, $db);
            break;
            
        case 'PUT':
            handlePutRequest($booking, $current_user, $db);
            break;
            
        case 'DELETE':
            handleDeleteRequest($booking, $current_user, $db);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetRequest($booking, $current_user, $db) {
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'my-bookings':
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            $status = $_GET['status'] ?? null;
            $date = $_GET['date'] ?? null;
            $search = $_GET['search'] ?? null;
            
            if ($current_user['user_type'] === 'service_provider') {
                $bookings = $booking->getBookingsByUser($current_user['id'], 'service_provider', $limit, $offset, $status, $date, $search);
            } else {
                $bookings = $booking->getCustomerBookings($current_user['id'], $limit, $offset, $status, $date, $search);
            }
            
            echo json_encode(['success' => true, 'bookings' => $bookings]);
            break;
            
        case 'details':
            $booking_id = (int)($_GET['id'] ?? 0);
            if (!$booking_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
                return;
            }
            
            // Get booking details
            $stmt = $db->prepare("SELECT b.*, s.title as service_title, s.price,
                                         u1.first_name as customer_first_name, u1.last_name as customer_last_name, u1.phone as customer_phone,
                                         u2.first_name as provider_first_name, u2.last_name as provider_last_name, u2.phone as provider_phone
                                  FROM bookings b
                                  LEFT JOIN services s ON b.service_id = s.id
                                  LEFT JOIN users u1 ON b.customer_id = u1.id
                                  LEFT JOIN users u2 ON b.provider_id = u2.id
                                  WHERE b.id = :id AND (b.customer_id = :user_id OR b.provider_id = :user_id OR :user_type = 'admin')");
            $stmt->execute([
                ':id' => $booking_id,
                ':user_id' => $current_user['id'],
                ':user_type' => $current_user['user_type']
            ]);
            
            $booking_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$booking_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            echo json_encode(['success' => true, 'booking' => $booking_data]);
            break;
            
        default:
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            $status = $_GET['status'] ?? null;
            $date = $_GET['date'] ?? null;
            $search = $_GET['search'] ?? null;
            
            // Admin can get all bookings
            if ($current_user['user_type'] === 'admin' || $current_user['user_type'] === 'superadmin') {
                $bookings = $booking->getAllBookings($limit, $offset, $status, $date, $search);
                echo json_encode(['success' => true, 'bookings' => $bookings]);
            } else {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
            }
            break;
    }
}

function handlePostRequest($booking, $current_user, $db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        return;
    }
    
    // For creating new bookings, only customers can do this
    if ($current_user['user_type'] !== 'customer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only customers can create bookings']);
        return;
    }
    
    // Validate required fields
    $required_fields = ['service_id', 'booking_date', 'booking_time', 'location_type'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Set booking properties
    $booking->customer_id = $current_user['id'];
    $booking->service_id = (int)$data['service_id'];
    $booking->booking_date = $data['booking_date'];
    $booking->booking_time = $data['booking_time'];
    $booking->location_type = $data['location_type'];
    $booking->customer_notes = $data['customer_notes'] ?? '';
    $booking->status = 'pending';
    
    // Get service details to set provider_id and price
    $stmt = $db->prepare("SELECT provider_id, price, duration FROM services WHERE id = :service_id AND is_active = 1");
    $stmt->execute([':service_id' => $booking->service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found or inactive']);
        return;
    }
    
    $booking->provider_id = $service['provider_id'];
    $booking->total_amount = $service['price'];
    $booking->duration = $service['duration'];
    $booking->service_address = $data['service_address'] ?? '';
    
    // Create booking
    if ($booking->create()) {
        // Send notification to provider
        sendNotification($db, $booking->provider_id, 'New Booking', 'You have received a new booking request', 'booking');
        
        echo json_encode([
            'success' => true, 
            'message' => 'Booking created successfully',
            'booking_id' => $booking->id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
    }
}

function handlePutRequest($booking, $current_user, $db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        return;
    }
    
    $booking_id = (int)$data['id'];
    $new_status = $data['status'] ?? null;
    
    if (!$new_status) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Status is required']);
        return;
    }
    
    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    // Check if user has permission to update this booking
    $stmt = $db->prepare("SELECT customer_id, provider_id, status FROM bookings WHERE id = :id");
    $stmt->execute([':id' => $booking_id]);
    $booking_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        return;
    }
    
    // Check permissions based on user type
    $can_update = false;
    $user_message = '';
    
    switch ($current_user['user_type']) {
        case 'customer':
            // Customers can only cancel their own bookings when pending or confirmed
            if ($booking_data['customer_id'] == $current_user['id'] && 
                $new_status === 'cancelled' && 
                in_array($booking_data['status'], ['pending', 'confirmed'])) {
                $can_update = true;
                $user_message = 'Booking cancelled successfully';
            }
            break;
            
        case 'service_provider':
            // Providers can update status of their own bookings
            if ($booking_data['provider_id'] == $current_user['id']) {
                $can_update = true;
                $user_message = 'Booking status updated successfully';
                
                // Special case for cancellation by provider
                if ($new_status === 'cancelled') {
                    $user_message = 'Booking cancelled successfully';
                }
            }
            break;
            
        case 'admin':
        case 'superadmin':
            // Admins can update any booking
            $can_update = true;
            $user_message = 'Booking status updated successfully';
            break;
    }
    
    if (!$can_update) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied or invalid status change']);
        return;
    }
    
    // Update booking status
    if ($booking->updateStatus($booking_id, $new_status, $current_user['user_type'] === 'admin' || $current_user['user_type'] === 'superadmin' ? null : $current_user['id'])) {
        // Send notification
        $notification_user_id = ($current_user['user_type'] === 'customer') ? $booking_data['provider_id'] : $booking_data['customer_id'];
        $notification_message = "Booking status updated to: " . ucfirst(str_replace('_', ' ', $new_status));
        sendNotification($db, $notification_user_id, 'Booking Update', $notification_message, 'booking');
        
        echo json_encode([
            'success' => true, 
            'message' => $user_message
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
    }
}

function handleDeleteRequest($booking, $current_user, $db) {
    // Booking deletion is handled through status update to 'cancelled'
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Use PUT request to cancel bookings']);
}

function sendNotification($db, $user_id, $title, $message, $type) {
    try {
        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:user_id, :title, :message, :type)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type
        ]);
    } catch (Exception $e) {
        // Log error but don't fail the main operation
        error_log("Failed to send notification: " . $e->getMessage());
    }
}
?>