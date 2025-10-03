<?php
require_once '../includes/session_check.php';

$user = checkUserAccess('customer');

// Set user data for JavaScript
echo "<script>
    window.currentUser = " . json_encode($user) . ";
    window.userPermissions = " . json_encode([
        'book_services' => hasPermission($user, 'book_services'),
        'view_own_bookings' => hasPermission($user, 'view_own_bookings'),
        'leave_reviews' => hasPermission($user, 'leave_reviews'),
        'manage_favorites' => hasPermission($user, 'manage_favorites'),
        'update_profile' => hasPermission($user, 'update_profile')
    ]) . ";
</script>";
?>
