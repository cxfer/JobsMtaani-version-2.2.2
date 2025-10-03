        </main>
        
        <!-- Unified Footer -->
        <footer class="bg-dark text-white pt-5 pb-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                        <h4 class="text-uppercase mb-4"><?php echo AppSettings::get('app_name', 'JobsMtaani'); ?></h4>
                        <p class="text-muted">Connecting customers with trusted local service providers across Kenya.</p>
                        <div class="d-flex gap-3 mt-4">
                            <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white fs-4"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="text-uppercase mb-4">For Customers</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/services.php" class="text-muted text-decoration-none">Browse Services</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/login.php" class="text-muted text-decoration-none">Login</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/register.php" class="text-muted text-decoration-none">Register</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/favorites.php" class="text-muted text-decoration-none">Favorites</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="text-uppercase mb-4">For Providers</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/register.php?user_type=provider" class="text-muted text-decoration-none">Join as Provider</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/login.php" class="text-muted text-decoration-none">Provider Login</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/provider/resources.php" class="text-muted text-decoration-none">Provider Resources</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/provider/help.php" class="text-muted text-decoration-none">Help Center</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="text-uppercase mb-4">Company</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/about.php" class="text-muted text-decoration-none">About Us</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/contact.php" class="text-muted text-decoration-none">Contact</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/careers.php" class="text-muted text-decoration-none">Careers</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/blog.php" class="text-muted text-decoration-none">Blog</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6">
                        <h5 class="text-uppercase mb-4">Support</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/help.php" class="text-muted text-decoration-none">Help Center</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/faq.php" class="text-muted text-decoration-none">FAQ</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/terms.php" class="text-muted text-decoration-none">Terms of Service</a></li>
                            <li class="mb-2"><a href="<?php echo $baseUrl; ?>/privacy.php" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                
                <hr class="my-4 bg-secondary">
                
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> <?php echo AppSettings::get('app_name', 'JobsMtaani'); ?>. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item me-3"><a href="<?php echo $baseUrl; ?>/terms.php" class="text-muted text-decoration-none">Terms</a></li>
                            <li class="list-inline-item me-3"><a href="<?php echo $baseUrl; ?>/privacy.php" class="text-muted text-decoration-none">Privacy</a></li>
                            <li class="list-inline-item"><a href="<?php echo $baseUrl; ?>/security.php" class="text-muted text-decoration-none">Security</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>