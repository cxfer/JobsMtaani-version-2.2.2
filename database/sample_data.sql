-- Sample data for testing JobsMtaani platform

USE jobsmtaani;

-- Sample service providers
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status, email_verified) VALUES
('john_barber', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Kamau', '+254712345678', 'service_provider', 'active', TRUE),
('mary_salon', 'mary@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary', 'Wanjiku', '+254723456789', 'service_provider', 'active', TRUE),
('peter_plumber', 'peter@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Peter', 'Mwangi', '+254734567890', 'service_provider', 'active', TRUE);

-- Sample customers
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status, email_verified) VALUES
('jane_customer', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Akinyi', '+254745678901', 'customer', 'active', TRUE),
('david_customer', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Ochieng', '+254756789012', 'customer', 'active', TRUE);

-- Sample user profiles
INSERT INTO user_profiles (user_id, bio, address, city, state, country) VALUES
(2, 'Professional barber with 5+ years experience', 'Westlands, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(3, 'Licensed beautician and salon owner', 'Karen, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(4, 'Certified plumber serving Nairobi area', 'Kasarani, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(5, 'Regular customer', 'Kilimani, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(6, 'Frequent service user', 'Parklands, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya');

-- Sample services
INSERT INTO services (provider_id, category_id, title, slug, description, short_description, price, price_type, duration, location_type, is_active) VALUES
(2, 4, 'Professional Haircut & Styling', 'professional-haircut-styling', 'Get a fresh, professional haircut with styling. Includes wash, cut, and basic styling.', 'Professional haircut with wash and styling', 800.00, 'fixed', 45, 'at_provider', TRUE),
(2, 4, 'Beard Trimming & Grooming', 'beard-trimming-grooming', 'Expert beard trimming and grooming service for the modern gentleman.', 'Professional beard trimming and grooming', 500.00, 'fixed', 30, 'at_provider', TRUE),
(3, 1, 'Hair Styling & Makeup', 'hair-styling-makeup', 'Complete hair styling and makeup service for special occasions.', 'Professional hair and makeup service', 2500.00, 'fixed', 120, 'both', TRUE),
(3, 1, 'Manicure & Pedicure', 'manicure-pedicure', 'Relaxing manicure and pedicure service with quality nail care.', 'Professional nail care service', 1200.00, 'fixed', 90, 'at_provider', TRUE),
(4, 2, 'Plumbing Repairs', 'plumbing-repairs', 'Professional plumbing repair services for homes and offices.', 'Expert plumbing repair services', 1500.00, 'hourly', 60, 'at_customer', TRUE),
(4, 2, 'Pipe Installation', 'pipe-installation', 'New pipe installation and replacement services.', 'Professional pipe installation', 2000.00, 'negotiable', 180, 'at_customer', TRUE);

-- Sample provider availability (Monday to Saturday, 8AM to 6PM)
INSERT INTO provider_availability (provider_id, day_of_week, start_time, end_time, is_available) VALUES
-- John Barber (user_id: 2)
(2, 1, '08:00:00', '18:00:00', TRUE), -- Monday
(2, 2, '08:00:00', '18:00:00', TRUE), -- Tuesday
(2, 3, '08:00:00', '18:00:00', TRUE), -- Wednesday
(2, 4, '08:00:00', '18:00:00', TRUE), -- Thursday
(2, 5, '08:00:00', '18:00:00', TRUE), -- Friday
(2, 6, '09:00:00', '17:00:00', TRUE), -- Saturday

-- Mary Salon (user_id: 3)
(3, 1, '09:00:00', '19:00:00', TRUE), -- Monday
(3, 2, '09:00:00', '19:00:00', TRUE), -- Tuesday
(3, 3, '09:00:00', '19:00:00', TRUE), -- Wednesday
(3, 4, '09:00:00', '19:00:00', TRUE), -- Thursday
(3, 5, '09:00:00', '19:00:00', TRUE), -- Friday
(3, 6, '08:00:00', '18:00:00', TRUE), -- Saturday

-- Peter Plumber (user_id: 4)
(4, 1, '07:00:00', '17:00:00', TRUE), -- Monday
(4, 2, '07:00:00', '17:00:00', TRUE), -- Tuesday
(4, 3, '07:00:00', '17:00:00', TRUE), -- Wednesday
(4, 4, '07:00:00', '17:00:00', TRUE), -- Thursday
(4, 5, '07:00:00', '17:00:00', TRUE), -- Friday
(4, 6, '08:00:00', '16:00:00', TRUE); -- Saturday

-- Sample bookings
INSERT INTO bookings (booking_number, customer_id, provider_id, service_id, booking_date, booking_time, duration, total_amount, status, payment_status, location_type, service_address) VALUES
('BK001', 5, 2, 1, '2024-01-15', '10:00:00', 45, 800.00, 'completed', 'paid', 'at_provider', 'Westlands Barber Shop'),
('BK002', 6, 3, 3, '2024-01-16', '14:00:00', 120, 2500.00, 'confirmed', 'paid', 'at_provider', 'Karen Beauty Salon'),
('BK003', 5, 4, 5, '2024-01-17', '09:00:00', 60, 1500.00, 'in_progress', 'paid', 'at_customer', 'Kilimani Apartments, Apt 12B');

-- Sample reviews
INSERT INTO reviews (booking_id, reviewer_id, reviewee_id, rating, review_text, is_public) VALUES
(1, 5, 2, 5, 'Excellent service! John is very professional and gave me exactly the haircut I wanted.', TRUE),
(2, 6, 3, 4, 'Great makeup and hair styling. Mary is talented and friendly. Will definitely book again.', TRUE);

-- Sample coupons
INSERT INTO coupons (code, description, discount_type, discount_value, minimum_amount, usage_limit, valid_from, valid_until, is_active, created_by) VALUES
('WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 500.00, 100, '2024-01-01', '2024-12-31', TRUE, 1),
('SAVE50', 'Save KES 50 on bookings above KES 1000', 'fixed', 50.00, 1000.00, 50, '2024-01-01', '2024-06-30', TRUE, 1);
