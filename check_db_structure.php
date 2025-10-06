<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get table structure
    $stmt = $db->prepare("DESCRIBE service_categories");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>service_categories table structure:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Add is_premium column if it doesn't exist
    $stmt = $db->prepare("SHOW COLUMNS FROM service_categories LIKE 'is_premium'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "<p>Adding is_premium column...</p>";
        $stmt = $db->prepare("ALTER TABLE service_categories ADD COLUMN is_premium BOOLEAN DEFAULT FALSE AFTER sort_order");
        $stmt->execute();
        echo "<p>is_premium column added successfully.</p>";
    } else {
        echo "<p>is_premium column already exists.</p>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>