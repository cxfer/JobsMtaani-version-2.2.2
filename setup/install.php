<?php
include 'database.php';

class DatabaseInstaller {
    private $db;
    
    public function __construct() {
        try {
            $this->db = (new Database())->getConnection();
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function install() {
        echo "<h2>JobsMtaani Database Installation</h2>";
        
        // Check if tables exist
        if ($this->tablesExist()) {
            echo "<p style='color: orange;'>Database tables already exist. Skipping table creation.</p>";
        } else {
            echo "<p>Creating database tables...</p>";
            $this->createTables();
        }
        
        // Insert default settings
        echo "<p>Setting up default configuration...</p>";
        $this->insertDefaultSettings();
        
        // Create default admin user
        echo "<p>Creating default admin user...</p>";
        $this->createDefaultAdmin();
        
        echo "<p style='color: green;'><strong>Installation completed successfully!</strong></p>";
        echo "<p>Default Admin Login:</p>";
        echo "<ul>";
        echo "<li>Username: admin</li>";
        echo "<li>Password: admin123</li>";
        echo "<li>Email: admin@jobsmtaani.com</li>";
        echo "</ul>";
        echo "<p><a href='../admin/'>Go to Admin Panel</a></p>";
    }
    
    private function tablesExist() {
        try {
            $stmt = $this->db->query("SELECT 1 FROM system_settings LIMIT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function createTables() {
        $schemaFile = '../database/jobsmtaani_schema.sql';
        if (!file_exists($schemaFile)) {
            die("Schema file not found: " . $schemaFile);
        }
        
        $sql = file_get_contents($schemaFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                try {
                    $this->db->exec($statement);
                } catch (Exception $e) {
                    echo "<p style='color: red;'>Error executing: " . substr($statement, 0, 50) . "...</p>";
                    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    private function insertDefaultSettings() {
        $defaultSettings = [
            ['app_name', 'JobsMtaani', 'text', 'Application name'],
            ['app_logo', '/assets/images/logo.png', 'text', 'Application logo path'],
            ['primary_color', '#667eea', 'color', 'Primary theme color'],
            ['secondary_color', '#764ba2', 'color', 'Secondary theme color'],
            ['accent_color', '#28a745', 'color', 'Accent color'],
            ['theme_mode', 'light', 'text', 'Theme mode (light/dark)'],
            ['currency', 'KES', 'text', 'Default currency'],
            ['timezone', 'Africa/Nairobi', 'text', 'Default timezone'],
            ['maintenance_mode', '0', 'boolean', 'Maintenance mode status'],
            ['registration_enabled', '1', 'boolean', 'User registration enabled'],
            ['email_verification', '1', 'boolean', 'Email verification required'],
            ['booking_approval', '0', 'boolean', 'Booking approval required'],
            ['commission_rate', '10', 'number', 'Platform commission rate (%)'],
            ['contact_email', 'info@jobsmtaani.com', 'text', 'Contact email'],
            ['contact_phone', '+254700000000', 'text', 'Contact phone'],
            ['facebook_url', '', 'text', 'Facebook page URL'],
            ['twitter_url', '', 'text', 'Twitter profile URL'],
            ['instagram_url', '', 'text', 'Instagram profile URL'],
            ['linkedin_url', '', 'text', 'LinkedIn profile URL']
        ];
        
        $stmt = $this->db->prepare("
            INSERT INTO system_settings (setting_key, setting_value, setting_type, description) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type),
                description = VALUES(description)
        ");
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }
    
    private function createDefaultAdmin() {
        // Check if admin already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute(['admin', 'admin@jobsmtaani.com']);
        
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: orange;'>Default admin user already exists.</p>";
            return;
        }
        
        // Create admin user
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, status, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute([
            'admin',
            'admin@jobsmtaani.com',
            $passwordHash,
            'System',
            'Administrator',
            'superadmin',
            'active',
            1
        ]);
        
        $adminId = $this->db->lastInsertId();
        
        // Create admin profile
        $stmt = $this->db->prepare("
            INSERT INTO user_profiles (user_id, bio, city, country) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $adminId,
            'System Administrator',
            'Nairobi',
            'Kenya'
        ]);
    }
}

// Run installation if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'install.php') {
    $installer = new DatabaseInstaller();
    $installer->install();
}
?>
