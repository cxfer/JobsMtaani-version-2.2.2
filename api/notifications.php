<?php
/**
 * Notifications API Endpoints
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
        
        // Get user's notifications
        $query = "SELECT id, title, message, type, is_read, action_url, created_at
                  FROM notifications 
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications
        ]);
        break;
        
    case 'PUT':
        if(!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $user = Auth::getCurrentUser();
        $action = $_GET['action'] ?? '';
        
        if ($action === 'mark-all-read') {
            // Mark all notifications as read
            $query = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } else {
            // Mark specific notification as read
            $notification_id = $_GET['id'] ?? 0;
            
            if (!$notification_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
                exit;
            }
            
            // Check if notification belongs to user
            $query = "UPDATE notifications 
                      SET is_read = :is_read 
                      WHERE id = :id AND user_id = :user_id";
            
            $is_read = $input['is_read'] ?? true;
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':is_read', $is_read, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $notification_id);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Notification updated successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Notification not found'
                ]);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>