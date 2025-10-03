<?php
/**
 * Authentication Class
 * Handles session management and authentication
 */

class Auth {
    
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user_data) {
        self::startSession();
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['user_type'] = $user_data['user_type'];
        $_SESSION['first_name'] = $user_data['first_name'];
        $_SESSION['last_name'] = $user_data['last_name'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
    }

    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function getCurrentUser() {
        self::startSession();
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'user_type' => $_SESSION['user_type'],
                'first_name' => $_SESSION['first_name'],
                'last_name' => $_SESSION['last_name']
            ];
        }
        return null;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    public static function requireRole($allowed_roles) {
        self::requireLogin();
        $user = self::getCurrentUser();
        
        if (!in_array($user['user_type'], $allowed_roles)) {
            header('HTTP/1.1 403 Forbidden');
            die('Access denied. Insufficient permissions.');
        }
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
