<?php
/**
 * Test script to verify all dashboard functionality
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Service.php';
require_once 'classes/Booking.php';

// Start session
Auth::startSession();

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    echo "User is not logged in. Please log in first.\n";
    exit;
}

// Get current user
$user = Auth::getCurrentUser();
echo "Current user: " . $user['first_name'] . " " . $user['last_name'] . " (" . $user['user_type'] . ")\n";

// Test database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "Database connection successful.\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test user class
try {
    $userObj = new User($db);
    echo "User class instantiated successfully.\n";
} catch (Exception $e) {
    echo "User class instantiation failed: " . $e->getMessage() . "\n";
    exit;
}

// Test service class
try {
    $serviceObj = new Service($db);
    echo "Service class instantiated successfully.\n";
} catch (Exception $e) {
    echo "Service class instantiation failed: " . $e->getMessage() . "\n";
    exit;
}

// Test booking class
try {
    $bookingObj = new Booking($db);
    echo "Booking class instantiated successfully.\n";
} catch (Exception $e) {
    echo "Booking class instantiation failed: " . $e->getMessage() . "\n";
    exit;
}

// Test API endpoints based on user type
$userType = $user['user_type'];
echo "Testing API endpoints for user type: " . $userType . "\n";

$endpoints = [];

switch($userType) {
    case 'admin':
    case 'superadmin':
        $endpoints = [
            'api/users.php?action=all-users&limit=1',
            'api/services.php?action=all-services&limit=1',
            'api/bookings.php?action=recent&limit=1'
        ];
        break;
        
    case 'customer':
        $endpoints = [
            'api/bookings.php?action=customer-bookings&limit=1',
            'api/favorites.php',
            'api/notifications.php'
        ];
        break;
        
    case 'service_provider':
        $endpoints = [
            'api/services.php?action=my-services',
            'api/bookings.php?action=my-bookings&limit=1',
            'api/availability.php',
            'api/reviews.php?action=provider-reviews'
        ];
        break;
        
    default:
        echo "Unknown user type: " . $userType . "\n";
        exit;
}

foreach ($endpoints as $endpoint) {
    $url = "http://localhost/jobsmtaani/" . $endpoint;
    echo "Testing endpoint: " . $endpoint . "\n";
    
    // Use file_get_contents for testing
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json',
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        echo "  Failed to connect to endpoint\n";
        continue;
    }
    
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "  SUCCESS: " . ($data['success'] ? 'OK' : ($data['message'] ?? 'Unknown error')) . "\n";
    } else {
        echo "  ERROR: Invalid response format\n";
    }
}

echo "Dashboard testing completed.\n";
?>