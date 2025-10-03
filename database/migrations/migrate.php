<?php
// Database Migration Runner
// Run this script to apply all pending migrations

require_once '../config/config.php';

class MigrationRunner {
    private $pdo;
    private $migrationsPath;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->migrationsPath = __DIR__;
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT PRIMARY KEY AUTO_INCREMENT,
            migration_name VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration_name)
        )";
        $this->pdo->exec($sql);
    }
    
    public function runMigrations() {
        $executedMigrations = $this->getExecutedMigrations();
        $migrationFiles = $this->getMigrationFiles();
        
        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.sql');
            
            if (!in_array($migrationName, $executedMigrations)) {
                echo "Running migration: $migrationName\n";
                $this->executeMigration($file, $migrationName);
                echo "Completed migration: $migrationName\n";
            } else {
                echo "Skipping already executed migration: $migrationName\n";
            }
        }
        
        echo "All migrations completed!\n";
    }
    
    private function getExecutedMigrations() {
        $stmt = $this->pdo->query("SELECT migration_name FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function getMigrationFiles() {
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        return $files;
    }
    
    private function executeMigration($file, $migrationName) {
        try {
            $sql = file_get_contents($file);
            
            // Split by semicolon and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            $this->pdo->beginTransaction();
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    $this->pdo->exec($statement);
                }
            }
            
            // Record migration as executed
            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
            $stmt->execute([$migrationName]);
            
            $this->pdo->commit();
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }
    
    public function rollbackMigration($migrationName) {
        // This would require rollback scripts - implement as needed
        echo "Rollback functionality not implemented yet\n";
    }
}

// Run migrations if called directly
if (php_sapi_name() === 'cli') {
    try {
        $migrationRunner = new MigrationRunner($pdo);
        $migrationRunner->runMigrations();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>
