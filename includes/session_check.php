<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';

function checkUserAccess($requiredRole = null) {
    $auth = new Auth();
    
    if (!$auth->isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
    
    $user = $auth->getCurrentUser();
    
    if ($requiredRole) {
        $roleHierarchy = [
            'customer' => 1,
            'provider' => 2,
            'admin' => 3,
            'superadmin' => 4
        ];
        
        $userLevel = $roleHierarchy[$user['role']] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 5;
        
        if ($userLevel < $requiredLevel) {
            header('Location: ../unauthorized.php');
            exit;
        }
    }
    
    return $user;
}

function hasPermission($user, $permission) {
    $permissions = [
        'superadmin' => [
            'manage_users', 'manage_settings', 'view_all_bookings', 
            'manage_services', 'view_analytics', 'manage_payments',
            'manage_categories', 'manage_reviews', 'system_backup'
        ],
        'admin' => [
            'manage_users', 'view_all_bookings', 'manage_services',
            'view_analytics', 'manage_categories', 'manage_reviews'
        ],
        'provider' => [
            'manage_own_services', 'view_own_bookings', 'update_availability',
            'view_own_analytics', 'respond_to_reviews'
        ],
        'customer' => [
            'book_services', 'view_own_bookings', 'leave_reviews',
            'manage_favorites', 'update_profile'
        ]
    ];
    
    return in_array($permission, $permissions[$user['role']] ?? []);
}
?>
