<?php
/**
 * Setup Script for JobsMtaani Test Environment
 * This script sets up the database with sample data for testing
 */

// Database configuration
$host = 'localhost';
$db_name = 'jobsmtaanii';
$username = 'root';
$password = '';

echo "<!DOCTYPE html>
<html>
<head>
    <title>JobsMtaani Setup Environment</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'>JobsMtaani Test Environment Setup</h3>
                </div>
                <div class='card-body'>";

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='alert alert-info'>✓ Connected to MySQL server</div>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "<div class='alert alert-success'>✓ Database '$db_name' created or already exists</div>";
    
    // Select the database
    $pdo->exec("USE `$db_name`");
    
    // Create tables using the schema file
    $schema_file = 'database/jobsmtaani_enhanced_schema.sql';
    if (file_exists($schema_file)) {
        $sql = file_get_contents($schema_file);
        $pdo->exec($sql);
        echo "<div class='alert alert-success'>✓ Database schema applied successfully</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠ Schema file not found: $schema_file</div>";
    }
    
    // Insert sample data
    $sample_data_file = 'database/enhanced_sample_data.sql';
    if (file_exists($sample_data_file)) {
        $sql = file_get_contents($sample_data_file);
        // Split SQL into individual statements
        $statements = explode(';', $sql);
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    $success_count++;
                } catch (PDOException $e) {
                    // Ignore errors for duplicate entries or already existing data
                    $error_count++;
                }
            }
        }
        echo "<div class='alert alert-success'>✓ Sample data inserted ($success_count statements processed)</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠ Sample data file not found: $sample_data_file</div>";
    }
    
    // Test data verification
    echo "<h4 class='mt-4'>Data Verification</h4>";
    
    // Check users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    echo "<div class='alert alert-info'>Users in database: $userCount</div>";
    
    // Check services
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM services");
    $stmt->execute();
    $serviceCount = $stmt->fetchColumn();
    echo "<div class='alert alert-info'>Services in database: $serviceCount</div>";
    
    // Check bookings
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings");
    $stmt->execute();
    $bookingCount = $stmt->fetchColumn();
    echo "<div class='alert alert-info'>Bookings in database: $bookingCount</div>";
    
    // Display test accounts
    echo "<h4 class='mt-4'>Test Accounts</h4>";
    echo "<div class='alert alert-warning'>
            <strong>Customer Account:</strong><br>
            Email: john@example.com<br>
            Password: password123<br><br>
            <strong>Provider Account:</strong><br>
            Email: jane@example.com<br>
            Password: password123<br><br>
            <strong>Admin Account:</strong><br>
            Email: admin@example.com<br>
            Password: admin123
          </div>";
    
    echo "<div class='alert alert-success'>
            <h5>Setup Complete!</h5>
            <p>The test environment is ready. You can now:</p>
            <ul>
                <li><a href='login.php'>Login</a> with the test accounts above</li>
                <li><a href='register.php'>Register</a> a new account</li>
                <li><a href='test_all_features.php'>Run feature tests</a></li>
            </ul>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

echo "                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>";

?>