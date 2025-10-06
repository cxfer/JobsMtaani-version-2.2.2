<?php
/**
 * Service Class
 * Handles service management
 */

require_once __DIR__ . '/../config/database.php';

class Service {
    private $conn;
    private $table_name = "services";
    
    public $id;
    public $provider_id;
    public $category_id;
    public $title;
    public $description;
    public $price;
    public $duration;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all active services
    public function getAllServices($limit = 20, $offset = 0, $category_id = null) {
        $where_clause = "WHERE s.is_active = 1";
        if ($category_id) {
            $where_clause .= " AND s.category_id = :category_id";
        }

        $query = "SELECT s.*, c.name as category_name, 
                         u.first_name, u.last_name, u.username,
                         AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                  FROM " . $this->table_name . " s
                  LEFT JOIN service_categories c ON s.category_id = c.id
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN bookings b ON s.id = b.service_id
                  LEFT JOIN reviews r ON b.id = r.booking_id
                  " . $where_clause . "
                  GROUP BY s.id
                  ORDER BY s.featured DESC, s.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get services by provider
    public function getServicesByProvider($provider_id) {
        $query = "SELECT s.*, c.name as category_name,
                         AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                  FROM " . $this->table_name . " s
                  LEFT JOIN service_categories c ON s.category_id = c.id
                  LEFT JOIN bookings b ON s.id = b.service_id
                  LEFT JOIN reviews r ON b.id = r.booking_id
                  WHERE s.provider_id = :provider_id
                  GROUP BY s.id
                  ORDER BY s.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_id', $provider_id);
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process images for each service
        foreach ($services as &$service) {
            if ($service['images']) {
                $service['images'] = json_decode($service['images'], true);
            } else {
                $service['images'] = [];
            }
        }
        
        return $services;
    }

    // Create new service
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET provider_id=:provider_id, category_id=:category_id, title=:title,
                      slug=:slug, description=:description, short_description=:short_description,
                      price=:price, price_type=:price_type, duration=:duration,
                      location_type=:location_type, is_active=:is_active";

        $stmt = $this->conn->prepare($query);

        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title)));

        // Bind values
        $stmt->bindParam(":provider_id", $this->provider_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":slug", $slug);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":short_description", $this->short_description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":price_type", $this->price_type);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":location_type", $this->location_type);
        $stmt->bindParam(":is_active", $this->is_active);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Get service categories
    public function getCategories() {
        $query = "SELECT *, CASE WHEN is_premium = 1 THEN 'Premium' ELSE 'Standard' END as category_type FROM service_categories WHERE is_active = 1 ORDER BY sort_order, name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch services for listings page with filters/pagination
    public function getServices($search, $categoryKey, $location, $limit, $offset) {
        // Map UI filter keys to category slugs
        $categoryMap = [
            'home' => 'home-maintenance',
            'beauty' => 'beauty-salon',
            'automotive' => 'transportation',
            'events' => 'events',
            'tutoring' => 'education'
        ];

        $where = ["s.is_active = 1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(s.title LIKE :search OR s.description LIKE :search)";
            $params[':search'] = "%" . $search . "%";
        }

        if (!empty($categoryKey) && isset($categoryMap[$categoryKey])) {
            $where[] = "c.slug = :category_slug";
            $params[':category_slug'] = $categoryMap[$categoryKey];
        }

        if (!empty($location)) {
            $where[] = "(up.city LIKE :location OR up.country LIKE :location)";
            $params[':location'] = "%" . $location . "%";
        }

        $whereSql = "WHERE " . implode(" AND ", $where);

        $query = "
            SELECT 
                s.id,
                s.title,
                s.description,
                s.price,
                s.price_type AS price_unit,
                c.name AS category,
                COALESCE(
                    JSON_UNQUOTE(JSON_EXTRACT(s.images, '$[0]')),
                    '/public/abstract-service.png'
                ) AS image,
                CONCAT(u.first_name, ' ', u.last_name) AS provider_name,
                COALESCE(u.profile_image, '/public/placeholder-user.jpg') AS provider_avatar,
                FLOOR(AVG(r.rating)) AS rating,
                COUNT(r.id) AS review_count
            FROM " . $this->table_name . " s
            LEFT JOIN service_categories c ON s.category_id = c.id
            LEFT JOIN users u ON s.provider_id = u.id
            LEFT JOIN user_profiles up ON up.user_id = u.id
            LEFT JOIN bookings b ON s.id = b.service_id
            LEFT JOIN reviews r ON b.id = r.booking_id
            " . $whereSql . "
            GROUP BY s.id
            ORDER BY s.featured DESC, s.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesCount($search, $categoryKey, $location) {
        $categoryMap = [
            'home' => 'home-maintenance',
            'beauty' => 'beauty-salon',
            'automotive' => 'transportation',
            'events' => 'events',
            'tutoring' => 'education'
        ];

        $where = ["s.is_active = 1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(s.title LIKE :search OR s.description LIKE :search)";
            $params[':search'] = "%" . $search . "%";
        }

        if (!empty($categoryKey) && isset($categoryMap[$categoryKey])) {
            $where[] = "c.slug = :category_slug";
            $params[':category_slug'] = $categoryMap[$categoryKey];
        }

        if (!empty($location)) {
            $where[] = "(up.city LIKE :location OR up.country LIKE :location)";
            $params[':location'] = "%" . $location . "%";
        }

        $whereSql = "WHERE " . implode(" AND ", $where);

        $query = "
            SELECT COUNT(DISTINCT s.id) AS total
            FROM " . $this->table_name . " s
            LEFT JOIN service_categories c ON s.category_id = c.id
            LEFT JOIN users u ON s.provider_id = u.id
            LEFT JOIN user_profiles up ON up.user_id = u.id
            " . $whereSql . "
        ";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
}
?>