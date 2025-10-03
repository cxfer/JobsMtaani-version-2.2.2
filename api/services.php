<?php
/**
 * Services API
 * Handles service operations for the JobsMtaani platform
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
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

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            handleGetRequest($service, $db);
            break;
            
        case 'POST':
            handlePostRequest($service, $db);
            break;
            
        case 'PUT':
            handlePutRequest($service, $db);
            break;
            
        case 'DELETE':
            handleDeleteRequest($service, $db);
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

function handleGetRequest($service, $db) {
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'categories':
            $categories = $service->getCategories();
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;
            
        case 'my-services':
            // Check if user is authenticated
            if (!Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            $current_user = Auth::getCurrentUser();
            
            // Only service providers can have services
            if ($current_user['user_type'] !== 'service_provider') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            
            $services = $service->getServicesByProvider($current_user['id']);
            echo json_encode(['success' => true, 'services' => $services]);
            break;
            
        case 'details':
            $service_id = (int)($_GET['id'] ?? 0);
            if (!$service_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Service ID is required']);
                return;
            }
            
            // Get service details with provider and reviews
            $stmt = $db->prepare("SELECT s.*, c.name as category_name, c.slug as category_slug,
                                         u.first_name as provider_first_name, u.last_name as provider_last_name, u.phone as provider_phone,
                                         u.profile_image as provider_image,
                                         AVG(r.rating) as avg_rating, COUNT(r.id) as review_count,
                                         GROUP_CONCAT(r.rating) as ratings
                                  FROM services s
                                  LEFT JOIN service_categories c ON s.category_id = c.id
                                  LEFT JOIN users u ON s.provider_id = u.id
                                  LEFT JOIN bookings b ON s.id = b.service_id
                                  LEFT JOIN reviews r ON b.id = r.booking_id
                                  WHERE s.id = :id AND s.is_active = 1
                                  GROUP BY s.id");
            $stmt->execute([':id' => $service_id]);
            
            $service_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$service_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            // Process images
            if ($service_data['images']) {
                $service_data['images'] = json_decode($service_data['images'], true);
            } else {
                $service_data['images'] = ['/public/abstract-service.png'];
            }
            
            // Get recent reviews
            $stmt = $db->prepare("SELECT r.*, u.first_name, u.last_name, u.profile_image
                                  FROM reviews r
                                  LEFT JOIN users u ON r.reviewer_id = u.id
                                  LEFT JOIN bookings b ON r.booking_id = b.id
                                  WHERE b.service_id = :service_id
                                  ORDER BY r.created_at DESC
                                  LIMIT 5");
            $stmt->execute([':service_id' => $service_id]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $service_data['reviews'] = $reviews;
            
            echo json_encode(['success' => true, 'service' => $service_data]);
            break;
            
        default:
            // Public service listing
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $location = $_GET['location'] ?? '';
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            
            $services = $service->getServices($search, $category, $location, $limit, $offset);
            $total = $service->getServicesCount($search, $category, $location);
            
            echo json_encode([
                'success' => true, 
                'services' => $services,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            break;
    }
}

function handlePostRequest($service, $db) {
    // Check if user is authenticated
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        return;
    }
    
    $current_user = Auth::getCurrentUser();
    
    // Only service providers can create services
    if ($current_user['user_type'] !== 'service_provider') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        return;
    }
    
    // Validate required fields
    $required_fields = ['title', 'category_id', 'description', 'price'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Set service properties
    $service->provider_id = $current_user['id'];
    $service->category_id = (int)$data['category_id'];
    $service->title = trim($data['title']);
    $service->description = $data['description'];
    $service->short_description = $data['short_description'] ?? substr($data['description'], 0, 200);
    $service->price = (float)$data['price'];
    $service->price_type = $data['price_type'] ?? 'fixed';
    $service->duration = (int)($data['duration'] ?? 60);
    $service->location_type = $data['location_type'] ?? 'both';
    $service->is_active = (bool)($data['is_active'] ?? true);
    
    // Create service
    if ($service->create()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Service created successfully',
            'service_id' => $service->id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create service']);
    }
}

function handlePutRequest($service, $db) {
    // Check if user is authenticated
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        return;
    }
    
    $current_user = Auth::getCurrentUser();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Service ID is required']);
        return;
    }
    
    $service_id = (int)$data['id'];
    
    // Check if user owns this service or is admin
    $stmt = $db->prepare("SELECT provider_id FROM services WHERE id = :id");
    $stmt->execute([':id' => $service_id]);
    $service_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        return;
    }
    
    $is_owner = ($service_data['provider_id'] == $current_user['id']);
    $is_admin = ($current_user['user_type'] === 'admin' || $current_user['user_type'] === 'superadmin');
    
    if (!$is_owner && !$is_admin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    // Build update query
    $fields = [];
    $params = [':id' => $service_id];
    
    // Only allow updating specific fields
    $allowed_fields = ['title', 'category_id', 'description', 'short_description', 'price', 'price_type', 'duration', 'location_type', 'is_active'];
    
    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        return;
    }
    
    $query = "UPDATE services SET " . implode(', ', $fields) . " WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Service updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update service']);
    }
}

function handleDeleteRequest($service, $db) {
    // Check if user is authenticated
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        return;
    }
    
    $current_user = Auth::getCurrentUser();
    
    $service_id = (int)($_GET['id'] ?? 0);
    
    if (!$service_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Service ID is required']);
        return;
    }
    
    // Check if user owns this service or is admin
    $stmt = $db->prepare("SELECT provider_id FROM services WHERE id = :id");
    $stmt->execute([':id' => $service_id]);
    $service_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        return;
    }
    
    $is_owner = ($service_data['provider_id'] == $current_user['id']);
    $is_admin = ($current_user['user_type'] === 'admin' || $current_user['user_type'] === 'superadmin');
    
    if (!$is_owner && !$is_admin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    // Soft delete - set is_active to false
    $stmt = $db->prepare("UPDATE services SET is_active = 0 WHERE id = :id");
    
    if ($stmt->execute([':id' => $service_id])) {
        echo json_encode([
            'success' => true, 
            'message' => 'Service deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
    }
}
?>