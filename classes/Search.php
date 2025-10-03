<?php
/**
 * Advanced Search and Filtering Class
 * Handles complex search queries, filtering, and recommendations
 */

class Search {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Advanced service search with multiple filters
     */
    public function searchServices($params = []) {
        $query = "SELECT s.*, c.name as category_name, c.icon as category_icon,
                         u.first_name, u.last_name, u.profile_image,
                         AVG(r.rating) as average_rating,
                         COUNT(r.id) as review_count,
                         COUNT(DISTINCT b.id) as booking_count
                  FROM services s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN reviews r ON s.id = r.service_id
                  LEFT JOIN bookings b ON s.id = b.service_id
                  WHERE s.is_active = 1";
        
        $conditions = [];
        $bind_params = [];
        
        // Text search
        if (!empty($params['search'])) {
            $conditions[] = "(s.title LIKE :search OR s.description LIKE :search OR c.name LIKE :search)";
            $bind_params[':search'] = '%' . $params['search'] . '%';
        }
        
        // Category filter
        if (!empty($params['category_id'])) {
            $conditions[] = "s.category_id = :category_id";
            $bind_params[':category_id'] = $params['category_id'];
        }
        
        // Location filter
        if (!empty($params['location'])) {
            $conditions[] = "(s.location LIKE :location OR u.location LIKE :location)";
            $bind_params[':location'] = '%' . $params['location'] . '%';
        }
        
        // Price range filter
        if (!empty($params['min_price'])) {
            $conditions[] = "s.price >= :min_price";
            $bind_params[':min_price'] = $params['min_price'];
        }
        
        if (!empty($params['max_price'])) {
            $conditions[] = "s.price <= :max_price";
            $bind_params[':max_price'] = $params['max_price'];
        }
        
        // Rating filter
        if (!empty($params['min_rating'])) {
            $conditions[] = "AVG(r.rating) >= :min_rating";
            $bind_params[':min_rating'] = $params['min_rating'];
        }
        
        // Availability filter
        if (!empty($params['available_date'])) {
            $conditions[] = "s.provider_id NOT IN (
                SELECT DISTINCT b.provider_id 
                FROM bookings b 
                WHERE DATE(b.booking_date) = :available_date 
                AND b.status IN ('confirmed', 'in_progress')
            )";
            $bind_params[':available_date'] = $params['available_date'];
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        
        $query .= " GROUP BY s.id";
        
        // Sorting
        $sort_options = [
            'price_asc' => 'ORDER BY s.price ASC',
            'price_desc' => 'ORDER BY s.price DESC',
            'rating' => 'ORDER BY average_rating DESC',
            'popular' => 'ORDER BY booking_count DESC',
            'newest' => 'ORDER BY s.created_at DESC',
            'relevance' => 'ORDER BY (CASE WHEN s.title LIKE :search THEN 3 WHEN s.description LIKE :search THEN 2 WHEN c.name LIKE :search THEN 1 ELSE 0 END) DESC'
        ];
        
        $sort = $params['sort'] ?? 'relevance';
        if (isset($sort_options[$sort])) {
            $query .= " " . $sort_options[$sort];
        }
        
        // Pagination
        $limit = $params['limit'] ?? 20;
        $offset = ($params['page'] ?? 1 - 1) * $limit;
        $query .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters
        foreach ($bind_params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get search suggestions based on user input
     */
    public function getSearchSuggestions($query, $limit = 10) {
        $suggestions = [];
        
        // Service title suggestions
        $stmt = $this->conn->prepare("
            SELECT DISTINCT title as suggestion, 'service' as type
            FROM services 
            WHERE title LIKE :query AND is_active = 1
            LIMIT :limit
        ");
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // Category suggestions
        $stmt = $this->conn->prepare("
            SELECT DISTINCT name as suggestion, 'category' as type
            FROM categories 
            WHERE name LIKE :query AND is_active = 1
            LIMIT :limit
        ");
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // Location suggestions
        $stmt = $this->conn->prepare("
            SELECT DISTINCT location as suggestion, 'location' as type
            FROM services 
            WHERE location LIKE :query AND location IS NOT NULL AND location != ''
            LIMIT :limit
        ");
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        return array_slice($suggestions, 0, $limit);
    }
    
    /**
     * Get personalized service recommendations for a user
     */
    public function getRecommendations($user_id, $limit = 10) {
        // Get user's booking history and preferences
        $user_categories = $this->getUserPreferredCategories($user_id);
        $user_location = $this->getUserLocation($user_id);
        
        $query = "SELECT s.*, c.name as category_name, c.icon as category_icon,
                         u.first_name, u.last_name, u.profile_image,
                         AVG(r.rating) as average_rating,
                         COUNT(r.id) as review_count,
                         (CASE 
                            WHEN s.category_id IN (" . implode(',', array_fill(0, count($user_categories), '?')) . ") THEN 2
                            WHEN s.location LIKE ? THEN 1.5
                            ELSE 1
                         END) as relevance_score
                  FROM services s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN reviews r ON s.id = r.service_id
                  WHERE s.is_active = 1
                  AND s.id NOT IN (
                      SELECT service_id FROM bookings 
                      WHERE customer_id = ? AND status = 'completed'
                  )
                  GROUP BY s.id
                  ORDER BY relevance_score DESC, average_rating DESC
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        
        $params = array_merge($user_categories, ['%' . $user_location . '%', $user_id, $limit]);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get trending services based on recent bookings
     */
    public function getTrendingServices($limit = 10, $days = 7) {
        $query = "SELECT s.*, c.name as category_name, c.icon as category_icon,
                         u.first_name, u.last_name, u.profile_image,
                         AVG(r.rating) as average_rating,
                         COUNT(r.id) as review_count,
                         COUNT(b.id) as recent_bookings
                  FROM services s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN reviews r ON s.id = r.service_id
                  LEFT JOIN bookings b ON s.id = b.service_id 
                      AND b.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                  WHERE s.is_active = 1
                  GROUP BY s.id
                  HAVING recent_bookings > 0
                  ORDER BY recent_bookings DESC, average_rating DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user's preferred categories based on booking history
     */
    private function getUserPreferredCategories($user_id) {
        $query = "SELECT s.category_id, COUNT(*) as booking_count
                  FROM bookings b
                  JOIN services s ON b.service_id = s.id
                  WHERE b.customer_id = :user_id
                  GROUP BY s.category_id
                  ORDER BY booking_count DESC
                  LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return empty($categories) ? [0] : $categories; // Return [0] if no categories to avoid SQL errors
    }
    
    /**
     * Get user's location
     */
    private function getUserLocation($user_id) {
        $query = "SELECT location FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['location'] : '';
    }
    
    /**
     * Log search queries for analytics
     */
    public function logSearch($user_id, $query, $filters = [], $results_count = 0) {
        $log_query = "INSERT INTO search_logs 
                      SET user_id = :user_id,
                          search_query = :search_query,
                          filters = :filters,
                          results_count = :results_count,
                          created_at = NOW()";
        
        $stmt = $this->conn->prepare($log_query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':search_query', $query);
        $stmt->bindParam(':filters', json_encode($filters));
        $stmt->bindParam(':results_count', $results_count);
        
        return $stmt->execute();
    }
    
    /**
     * Get popular search terms
     */
    public function getPopularSearches($limit = 10) {
        $query = "SELECT search_query, COUNT(*) as search_count
                  FROM search_logs
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  AND search_query IS NOT NULL
                  AND search_query != ''
                  GROUP BY search_query
                  ORDER BY search_count DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
