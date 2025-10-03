-- Migration: Add advanced platform features
-- Date: 2024-01-16

-- File uploads table
CREATE TABLE file_uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_type ENUM('image', 'document', 'video', 'audio', 'other') NOT NULL,
    related_table VARCHAR(50), -- services, users, bookings, etc.
    related_id INT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Search history table
CREATE TABLE search_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    search_query VARCHAR(500) NOT NULL,
    search_type ENUM('services', 'providers', 'general') DEFAULT 'general',
    results_count INT DEFAULT 0,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Popular searches table
CREATE TABLE popular_searches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    search_term VARCHAR(200) NOT NULL,
    search_count INT DEFAULT 1,
    last_searched TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_search_term (search_term)
);

-- Service views/analytics table
CREATE TABLE service_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Platform analytics table
CREATE TABLE platform_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(15, 2) NOT NULL,
    metric_date DATE NOT NULL,
    additional_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_metric_date (metric_name, metric_date)
);

-- Create indexes
CREATE INDEX idx_file_uploads_user ON file_uploads(user_id);
CREATE INDEX idx_file_uploads_related ON file_uploads(related_table, related_id);
CREATE INDEX idx_search_history_user ON search_history(user_id);
CREATE INDEX idx_service_views_service ON service_views(service_id);
CREATE INDEX idx_service_views_date ON service_views(viewed_at);
CREATE INDEX idx_platform_analytics_date ON platform_analytics(metric_date);
