<?php
session_start();
require_once __DIR__ . '/includes/session_check.php';
$user = checkUserAccess();
require_once __DIR__ . '/includes/header.php';
?>
<main>
    <section class="py-5 hero-section text-white">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Hello, <?php echo htmlspecialchars($user['username'] ?? 'User'); ?></h1>
            <p class="mb-0">Here’s a quick overview of your account.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-muted">Bookings</h6>
                            <h3 class="fw-bold">--</h3>
                            <p class="mb-0">Recent activity at a glance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-muted">Payments</h6>
                            <h3 class="fw-bold">--</h3>
                            <p class="mb-0">Track your latest transactions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-muted">Favorites</h6>
                            <h3 class="fw-bold">--</h3>
                            <p class="mb-0">Quick access to saved services.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Quick Links</h5>
                            <div class="d-grid gap-2">
                                <a href="services.php" class="btn btn-primary">Browse Services</a>
                                <a href="#" class="btn btn-outline-secondary">My Bookings</a>
                                <a href="#" class="btn btn-outline-secondary">Account Settings</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Tips</h5>
                            <ul class="mb-0 text-muted">
                                <li>Complete your profile for better matches.</li>
                                <li>Enable notifications to never miss updates.</li>
                                <li>Keep your availability up to date.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>


