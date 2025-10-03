<?php
/**
 * User Profile API Endpoints
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
    case 'PUT':
        if(!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $user = Auth::getCurrentUser();
        
        // Update user profile
        $first_name = $_POST['first_name'] ?? $input['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? $input['last_name'] ?? '';
        $phone = $_POST['phone'] ?? $input['phone'] ?? '';
        $address = $_POST['address'] ?? $input['address'] ?? '';
        $city = $_POST['city'] ?? $input['city'] ?? '';
        $state = $_POST['state'] ?? $input['state'] ?? '';
        $postal_code = $_POST['postal_code'] ?? $input['postal_code'] ?? '';
        $business_name = $_POST['business_name'] ?? $input['business_name'] ?? '';
        $business_description = $_POST['business_description'] ?? $input['business_description'] ?? '';
        
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Update users table
            $query = "UPDATE users 
                      SET first_name = :first_name, last_name = :last_name, phone = :phone
                      WHERE id = :user_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            // Update user_profiles table
            $query = "UPDATE user_profiles 
                      SET address = :address, city = :city, state = :state, postal_code = :postal_code
                      WHERE user_id = :user_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':postal_code', $postal_code);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            // If user is a provider, update provider profile
            if ($user['user_type'] === 'service_provider') {
                $query = "UPDATE provider_profiles 
                          SET business_name = :business_name, business_description = :business_description
                          WHERE user_id = :user_id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':business_name', $business_name);
                $stmt->bindParam(':business_description', $business_description);
                $stmt->bindParam(':user_id', $user['id']);
                $stmt->execute();
            }
            
            // Commit transaction
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (Exception $e) {
            // Rollback transaction
            $db->rollback();
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>