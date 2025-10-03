<?php
require_once '../includes/session_check.php';

$user = checkUserAccess('provider');

// Set user data for JavaScript
echo "<script>
    window.currentUser = " . json_encode($user) . ";
    window.userPermissions = " . json_encode([
        'manage_own_services' => hasPermission($user, 'manage_own_services'),
        'view_own_bookings' => hasPermission($user, 'view_own_bookings'),
        'update_availability' => hasPermission($user, 'update_availability'),
        'view_own_analytics' => hasPermission($user, 'view_own_analytics'),
        'respond_to_reviews' => hasPermission($user, 'respond_to_reviews')
    ]) . ";
</script>";
?>
