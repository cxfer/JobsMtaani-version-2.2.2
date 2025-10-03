<?php
/**
 * Test script to verify dashboard functionality
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

// Test API endpoints
$endpoints = [
    'api/users.php?action=list&limit=1',
    'api/services.php?action=list&limit=1',
    'api/bookings.php?action=recent&limit=1'
];

foreach ($endpoints as $endpoint) {
    $url = "http://localhost/jobsmtaani/" . $endpoint;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "API endpoint $endpoint: SUCCESS\n";
        } else {
            echo "API endpoint $endpoint: FAILED - " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "API endpoint $endpoint: HTTP $httpCode\n";
    }
}

echo "Dashboard testing completed.\n";
?>