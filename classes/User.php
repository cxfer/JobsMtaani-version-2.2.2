<?php
/**
 * User Class
 * Handles user authentication and management
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $first_name;
    public $last_name;
    public $phone;
    public $user_type;
    public $status;
    public $created_at;

    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, email=:email, password_hash=:password_hash, 
                      first_name=:first_name, last_name=:last_name, phone=:phone, 
                      user_type=:user_type, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $this->password_hash);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":user_type", $this->user_type);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Create user profile
            $this->createUserProfile($this->id);
            
            // If provider, create provider profile
            if ($this->user_type === 'service_provider') {
                $this->createProviderProfile($this->id);
            }
            
            return true;
        }
        return false;
    }

    public function createUser($userData) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET first_name=:first_name, last_name=:last_name, email=:email, 
                      phone=:phone, password_hash=:password_hash, user_type=:user_type, 
                      status=:status, subscription_plan=:subscription_plan";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(":first_name", $userData['first_name']);
        $stmt->bindParam(":last_name", $userData['last_name']);
        $stmt->bindParam(":email", $userData['email']);
        $stmt->bindParam(":phone", $userData['phone']);
        $stmt->bindParam(":password_hash", $userData['password']);
        $stmt->bindParam(":user_type", $userData['user_type']);
        $stmt->bindParam(":status", $userData['status']);
        $stmt->bindParam(":subscription_plan", $userData['subscription_plan']);

        if($stmt->execute()) {
            $userId = $this->conn->lastInsertId();
            
            // Create user profile
            $this->createUserProfile($userId, $userData);
            
            // If provider, create provider profile
            if ($userData['user_type'] === 'service_provider') {
                $this->createProviderProfile($userId, $userData);
            }
            
            return $userId;
        }
        return false;
    }

    private function createUserProfile($userId, $userData = []) {
        $query = "INSERT INTO user_profiles 
                  SET user_id=:user_id, email_verified=0, phone_verified=0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        return $stmt->execute();
    }

    private function createProviderProfile($userId, $userData = []) {
        $query = "INSERT INTO provider_profiles 
                  SET user_id=:user_id, business_name=:business_name, 
                      category=:category, description=:description, status='pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":business_name", $userData['business_name'] ?? '');
        $stmt->bindParam(":category", $userData['category'] ?? '');
        $stmt->bindParam(":description", $userData['description'] ?? '');
        return $stmt->execute();
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function setVerificationToken($userId, $token) {
        $query = "UPDATE user_profiles SET email_verification_token=:token WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function verifyEmail($token) {
        $query = "SELECT user_id FROM user_profiles WHERE email_verification_token=:token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $row['user_id'];
            
            // Update verification status
            $updateQuery = "UPDATE user_profiles SET email_verified=1, email_verification_token=NULL WHERE user_id=:user_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':user_id', $userId);
            
            // Also activate user account
            $activateQuery = "UPDATE users SET status='active' WHERE id=:user_id";
            $activateStmt = $this->conn->prepare($activateQuery);
            $activateStmt->bindParam(':user_id', $userId);
            
            return $updateStmt->execute() && $activateStmt->execute();
        }
        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT u.*, up.bio, up.address, up.city, up.phone_verified, up.email_verified, up.id_number, up.id_document_path
                  FROM " . $this->table_name . " u
                  LEFT JOIN user_profiles up ON u.id = up.user_id
                  WHERE u.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Check user permissions
    public function hasPermission($user_id, $permission) {
        $query = "SELECT COUNT(*) as count FROM user_permissions 
                  WHERE user_id = :user_id AND permission_name = :permission AND granted = 1
                  UNION
                  SELECT COUNT(*) as count FROM role_permissions rp
                  JOIN users u ON u.user_type = rp.role_name
                  WHERE u.id = :user_id AND rp.permission_name = :permission";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':permission', $permission);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Get all users (admin function)
    public function getAllUsers($limit = 50, $offset = 0, $user_type = null, $status = null, $search = null) {
        $query = "SELECT u.id, u.username, u.email, u.first_name, u.last_name, 
                         u.user_type, u.status, u.created_at,
                         up.city, up.id_number
                  FROM " . $this->table_name . " u
                  LEFT JOIN user_profiles up ON u.id = up.user_id";
        
        // Add filters
        $where_conditions = [];
        $params = [];
        
        if ($user_type) {
            $where_conditions[] = "u.user_type = :user_type";
            $params[':user_type'] = $user_type;
        }
        
        if ($status) {
            $where_conditions[] = "u.status = :status";
            $params[':status'] = $status;
        }
        
        if ($search) {
            $where_conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR up.id_number LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $query .= " ORDER BY u.created_at DESC
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

    // Update user status
    public function updateStatus($user_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // Login user
    public function login($email, $password) {
        $query = "SELECT id, first_name, last_name, email, password_hash, user_type, status 
                  FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }
    
    // Update user profile with ID information
    public function updateUserIdInfo($userId, $idNumber, $idDocumentPath) {
        $query = "UPDATE user_profiles 
                  SET id_number = :id_number, id_document_path = :id_document_path
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_number', $idNumber);
        $stmt->bindParam(':id_document_path', $idDocumentPath);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
?>