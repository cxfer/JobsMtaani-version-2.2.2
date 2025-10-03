-- Initial data for JobsMtaani platform

USE jobsmtaani;

-- Insert default service categories
INSERT INTO service_categories (name, slug, description, icon, sort_order) VALUES
('Beauty & Salon', 'beauty-salon', 'Hair styling, makeup, manicure, pedicure and beauty treatments', 'fas fa-cut', 1),
('Home Maintenance', 'home-maintenance', 'Plumbing, electrical, carpentry and general repairs', 'fas fa-tools', 2),
('Transportation', 'transportation', 'Taxi services, delivery and logistics', 'fas fa-car', 3),
('Personal Care', 'personal-care', 'Barber, massage, fitness training', 'fas fa-user', 4),
('Education', 'education', 'Tutoring, training and educational services', 'fas fa-graduation-cap', 5),
('Cleaning', 'cleaning', 'House cleaning, office cleaning, laundry services', 'fas fa-broom', 6),
('Technology', 'technology', 'Computer repair, phone repair, IT support', 'fas fa-laptop', 7),
('Events', 'events', 'Photography, catering, event planning', 'fas fa-camera', 8);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('app_name', 'JobsMtaani', 'text', 'Application name', TRUE),
('app_tagline', 'Your Neighborhood Services, Just a Click Away', 'text', 'Application tagline', TRUE),
('primary_color', '#007bff', 'color', 'Primary brand color', TRUE),
('secondary_color', '#6c757d', 'color', 'Secondary brand color', TRUE),
('accent_color', '#28a745', 'color', 'Accent color for highlights', TRUE),
('currency', 'KES', 'text', 'Default currency', TRUE),
('timezone', 'Africa/Nairobi', 'text', 'Default timezone', FALSE),
('booking_advance_days', '30', 'number', 'How many days in advance bookings can be made', FALSE),
('commission_rate', '10', 'number', 'Platform commission percentage', FALSE),
('min_booking_amount', '100', 'number', 'Minimum booking amount', FALSE),
('max_booking_amount', '50000', 'number', 'Maximum booking amount', FALSE),
('email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE),
('sms_notifications', 'true', 'boolean', 'Enable SMS notifications', FALSE),
('auto_confirm_bookings', 'false', 'boolean', 'Auto-confirm bookings without provider approval', FALSE),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode', FALSE);

-- Insert role permissions for different user types
INSERT INTO role_permissions (role_name, permission_name, description) VALUES
-- Superadmin permissions (all permissions)
('superadmin', 'manage_users', 'Create, edit, delete users'),
('superadmin', 'manage_services', 'Manage all services'),
('superadmin', 'manage_bookings', 'Manage all bookings'),
('superadmin', 'manage_payments', 'View and manage payments'),
('superadmin', 'manage_reviews', 'Moderate reviews'),
('superadmin', 'manage_categories', 'Manage service categories'),
('superadmin', 'manage_settings', 'Manage system settings'),
('superadmin', 'view_analytics', 'View platform analytics'),
('superadmin', 'manage_permissions', 'Manage user permissions'),
('superadmin', 'view_logs', 'View activity logs'),
('superadmin', 'manage_coupons', 'Create and manage coupons'),

-- Admin permissions (limited admin access)
('admin', 'manage_services', 'Manage services (limited)'),
('admin', 'manage_bookings', 'Manage bookings (limited)'),
('admin', 'view_analytics', 'View limited analytics'),
('admin', 'manage_reviews', 'Moderate reviews'),
('admin', 'manage_categories', 'Manage service categories'),

-- Service provider permissions
('service_provider', 'manage_own_services', 'Manage own services'),
('service_provider', 'manage_own_bookings', 'Manage own bookings'),
('service_provider', 'view_own_analytics', 'View own performance analytics'),
('service_provider', 'manage_availability', 'Manage availability schedule'),
('service_provider', 'respond_to_reviews', 'Respond to customer reviews'),

-- Customer permissions
('customer', 'book_services', 'Book services'),
('customer', 'manage_own_bookings', 'Manage own bookings'),
('customer', 'write_reviews', 'Write service reviews'),
('customer', 'manage_favorites', 'Manage favorite services'),
('customer', 'view_booking_history', 'View booking history');

-- Create default superadmin user (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, status, email_verified) VALUES
('superadmin', 'admin@jobsmtaani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Admin', 'superadmin', 'active', TRUE);

-- Insert superadmin profile
INSERT INTO user_profiles (user_id, bio, city, country) VALUES
(1, 'System Administrator', 'Nairobi', 'Kenya');

-- Grant all permissions to superadmin
INSERT INTO user_permissions (user_id, permission_name, granted_by)
SELECT 1, permission_name, 1 FROM role_permissions WHERE role_name = 'superadmin';
