<?php
/**
 * Authentication API Endpoints
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'POST':
        $action = $_GET['action'] ?? '';
        
        switch($action) {
            case 'login':
                if(isset($input['email']) && isset($input['password'])) {
                    if($user->login($input['email'], $input['password'])) {
                        $user_data = [
                            'id' => $user->id,
                            'username' => $user->username,
                            'email' => $user->email,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'user_type' => $user->user_type
                        ];
                        
                        Auth::login($user_data);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Login successful',
                            'user' => $user_data
                        ]);
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Invalid email or password'
                        ]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Email and password required'
                    ]);
                }
                break;
                
            case 'register':
                if(isset($input['username']) && isset($input['email']) && isset($input['password'])) {
                    $user->username = $input['username'];
                    $user->email = $input['email'];
                    $user->password_hash = Auth::hashPassword($input['password']);
                    $user->first_name = $input['first_name'] ?? '';
                    $user->last_name = $input['last_name'] ?? '';
                    $user->phone = $input['phone'] ?? '';
                    $user->user_type = $input['user_type'] ?? 'customer';
                    $user->status = 'active';
                    
                    if($user->create()) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'User registered successfully',
                            'user_id' => $user->id
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Registration failed'
                        ]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Username, email and password required'
                    ]);
                }
                break;
                
            case 'logout':
                Auth::logout();
                echo json_encode([
                    'success' => true,
                    'message' => 'Logged out successfully'
                ]);
                break;
                
            default:
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Action not found'
                ]);
        }
        break;
        
    case 'GET':
        if(Auth::isLoggedIn()) {
            echo json_encode([
                'success' => true,
                'user' => Auth::getCurrentUser()
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
}
?>
