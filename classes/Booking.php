<?php
/**
 * Booking Class
 * Handles booking management
 */

require_once __DIR__ . '/../config/database.php';

class Booking {
    private $conn;
    private $table_name = "bookings";
    
    public $id;
    public $customer_id;
    public $provider_id;
    public $service_id;
    public $booking_date;
    public $booking_time;
    public $total_amount;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new booking
    public function create() {
        // Generate booking number
        $booking_number = 'BK' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $query = "INSERT INTO " . $this->table_name . "
                  SET booking_number=:booking_number, customer_id=:customer_id, 
                      provider_id=:provider_id, service_id=:service_id,
                      booking_date=:booking_date, booking_time=:booking_time,
                      duration=:duration, total_amount=:total_amount,
                      status=:status, location_type=:location_type,
                      service_address=:service_address, customer_notes=:customer_notes";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":booking_number", $booking_number);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":provider_id", $this->provider_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":booking_time", $this->booking_time);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":location_type", $this->location_type);
        $stmt->bindParam(":service_address", $this->service_address);
        $stmt->bindParam(":customer_notes", $this->customer_notes);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Get bookings by user
    public function getBookingsByUser($user_id, $user_type, $limit = 50, $offset = 0, $status = null, $date = null, $search = null) {
        $where_field = ($user_type === 'service_provider') ? 'provider_id' : 'customer_id';
        
        $query = "SELECT b.*, s.title as service_title, s.price,
                         u1.first_name as customer_first_name, u1.last_name as customer_last_name,
                         u2.first_name as provider_first_name, u2.last_name as provider_last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN users u1 ON b.customer_id = u1.id
                  LEFT JOIN users u2 ON b.provider_id = u2.id
                  WHERE b." . $where_field . " = :user_id";
        
        // Add filters
        $params = [':user_id' => $user_id];
        
        if ($status) {
            $query .= " AND b.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date) {
            $query .= " AND b.booking_date = :date";
            $params[':date'] = $date;
        }
        
        if ($search) {
            $query .= " AND (s.title LIKE :search OR b.booking_number LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC
                    LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get customer bookings
    public function getCustomerBookings($customer_id, $limit = 50, $offset = 0, $status = null, $date = null, $search = null) {
        $query = "SELECT b.*, s.title as service_title, s.price,
                         u1.first_name as customer_first_name, u1.last_name as customer_last_name,
                         u2.first_name as provider_first_name, u2.last_name as provider_last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN users u1 ON b.customer_id = u1.id
                  LEFT JOIN users u2 ON b.provider_id = u2.id
                  WHERE b.customer_id = :customer_id";
        
        // Add filters
        $params = [':customer_id' => $customer_id];
        
        if ($status) {
            $query .= " AND b.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date) {
            $query .= " AND b.booking_date = :date";
            $params[':date'] = $date;
        }
        
        if ($search) {
            $query .= " AND (s.title LIKE :search OR b.booking_number LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC
                    LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update booking status
    public function updateStatus($booking_id, $status, $user_id = null) {
        $query = "UPDATE " . $this->table_name . " SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        
        // Add user restriction for non-admin users
        if ($user_id) {
            $query .= " AND (customer_id = :user_id OR provider_id = :user_id)";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $booking_id);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        return $stmt->execute();
    }

    // Cancel booking
    public function cancelBooking($booking_id, $user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'cancelled', cancellation_reason = 'Cancelled by user', updated_at = CURRENT_TIMESTAMP 
                  WHERE id = :id AND customer_id = :user_id AND status IN ('pending', 'confirmed')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $booking_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    // Get all bookings (admin function)
    public function getAllBookings($limit = 50, $offset = 0, $status = null, $date = null, $search = null) {
        $query = "SELECT b.*, s.title as service_title,
                         u1.first_name as customer_first_name, u1.last_name as customer_last_name,
                         u2.first_name as provider_first_name, u2.last_name as provider_last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN users u1 ON b.customer_id = u1.id
                  LEFT JOIN users u2 ON b.provider_id = u2.id";
        
        // Add filters
        $where_conditions = [];
        $params = [];
        
        if ($status) {
            $where_conditions[] = "b.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date) {
            $where_conditions[] = "b.booking_date = :date";
            $params[':date'] = $date;
        }
        
        if ($search) {
            $where_conditions[] = "(s.title LIKE :search OR b.booking_number LIKE :search OR u1.first_name LIKE :search OR u1.last_name LIKE :search OR u2.first_name LIKE :search OR u2.last_name LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $query .= " ORDER BY b.created_at DESC
                    LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>