<?php
/**
 * Web-based test page for dashboard functionality
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Service.php';
require_once 'classes/Booking.php';

// Start session
Auth::startSession();

// Check if user is logged in
$isLoggedIn = Auth::isLoggedIn();
$user = null;
if ($isLoggedIn) {
    $user = Auth::getCurrentUser();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Dashboard Functionality Test</h1>
        
        <?php if (!$isLoggedIn): ?>
            <div class="alert alert-warning">
                <h4>Not Logged In</h4>
                <p>You need to be logged in to test dashboard functionality.</p>
                <a href="login.php" class="btn btn-primary">Login</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <h4>Logged In</h4>
                <p>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> (<?php echo $user['user_type']; ?>)</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Admin Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <?php if (in_array($user['user_type'], ['admin', 'superadmin'])): ?>
                                <a href="admin/enhanced_dashboard.php" class="btn btn-primary">Access Admin Dashboard</a>
                            <?php else: ?>
                                <p class="text-muted">Not accessible for your user type</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Customer Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <?php if (in_array($user['user_type'], ['customer'])): ?>
                                <a href="customer/enhanced_dashboard.php" class="btn btn-primary">Access Customer Dashboard</a>
                            <?php else: ?>
                                <p class="text-muted">Not accessible for your user type</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Provider Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <?php if (in_array($user['user_type'], ['service_provider'])): ?>
                                <a href="provider/index.php" class="btn btn-primary">Access Provider Dashboard</a>
                            <?php else: ?>
                                <p class="text-muted">Not accessible for your user type</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3>API Endpoint Tests</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Endpoint</th>
                                <th>Status</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>api/users.php?action=list&limit=1</td>
                                <td id="users-status">Testing...</td>
                                <td id="users-result">-</td>
                            </tr>
                            <tr>
                                <td>api/services.php?action=list&limit=1</td>
                                <td id="services-status">Testing...</td>
                                <td id="services-result">-</td>
                            </tr>
                            <tr>
                                <td>api/bookings.php?action=recent&limit=1</td>
                                <td id="bookings-status">Testing...</td>
                                <td id="bookings-result">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isLoggedIn): ?>
        <script>
            // Test API endpoints
            const endpoints = [
                { url: 'api/users.php?action=list&limit=1', id: 'users' },
                { url: 'api/services.php?action=list&limit=1', id: 'services' },
                { url: 'api/bookings.php?action=recent&limit=1', id: 'bookings' }
            ];

            endpoints.forEach(endpoint => {
                fetch(endpoint.url)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById(endpoint.id + '-status').innerHTML = 
                            data.success ? '<span class="text-success">SUCCESS</span>' : '<span class="text-danger">FAILED</span>';
                        document.getElementById(endpoint.id + '-result').innerHTML = 
                            data.success ? 'OK' : (data.message || 'Error');
                    })
                    .catch(error => {
                        document.getElementById(endpoint.id + '-status').innerHTML = '<span class="text-danger">ERROR</span>';
                        document.getElementById(endpoint.id + '-result').innerHTML = error.message;
                    });
            });
        </script>
    <?php endif; ?>
</body>
</html>