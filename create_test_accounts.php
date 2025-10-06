<?php
/**
 * Create Test Accounts Script for JobsMtaani
 * This script creates test accounts for customer, provider, and admin roles
 */

require_once 'config/database.php';
require_once 'classes/Auth.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Test Accounts</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'>Create Test Accounts</h3>
                </div>
                <div class='card-body'>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Failed to connect to database");
    }
    
    echo "<div class='alert alert-success'>✓ Database connection established</div>";
    
    // Hash password for all test accounts
    $hashedPassword = Auth::hashPassword('password123');
    $adminPassword = Auth::hashPassword('admin123');
    
    // Test accounts data
    $accounts = [
        [
            'email' => 'john.customer@example.com',
            'first_name' => 'John',
            'last_name' => 'Customer',
            'phone' => '+254700111222',
            'user_type' => 'customer',
            'password' => $hashedPassword,
            'status' => 'active'
        ],
        [
            'email' => 'jane.provider@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Provider',
            'phone' => '+254700333444',
            'user_type' => 'service_provider',
            'password' => $hashedPassword,
            'status' => 'active'
        ],
        [
            'email' => 'admin@example.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '+254700555666',
            'user_type' => 'admin',
            'password' => $adminPassword,
            'status' => 'active'
        ]
    ];
    
    $createdAccounts = 0;
    
    foreach ($accounts as $account) {
        // Check if user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$account['email']]);
        
        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-warning'>⚠ Account {$account['email']} already exists</div>";
            continue;
        }
        
        // Create user
        $stmt = $db->prepare("INSERT INTO users (email, first_name, last_name, phone, user_type, password_hash, status, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $account['email'],
            $account['first_name'],
            $account['last_name'],
            $account['phone'],
            $account['user_type'],
            $account['password'],
            $account['status'],
            1 // email_verified
        ]);
        
        if ($result) {
            $userId = $db->lastInsertId();
            
            // Create user profile
            $stmt = $db->prepare("INSERT INTO user_profiles (user_id, bio, address, city, country) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                "Test {$account['user_type']} account",
                "Test Address, Nairobi",
                "Nairobi",
                "Kenya"
            ]);
            
            // If provider, create provider profile
            if ($account['user_type'] === 'service_provider') {
                $stmt = $db->prepare("INSERT INTO provider_profiles (user_id, business_name, business_description, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $userId,
                    "{$account['first_name']}'s Services",
                    "Test service provider business",
                    "approved"
                ]);
            }
            
            echo "<div class='alert alert-success'>✓ Created {$account['user_type']} account: {$account['email']}</div>";
            $createdAccounts++;
        } else {
            echo "<div class='alert alert-danger'>✗ Failed to create account: {$account['email']}</div>";
        }
    }
    
    echo "<div class='alert alert-info mt-4'>
            <h5>Test Accounts Summary</h5>
            <p>Created $createdAccounts new test accounts.</p>
            <p><strong>Login Credentials:</strong></p>
            <ul>
                <li><strong>Customer:</strong> john.customer@example.com / password123</li>
                <li><strong>Provider:</strong> jane.provider@example.com / password123</li>
                <li><strong>Admin:</strong> admin@example.com / admin123</li>
            </ul>
            <p>You can now <a href='login.php'>login</a> with these accounts to test the platform.</p>
          </div>";
    
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