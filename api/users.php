<?php
/**
 * Users API
 * Handles user operations for the JobsMtaani platform
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';

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
$user = new User($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            handleGetRequest($user, $db);
            break;
            
        case 'POST':
            handlePostRequest($user, $db);
            break;
            
        case 'PUT':
            handlePutRequest($user, $db);
            break;
            
        case 'DELETE':
            handleDeleteRequest($user, $db);
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

function handleGetRequest($user, $db) {
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'profile':
            // Check if user is authenticated
            if (!Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            $current_user = Auth::getCurrentUser();
            
            // Get user profile with additional details
            $stmt = $db->prepare("SELECT u.*, up.bio, up.address, up.city, up.phone_verified, up.email_verified,
                                         up.date_of_birth, up.gender, up.profile_image
                                  FROM users u
                                  LEFT JOIN user_profiles up ON u.id = up.user_id
                                  WHERE u.id = :id");
            $stmt->execute([':id' => $current_user['id']]);
            
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }
            
            // Remove sensitive information
            unset($user_data['password_hash']);
            
            echo json_encode(['success' => true, 'user' => $user_data]);
            break;
            
        case 'details':
            // Check if user is authenticated
            if (!Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            $current_user = Auth::getCurrentUser();
            
            // Only admins can get other user details
            if ($current_user['user_type'] !== 'admin' && $current_user['user_type'] !== 'superadmin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            
            $user_id = (int)($_GET['id'] ?? 0);
            if (!$user_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
                return;
            }
            
            // Get user details
            $stmt = $db->prepare("SELECT u.*, up.bio, up.address, up.city, up.phone_verified, up.email_verified,
                                         up.date_of_birth, up.gender, up.profile_image
                                  FROM users u
                                  LEFT JOIN user_profiles up ON u.id = up.user_id
                                  WHERE u.id = :id");
            $stmt->execute([':id' => $user_id]);
            
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }
            
            // Remove sensitive information
            unset($user_data['password_hash']);
            
            echo json_encode(['success' => true, 'user' => $user_data]);
            break;
            
        default:
            // Check if user is authenticated
            if (!Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            $current_user = Auth::getCurrentUser();
            
            // Only admins can list users
            if ($current_user['user_type'] !== 'admin' && $current_user['user_type'] !== 'superadmin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            
            // Get user list
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            $user_type = $_GET['user_type'] ?? null;
            $status = $_GET['status'] ?? null;
            $search = $_GET['search'] ?? null;
            
            $users = $user->getAllUsers($limit, $offset, $user_type, $status, $search);
            
            // Remove sensitive information
            foreach ($users as &$u) {
                unset($u['password_hash']);
            }
            
            echo json_encode(['success' => true, 'users' => $users]);
            break;
    }
}

function handlePostRequest($user, $db) {
    $action = $_GET['action'] ?? 'create';
    
    switch ($action) {
        case 'register':
            // Public registration
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
                return;
            }
            
            // Validate required fields
            $required_fields = ['first_name', 'last_name', 'email', 'password', 'user_type'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                    return;
                }
            }
            
            // Validate user type
            $valid_user_types = ['customer', 'service_provider'];
            if (!in_array($data['user_type'], $valid_user_types)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user type']);
                return;
            }
            
            // Check if email already exists
            if ($user->emailExists($data['email'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                return;
            }
            
            // Hash password
            $password_hash = Auth::hashPassword($data['password']);
            
            // Create user
            $user_data = [
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $password_hash,
                'user_type' => $data['user_type'],
                'status' => 'active',
                'phone' => $data['phone'] ?? '',
                'subscription_plan' => 'free'
            ];
            
            $user_id = $user->createUser($user_data);
            
            if ($user_id) {
                // Generate email verification token
                $token = bin2hex(random_bytes(32));
                $user->setVerificationToken($user_id, $token);
                
                // Send verification email (in a real app, you would send an actual email)
                $verification_link = APP_URL . "/verify.php?token=" . $token;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'User registered successfully. Please check your email for verification.',
                    'user_id' => $user_id,
                    'verification_link' => $verification_link // For testing purposes
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to register user']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function handlePutRequest($user, $db) {
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
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }
    
    $user_id = (int)$data['id'];
    
    // Check permissions
    $is_owner = ($user_id == $current_user['id']);
    $is_admin = ($current_user['user_type'] === 'admin' || $current_user['user_type'] === 'superadmin');
    
    if (!$is_owner && !$is_admin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    // Handle different actions
    $action = $_GET['action'] ?? 'update';
    
    switch ($action) {
        case 'update':
            // Update user profile
            $fields = [];
            $params = [':id' => $user_id];
            
            // Only allow updating specific fields
            $allowed_fields = ['first_name', 'last_name', 'phone', 'bio', 'address', 'city', 'date_of_birth', 'gender'];
            
            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    if (in_array($field, ['first_name', 'last_name', 'phone', 'city', 'address'])) {
                        $fields[] = "u.$field = :$field";
                    } else {
                        $fields[] = "up.$field = :$field";
                    }
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                return;
            }
            
            // Split fields for users and user_profiles tables
            $user_fields = [];
            $profile_fields = [];
            
            foreach ($fields as $field) {
                if (strpos($field, 'u.') === 0) {
                    $user_fields[] = str_replace('u.', '', $field);
                } else {
                    $profile_fields[] = str_replace('up.', '', $field);
                }
            }
            
            // Update user table
            if (!empty($user_fields)) {
                $user_query = "UPDATE users SET " . implode(', ', array_map(function($f) { return "$f = :$f"; }, $user_fields)) . " WHERE id = :id";
                $user_stmt = $db->prepare($user_query);
                $user_params = array_filter($params, function($key) use ($user_fields) {
                    return in_array(str_replace(':', '', $key), $user_fields) || $key === ':id';
                }, ARRAY_FILTER_USE_KEY);
                $user_stmt->execute($user_params);
            }
            
            // Update user_profiles table
            if (!empty($profile_fields)) {
                $profile_query = "UPDATE user_profiles SET " . implode(', ', array_map(function($f) { return "$f = :$f"; }, $profile_fields)) . " WHERE user_id = :id";
                $profile_stmt = $db->prepare($profile_query);
                $profile_params = array_filter($params, function($key) use ($profile_fields) {
                    return in_array(str_replace(':', '', $key), $profile_fields) || $key === ':id';
                }, ARRAY_FILTER_USE_KEY);
                $profile_stmt->execute($profile_params);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Profile updated successfully'
            ]);
            break;
            
        case 'update-status':
            // Only admins can update user status
            if (!$is_admin) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            
            if (!isset($data['status'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status is required']);
                return;
            }
            
            $valid_statuses = ['active', 'inactive', 'suspended'];
            if (!in_array($data['status'], $valid_statuses)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }
            
            if ($user->updateStatus($user_id, $data['status'])) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'User status updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
            }
            break;
            
        case 'change-password':
            // Only owner can change password
            if (!$is_owner) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            
            if (!isset($data['current_password']) || !isset($data['new_password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Current and new passwords are required']);
                return;
            }
            
            // Verify current password
            $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user_data || !Auth::verifyPassword($data['current_password'], $user_data['password_hash'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                return;
            }
            
            // Hash new password
            $new_password_hash = Auth::hashPassword($data['new_password']);
            
            // Update password
            $stmt = $db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
            if ($stmt->execute([':password_hash' => $new_password_hash, ':id' => $user_id])) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Password changed successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to change password']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function handleDeleteRequest($user, $db) {
    // Not implemented for security reasons - use status update instead
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Use PUT request with action=update-status to deactivate users']);
}
?>