<?php
require_once __DIR__ . '/../config/database.php';

class AppSettings {
    private $db;
    private static $settings = null;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->loadSettings();
    }
    
    private function loadSettings() {
        if (self::$settings === null) {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM system_settings");
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // Default settings
            $defaults = [
                'app_name' => 'JobsMtaani',
                'app_logo' => '/assets/images/logo.png',
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'accent_color' => '#28a745',
                'theme_mode' => 'light',
                'currency' => 'KES',
                'timezone' => 'Africa/Nairobi',
                'maintenance_mode' => '0',
                'registration_enabled' => '1',
                'email_verification' => '1',
                'booking_approval' => '0',
                'commission_rate' => '10',
                'contact_email' => 'info@jobsmtaani.com',
                'contact_phone' => '+254700000000',
                'facebook_url' => '',
                'twitter_url' => '',
                'instagram_url' => '',
                'linkedin_url' => ''
            ];
            
            self::$settings = array_merge($defaults, $settings);
        }
    }
    
    public static function get($key, $default = null) {
        $instance = new self();
        return self::$settings[$key] ?? $default;
    }
    
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO system_settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        $stmt->execute([$key, $value, $value]);
        
        // Update cached settings
        self::$settings[$key] = $value;
        
        return true;
    }
    
    public function getAll() {
        return self::$settings;
    }
    
    public function generateCSS() {
        $primaryColor = self::get('primary_color', '#667eea');
        $secondaryColor = self::get('secondary_color', '#764ba2');
        $accentColor = self::get('accent_color', '#28a745');
        
        return "
        :root {
            --primary-color: {$primaryColor};
            --secondary-color: {$secondaryColor};
            --accent-color: {$accentColor};
            --primary-rgb: " . $this->hexToRgb($primaryColor) . ";
            --secondary-rgb: " . $this->hexToRgb($secondaryColor) . ";
            --accent-rgb: " . $this->hexToRgb($accentColor) . ";
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            border-color: var(--secondary-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        }
        ";
    }
    
    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return implode(', ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ]);
    }
}
?>
