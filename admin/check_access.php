<?php
require_once '../includes/session_check.php';

$user = checkUserAccess('superadmin');

// Set user data for JavaScript
echo "<script>
    window.currentUser = " . json_encode($user) . ";
    window.userPermissions = " . json_encode([
        'manage_users' => hasPermission($user, 'manage_users'),
        'manage_settings' => hasPermission($user, 'manage_settings'),
        'view_all_bookings' => hasPermission($user, 'view_all_bookings'),
        'manage_services' => hasPermission($user, 'manage_services'),
        'view_analytics' => hasPermission($user, 'view_analytics'),
        'manage_payments' => hasPermission($user, 'manage_payments'),
        'system_backup' => hasPermission($user, 'system_backup')
    ]) . ";
</script>";
?>
