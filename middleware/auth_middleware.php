<?php
require_once '../config/database.php';
require_once '../classes/Auth.php';

class AuthMiddleware {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    public function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        return $this->auth->getCurrentUser();
    }
    
    public function requireRole($allowedRoles) {
        $user = $this->requireAuth();
        
        if (!in_array($user['role'], $allowedRoles)) {
            http_response_code(403);
            echo json_encode(['error' => 'Insufficient permissions']);
            exit;
        }
        
        return $user;
    }
    
    public function requireSuperAdmin() {
        return $this->requireRole(['superadmin']);
    }
    
    public function requireAdmin() {
        return $this->requireRole(['superadmin', 'admin']);
    }
    
    public function requireProvider() {
        return $this->requireRole(['superadmin', 'admin', 'provider']);
    }
    
    public function requireCustomer() {
        return $this->requireRole(['superadmin', 'admin', 'provider', 'customer']);
    }
}
?>
