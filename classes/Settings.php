<?php
/**
 * Settings Class
 * Handles system settings management
 */

require_once __DIR__ . '/../config/database.php';

class Settings {
    private $conn;
    private $table_name = "system_settings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get setting value
    public function get($key, $default = null) {
        $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = :key LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['setting_value'];
        }
        return $default;
    }

    // Set setting value
    public function set($key, $value, $type = 'text', $description = '') {
        $query = "INSERT INTO " . $this->table_name . " 
                  (setting_key, setting_value, setting_type, description) 
                  VALUES (:key, :value, :type, :description)
                  ON DUPLICATE KEY UPDATE 
                  setting_value = :value, setting_type = :type, description = :description";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        
        return $stmt->execute();
    }

    // Get all settings
    public function getAll($public_only = false) {
        $where_clause = $public_only ? "WHERE is_public = 1" : "";
        
        $query = "SELECT * FROM " . $this->table_name . " " . $where_clause . " ORDER BY setting_key";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $settings = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }

    // Get settings for admin panel
    public function getForAdmin() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY setting_key";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update multiple settings
    public function updateMultiple($settings) {
        $this->conn->beginTransaction();
        
        try {
            foreach($settings as $key => $value) {
                $this->set($key, $value);
            }
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>
