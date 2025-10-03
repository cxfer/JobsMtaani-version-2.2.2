<?php
/**
 * Settings API Endpoints
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Settings.php';
require_once '../classes/Auth.php';

// Require admin access
Auth::requireRole(['admin', 'superadmin']);

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$settings = new Settings($db);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        $allSettings = $settings->getForAdmin();
        echo json_encode([
            'success' => true,
            'settings' => $allSettings
        ]);
        break;
        
    case 'POST':
        if ($settings->updateMultiple($input)) {
            echo json_encode([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update settings'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
