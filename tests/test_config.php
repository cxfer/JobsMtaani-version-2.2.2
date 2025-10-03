<?php
/**
 * Test Configuration for JobsMtaani Platform
 * PHPUnit test setup and utilities
 */

// Test database configuration
define('TEST_DB_HOST', 'localhost');
define('TEST_DB_NAME', 'jobsmtaani_test');
define('TEST_DB_USER', 'root');
define('TEST_DB_PASS', '');

// Test application settings
define('TEST_APP_NAME', 'JobsMtaani Test');
define('TEST_APP_URL', 'http://localhost/jobsmtaani');
define('TEST_UPLOAD_PATH', 'tests/uploads/');

// Test security settings
define('TEST_JWT_SECRET', 'test-jwt-secret-key');
define('TEST_PASSWORD_SALT', 'test-password-salt');

class TestDatabase {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . TEST_DB_HOST . ";dbname=" . TEST_DB_NAME,
                TEST_DB_USER,
                TEST_DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $e) {
            die("Test database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function resetDatabase() {
        // Drop all tables and recreate schema for clean tests
        $tables = ['users', 'services', 'bookings', 'payments', 'reviews', 'notifications'];
        
        foreach ($tables as $table) {
            $this->connection->exec("DROP TABLE IF EXISTS $table");
        }
        
        // Recreate schema
        $schemaFile = __DIR__ . '/../database/jobsmtaani_schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                    $this->connection->exec($statement);
                }
            }
        }
    }
    
    public function seedTestData() {
        // Insert test users
        $stmt = $this->connection->prepare("
            INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, status, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $testUsers = [
            ['test_admin', 'admin@test.com', password_hash('test123', PASSWORD_DEFAULT), 'Test', 'Admin', 'admin', 'active', 1],
            ['test_provider', 'provider@test.com', password_hash('test123', PASSWORD_DEFAULT), 'Test', 'Provider', 'service_provider', 'active', 1],
            ['test_customer', 'customer@test.com', password_hash('test123', PASSWORD_DEFAULT), 'Test', 'Customer', 'customer', 'active', 1]
        ];
        
        foreach ($testUsers as $user) {
            $stmt->execute($user);
        }
    }
}

// Test helper functions
function createTestUser($type = 'customer', $data = []) {
    $db = TestDatabase::getInstance()->getConnection();
    
    $defaults = [
        'username' => 'test_' . $type . '_' . uniqid(),
        'email' => $type . '_' . uniqid() . '@test.com',
        'password_hash' => password_hash('test123', PASSWORD_DEFAULT),
        'first_name' => 'Test',
        'last_name' => ucfirst($type),
        'user_type' => $type,
        'status' => 'active',
        'email_verified' => 1
    ];
    
    $userData = array_merge($defaults, $data);
    
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, status, email_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute(array_values($userData));
    
    return $db->lastInsertId();
}

function createTestService($providerId, $data = []) {
    $db = TestDatabase::getInstance()->getConnection();
    
    $defaults = [
        'provider_id' => $providerId,
        'category_id' => 1,
        'title' => 'Test Service ' . uniqid(),
        'slug' => 'test-service-' . uniqid(),
        'description' => 'Test service description',
        'short_description' => 'Test service',
        'price' => 1000.00,
        'price_type' => 'fixed',
        'duration' => 60,
        'location_type' => 'both',
        'is_active' => 1
    ];
    
    $serviceData = array_merge($defaults, $data);
    
    $stmt = $db->prepare("
        INSERT INTO services (provider_id, category_id, title, slug, description, short_description, price, price_type, duration, location_type, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute(array_values($serviceData));
    
    return $db->lastInsertId();
}
?>
