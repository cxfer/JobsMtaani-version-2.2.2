<?php
/**
 * Link Verification Script for JobsMtaani
 * This script verifies that all previously broken links are now working
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Link Verification</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'>JobsMtaani Link Verification</h3>
                </div>
                <div class='card-body'>";

// List of files to check
$files_to_check = [
    'service-details.php' => 'Service Details Page',
    'customer/bookings.php' => 'Customer Bookings Page',
    'customer/favorites.php' => 'Customer Favorites Page',
    'customer/reviews.php' => 'Customer Reviews Page'
];

echo "<h4>File Existence Verification</h4>";

foreach ($files_to_check as $file => $description) {
    $file_path = __DIR__ . '/' . $file;
    if (file_exists($file_path)) {
        echo "<div class='alert alert-success'>✓ $description ($file) - EXISTS</div>";
    } else {
        echo "<div class='alert alert-danger'>✗ $description ($file) - MISSING</div>";
    }
}

echo "<h4 class='mt-4'>URL Accessibility Test</h4>";
echo "<p class='text-muted'>Note: These tests check if files exist, not if they function correctly with data.</p>";

$base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

foreach ($files_to_check as $file => $description) {
    $url = $base_url . '/' . $file;
    // For service-details.php, we need to add an ID parameter
    if ($file === 'service-details.php') {
        $url .= '?id=1';
    }
    
    echo "<div class='mb-3'>
            <strong>$description</strong><br>
            <a href='$url' target='_blank'>$url</a>
          </div>";
}

echo "<div class='alert alert-info mt-4'>
        <h5>Next Steps:</h5>
        <p>To fully test functionality:</p>
        <ol>
            <li>Create a test user account or log in with existing credentials</li>
            <li>Visit each page to verify it loads without errors</li>
            <li>Check that database queries work correctly</li>
            <li>Verify all navigation links within the pages work</li>
        </ol>
      </div>";

echo "                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>";

?>