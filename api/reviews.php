<?php
/**
 * Reviews API Endpoints
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
        $action = $_GET['action'] ?? 'list';
        
        switch($action) {
            case 'provider-reviews':
                if(!Auth::isLoggedIn()) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Authentication required']);
                    exit;
                }
                
                $user = Auth::getCurrentUser();
                if($user['user_type'] !== 'service_provider') {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Access denied']);
                    exit;
                }
                
                // Get reviews for provider's services
                $query = "SELECT r.*, 
                                 u1.first_name as reviewer_first_name, u1.last_name as reviewer_last_name, u1.email as reviewer_email,
                                 s.title as service_title
                          FROM reviews r
                          JOIN bookings b ON r.booking_id = b.id
                          JOIN services s ON b.service_id = s.id
                          JOIN users u1 ON r.reviewer_id = u1.id
                          WHERE s.provider_id = :provider_id
                          ORDER BY r.created_at DESC";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':provider_id', $user['id']);
                $stmt->execute();
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'reviews' => $reviews
                ]);
                break;
                
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Action not found']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
