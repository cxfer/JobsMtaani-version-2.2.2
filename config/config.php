<?php
/**
 * Application Configuration
 * JobsMtaani Platform
 */
// Session settings
//ini_set('session.cookie_httponly', 1);
//ini_set('session.use_only_cookies', 1);
//ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'jobsmtaani');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('APP_NAME', 'JobsMtaani');
define('APP_URL', 'http://localhost/jobsmtaani');
define('UPLOAD_PATH', 'uploads/');

// Security settings
define('JWT_SECRET', 'your-secret-key-here');
define('PASSWORD_SALT', 'your-salt-here');



// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Nairobi');

// CORS headers for API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
?>
