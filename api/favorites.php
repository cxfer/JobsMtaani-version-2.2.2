<?php
/**
 * Favorites API Endpoints
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        if(!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $user = Auth::getCurrentUser();
        
        // Get user's favorite services
        $query = "SELECT f.id, f.service_id, f.created_at,
                         s.title, s.description, s.price, s.price_type,
                         c.name as category_name,
                         AVG(r.rating) as avg_rating, COUNT(r.id) as review_count,
                         s.images
                  FROM favorites f
                  JOIN services s ON f.service_id = s.id
                  JOIN service_categories c ON s.category_id = c.id
                  LEFT JOIN bookings b ON s.id = b.service_id
                  LEFT JOIN reviews r ON b.id = r.booking_id
                  WHERE f.user_id = :user_id AND s.is_active = 1
                  GROUP BY f.id, f.service_id, f.created_at, s.title, s.description, s.price, s.price_type, c.name, s.images
                  ORDER BY f.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process images for each favorite
        foreach ($favorites as &$favorite) {
            if ($favorite['images']) {
                $favorite['images'] = json_decode($favorite['images'], true);
            } else {
                $favorite['images'] = [];
            }
        }
        
        echo json_encode([
            'success' => true,
            'favorites' => $favorites
        ]);
        break;
        
    case 'POST':
        if(!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $user = Auth::getCurrentUser();
        $service_id = $_GET['service_id'] ?? 0;
        
        if (!$service_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            exit;
        }
        
        // Check if service exists and is active
        $query = "SELECT id FROM services WHERE id = :service_id AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Service not found or inactive']);
            exit;
        }
        
        // Check if already favorited
        $query = "SELECT id FROM favorites WHERE user_id = :user_id AND service_id = :service_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Remove from favorites
            $query = "DELETE FROM favorites WHERE user_id = :user_id AND service_id = :service_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':service_id', $service_id);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Service removed from favorites',
                'favorited' => false
            ]);
        } else {
            // Add to favorites
            $query = "INSERT INTO favorites (user_id, service_id) VALUES (:user_id, :service_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':service_id', $service_id);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Service added to favorites',
                'favorited' => true
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>