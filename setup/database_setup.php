<?php
// Complete Database Setup Script
// This script sets up the entire database with initial data

require_once '../config/config.php';

class DatabaseSetup {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function setupDatabase() {
        echo "Starting database setup...\n";
        
        // Run main schema
        $this->runSQLFile('../database/jobsmtaani_schema.sql');
        echo "✓ Main schema created\n";
        
        // Run migrations
        require_once '../database/migrations/migrate.php';
        $migrationRunner = new MigrationRunner($this->pdo);
        $migrationRunner->runMigrations();
        echo "✓ Migrations completed\n";
        
        // Insert initial data
        $this->insertInitialData();
        echo "✓ Initial data inserted\n";
        
        // Insert sample data
        $this->insertSampleData();
        echo "✓ Sample data inserted\n";
        
        echo "Database setup completed successfully!\n";
    }
    
    private function runSQLFile($filePath) {
        $sql = file_get_contents($filePath);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                $this->pdo->exec($statement);
            }
        }
    }
    
    private function insertInitialData() {
        // Insert default admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, user_type, status, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute(['admin', 'admin@jobsmtaani.com', $adminPassword, 'System', 'Administrator', 'superadmin', 'active', 1]);
        
        // Insert default service categories
        $categories = [
            ['Home Cleaning', 'home-cleaning', 'Professional home cleaning services', 'home'],
            ['Plumbing', 'plumbing', 'Plumbing repair and installation services', 'wrench'],
            ['Electrical', 'electrical', 'Electrical repair and installation services', 'zap'],
            ['Gardening', 'gardening', 'Garden maintenance and landscaping services', 'leaf'],
            ['Carpentry', 'carpentry', 'Furniture and woodwork services', 'hammer'],
            ['Painting', 'painting', 'Interior and exterior painting services', 'brush'],
            ['Tutoring', 'tutoring', 'Educational tutoring services', 'book'],
            ['Beauty & Wellness', 'beauty-wellness', 'Beauty and wellness services', 'heart']
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO service_categories (name, slug, description, icon) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
        
        // Insert subscription plans
        $plans = [
            ['Basic', 'basic', 'Perfect for getting started', 0.00, 'monthly', '["Up to 3 services", "Basic support", "Standard listing"]', 3, 10, 15.00],
            ['Professional', 'professional', 'For growing service providers', 29.99, 'monthly', '["Up to 15 services", "Priority support", "Featured listing", "Analytics"]', 15, 50, 10.00],
            ['Business', 'business', 'For established businesses', 79.99, 'monthly', '["Unlimited services", "24/7 support", "Premium listing", "Advanced analytics", "Custom branding"]', null, null, 5.00]
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO subscription_plans (name, slug, description, price, billing_cycle, features, max_services, max_bookings_per_month, commission_rate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($plans as $plan) {
            $stmt->execute($plan);
        }
        
        // Insert system settings
        $settings = [
            ['site_name', 'JobsMtaani', 'text', 'Website name'],
            ['site_description', 'Your trusted local service marketplace', 'text', 'Website description'],
            ['contact_email', 'info@jobsmtaani.com', 'text', 'Contact email address'],
            ['contact_phone', '+254700000000', 'text', 'Contact phone number'],
            ['currency', 'KES', 'text', 'Default currency'],
            ['timezone', 'Africa/Nairobi', 'text', 'Default timezone'],
            ['commission_rate', '10.00', 'number', 'Default commission rate percentage'],
            ['maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'],
            ['allow_registration', 'true', 'boolean', 'Allow new user registration'],
            ['email_verification_required', 'true', 'boolean', 'Require email verification']
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }
    }
    
    private function insertSampleData() {
        // Insert sample service provider
        $providerPassword = password_hash('provider123', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute(['john_cleaner', 'john@example.com', $providerPassword, 'John', 'Doe', '+254712345678', 'service_provider', 'active', 1]);
        
        $providerId = $this->pdo->lastInsertId();
        
        // Insert sample customer
        $customerPassword = password_hash('customer123', PASSWORD_DEFAULT);
        $stmt->execute(['jane_customer', 'jane@example.com', $customerPassword, 'Jane', 'Smith', '+254787654321', 'customer', 'active', 1]);
        
        // Insert sample service
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO services (provider_id, category_id, title, slug, description, short_description, price, price_type, duration, location_type, is_active, featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $providerId, 1, 'Professional House Cleaning', 'professional-house-cleaning',
            'Complete house cleaning service including all rooms, kitchen, and bathrooms. We use eco-friendly products and professional equipment.',
            'Professional house cleaning with eco-friendly products',
            2500.00, 'fixed', 180, 'at_customer', 1, 1
        ]);
    }
}

// Run setup if called directly
if (php_sapi_name() === 'cli') {
    try {
        $setup = new DatabaseSetup($pdo);
        $setup->setupDatabase();
    } catch (Exception $e) {
        echo "Setup Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>
