<?php
/**
 * Provider Availability API Endpoints
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
        if($user['user_type'] !== 'service_provider') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        // Get provider's availability
        $query = "SELECT id, day_of_week, start_time, end_time, is_available
                  FROM provider_availability 
                  WHERE provider_id = :provider_id
                  ORDER BY day_of_week";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':provider_id', $user['id']);
        $stmt->execute();
        $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'availability' => $availability
        ]);
        break;
        
    case 'POST':
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
        
        $day_of_week = $input['day_of_week'] ?? 0;
        $start_time = $input['start_time'] ?? '09:00';
        $end_time = $input['end_time'] ?? '17:00';
        $is_available = $input['is_available'] ?? true;
        
        // Check if availability record exists
        $query = "SELECT id FROM provider_availability 
                  WHERE provider_id = :provider_id AND day_of_week = :day_of_week";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':provider_id', $user['id']);
        $stmt->bindParam(':day_of_week', $day_of_week);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Update existing record
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $query = "UPDATE provider_availability 
                      SET start_time = :start_time, end_time = :end_time, is_available = :is_available
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $row['id']);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':is_available', $is_available, PDO::PARAM_BOOL);
            $stmt->execute();
        } else {
            // Insert new record
            $query = "INSERT INTO provider_availability 
                      (provider_id, day_of_week, start_time, end_time, is_available)
                      VALUES (:provider_id, :day_of_week, :start_time, :end_time, :is_available)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':provider_id', $user['id']);
            $stmt->bindParam(':day_of_week', $day_of_week);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':is_available', $is_available, PDO::PARAM_BOOL);
            $stmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Availability updated successfully'
        ]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>