-- Enhanced Sample Data for JobsMtaani Platform
-- This script adds more comprehensive sample data for testing all dashboard features

USE jobsmtaani;

-- Additional service providers
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status, email_verified) VALUES
('kevin_electrician', 'kevin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kevin', 'Omondi', '+254711111111', 'service_provider', 'active', TRUE),
('lisa_catering', 'lisa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa', 'Kariuki', '+254722222222', 'service_provider', 'active', TRUE),
('tom_tutor', 'tom@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tom', 'Waweru', '+254733333333', 'service_provider', 'active', TRUE),
('susan_cleaning', 'susan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Susan', 'Nduta', '+254744444444', 'service_provider', 'active', TRUE),
('mike_transport', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Gitau', '+254755555555', 'service_provider', 'active', TRUE);

-- Additional customers
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status, email_verified) VALUES
('alice_customer', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Mutua', '+254766666666', 'customer', 'active', TRUE),
('bob_customer', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Maina', '+254777777777', 'customer', 'active', TRUE),
('carol_customer', 'carol@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carol', 'Wambui', '+254788888888', 'customer', 'active', TRUE),
('dave_customer', 'dave@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dave', 'Kipchumba', '+254799999999', 'customer', 'active', TRUE);

-- Additional user profiles
INSERT INTO user_profiles (user_id, bio, address, city, state, country) VALUES
(7, 'Certified electrician with 8+ years experience', 'Industrial Area, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(8, 'Professional catering service for all occasions', 'Lavington, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(9, 'Mathematics and Science tutor with 10+ years experience', 'Hurlingham, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(10, 'Professional cleaning service for homes and offices', 'Donholm, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(11, 'Reliable transport and delivery services', 'Machakos', 'Machakos', 'Machakos County', 'Kenya'),
(12, 'Regular customer looking for quality services', 'Rongai, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(13, 'Frequent user of home maintenance services', 'Westlands, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(14, 'Regular customer for beauty and wellness services', 'Karen, Nairobi', 'Nairobi', 'Nairobi County', 'Kenya'),
(15, 'Business owner looking for reliable service providers', 'Thika', 'Thika', 'Kiambu County', 'Kenya');

-- Additional services with images
INSERT INTO services (provider_id, category_id, title, slug, description, short_description, price, price_type, duration, location_type, is_active, images) VALUES
(7, 2, 'Electrical Installation & Repair', 'electrical-installation-repair', 'Professional electrical installation and repair services for homes and businesses. Licensed electrician with 8+ years experience.', 'Expert electrical installation and repair', 2000.00, 'hourly', 60, 'at_customer', TRUE, '["/assets/images/plumbing-repair.jpg"]'),
(7, 2, 'Lighting Fixture Installation', 'lighting-fixture-installation', 'Installation of new lighting fixtures including LED lights, chandeliers, and outdoor lighting.', 'Professional lighting fixture installation', 1500.00, 'fixed', 90, 'both', TRUE, '["/assets/images/house-cleaning.png"]'),
(8, 8, 'Corporate Catering Services', 'corporate-catering-services', 'Professional catering for corporate events, meetings, and conferences. Custom menus for any occasion.', 'Corporate catering for events', 5000.00, 'negotiable', 240, 'at_customer', TRUE, '["/assets/images/event-photography.png"]'),
(8, 8, 'Wedding Catering', 'wedding-catering', 'Specialized wedding catering with custom menus and professional service for your special day.', 'Wedding catering services', 8000.00, 'negotiable', 480, 'at_customer', TRUE, '["/assets/images/diverse-group-profile.png"]'),
(9, 5, 'Mathematics Tutoring', 'mathematics-tutoring', 'Professional mathematics tutoring for students of all levels. Specialized in high school and college mathematics.', 'Expert mathematics tutoring', 1200.00, 'hourly', 60, 'both', TRUE, '["/assets/images/math-tutoring.png"]'),
(9, 5, 'Science Tutoring (Physics & Chemistry)', 'science-tutoring', 'Comprehensive tutoring in Physics and Chemistry for high school and college students.', 'Science tutoring for students', 1500.00, 'hourly', 90, 'both', TRUE, '["/assets/images/man-profile.png"]'),
(10, 6, 'Deep House Cleaning', 'deep-house-cleaning', 'Professional deep cleaning service for homes including kitchen, bathroom, and all rooms.', 'Thorough house cleaning service', 3500.00, 'fixed', 240, 'at_customer', TRUE, '["/assets/images/house-cleaning.png"]'),
(10, 6, 'Office Cleaning Service', 'office-cleaning-service', 'Regular office cleaning service for businesses. Flexible scheduling and professional staff.', 'Professional office cleaning', 5000.00, 'negotiable', 180, 'at_customer', TRUE, '["/assets/images/diverse-group-of-service-providers.jpg"]'),
(11, 3, 'Reliable Taxi Service', 'reliable-taxi-service', '24/7 reliable taxi service for individuals and groups. Well-maintained vehicles and professional drivers.', '24/7 taxi service', 800.00, 'fixed', 30, 'at_customer', TRUE, '["/assets/images/car-wash-detailing.jpg"]'),
(11, 3, 'Package Delivery', 'package-delivery', 'Fast and reliable package delivery service within Nairobi and surrounding areas.', 'Package delivery service', 500.00, 'fixed', 60, 'at_customer', TRUE, '["/assets/images/diverse-group-profile.png"]');

-- Additional provider availability
INSERT INTO provider_availability (provider_id, day_of_week, start_time, end_time, is_available) VALUES
-- Kevin Electrician (user_id: 7)
(7, 1, '08:00:00', '18:00:00', TRUE), -- Monday
(7, 2, '08:00:00', '18:00:00', TRUE), -- Tuesday
(7, 3, '08:00:00', '18:00:00', TRUE), -- Wednesday
(7, 4, '08:00:00', '18:00:00', TRUE), -- Thursday
(7, 5, '08:00:00', '18:00:00', TRUE), -- Friday
(7, 6, '09:00:00', '17:00:00', TRUE), -- Saturday

-- Lisa Catering (user_id: 8)
(8, 1, '07:00:00', '20:00:00', TRUE), -- Monday
(8, 2, '07:00:00', '20:00:00', TRUE), -- Tuesday
(8, 3, '07:00:00', '20:00:00', TRUE), -- Wednesday
(8, 4, '07:00:00', '20:00:00', TRUE), -- Thursday
(8, 5, '07:00:00', '20:00:00', TRUE), -- Friday
(8, 6, '07:00:00', '22:00:00', TRUE), -- Saturday
(8, 0, '08:00:00', '20:00:00', TRUE), -- Sunday

-- Tom Tutor (user_id: 9)
(9, 1, '16:00:00', '20:00:00', TRUE), -- Monday
(9, 2, '16:00:00', '20:00:00', TRUE), -- Tuesday
(9, 3, '16:00:00', '20:00:00', TRUE), -- Wednesday
(9, 4, '16:00:00', '20:00:00', TRUE), -- Thursday
(9, 5, '16:00:00', '20:00:00', TRUE), -- Friday
(9, 6, '09:00:00', '17:00:00', TRUE), -- Saturday
(9, 0, '09:00:00', '17:00:00', TRUE), -- Sunday

-- Susan Cleaning (user_id: 10)
(10, 1, '08:00:00', '18:00:00', TRUE), -- Monday
(10, 2, '08:00:00', '18:00:00', TRUE), -- Tuesday
(10, 3, '08:00:00', '18:00:00', TRUE), -- Wednesday
(10, 4, '08:00:00', '18:00:00', TRUE), -- Thursday
(10, 5, '08:00:00', '18:00:00', TRUE), -- Friday
(10, 6, '08:00:00', '16:00:00', TRUE), -- Saturday

-- Mike Transport (user_id: 11)
(11, 1, '06:00:00', '22:00:00', TRUE), -- Monday
(11, 2, '06:00:00', '22:00:00', TRUE), -- Tuesday
(11, 3, '06:00:00', '22:00:00', TRUE), -- Wednesday
(11, 4, '06:00:00', '22:00:00', TRUE), -- Thursday
(11, 5, '06:00:00', '22:00:00', TRUE), -- Friday
(11, 6, '06:00:00', '22:00:00', TRUE), -- Saturday
(11, 0, '07:00:00', '21:00:00', TRUE); -- Sunday

-- Additional bookings
INSERT INTO bookings (booking_number, customer_id, provider_id, service_id, booking_date, booking_time, duration, total_amount, status, payment_status, location_type, service_address) VALUES
('BK004', 12, 7, 16, '2025-10-10', '14:00:00', 60, 2000.00, 'confirmed', 'paid', 'at_customer', 'Rongai Apartments, Apt 5A'),
('BK005', 13, 8, 18, '2025-10-12', '18:00:00', 240, 5000.00, 'pending', 'pending', 'at_customer', 'Westlands Office Complex'),
('BK006', 14, 9, 20, '2025-10-11', '16:00:00', 60, 1200.00, 'in_progress', 'paid', 'at_provider', 'Karen Learning Center'),
('BK007', 15, 10, 22, '2025-10-15', '09:00:00', 240, 3500.00, 'confirmed', 'paid', 'at_customer', 'Thika Business Park'),
('BK008', 5, 11, 24, '2025-10-08', '07:30:00', 30, 800.00, 'completed', 'paid', 'at_customer', 'Kilimani Shopping Center'),
('BK009', 6, 2, 1, '2025-10-09', '10:00:00', 45, 800.00, 'completed', 'paid', 'at_provider', 'Westlands Barber Shop'),
('BK010', 12, 3, 3, '2025-10-13', '14:00:00', 120, 2500.00, 'confirmed', 'paid', 'at_provider', 'Karen Beauty Salon');

-- Additional reviews
INSERT INTO reviews (booking_id, reviewer_id, reviewee_id, rating, review_text, is_public) VALUES
(4, 12, 7, 5, 'Kevin did an excellent job installing new lighting in my house. Professional and efficient service!', TRUE),
(6, 14, 9, 4, 'Tom is a great tutor. My son''s math grades have improved significantly since starting sessions with him.', TRUE),
(7, 15, 10, 5, 'Susan and her team did a fantastic deep cleaning of our office. Highly recommend!', TRUE),
(8, 15, 11, 4, 'Mike is reliable and punctual. Got me to my meeting on time despite heavy traffic.', TRUE),
(9, 5, 2, 5, 'John is the best barber in Nairobi! Always gives me exactly what I want.', TRUE),
(10, 6, 3, 5, 'Mary did an amazing job for my birthday party makeup and hair. Everyone was impressed!', TRUE);

-- Additional notifications
INSERT INTO notifications (user_id, title, message, type, is_read, action_url) VALUES
(7, 'New Booking Request', 'You have a new booking request for Electrical Installation & Repair on Oct 10, 2025 at 2:00 PM', 'booking', FALSE, '/provider/bookings'),
(8, 'Booking Confirmation', 'Your booking for Corporate Catering Services has been confirmed', 'booking', FALSE, '/customer/bookings'),
(9, 'Payment Received', 'Payment of KES 1,200 received for Mathematics Tutoring session', 'payment', FALSE, '/provider/earnings'),
(10, 'New Review', 'You received a 5-star review from Susan Nduta', 'review', FALSE, '/provider/reviews'),
(11, 'Booking Reminder', 'Reminder: You have a taxi booking tomorrow at 7:30 AM', 'booking', FALSE, '/customer/bookings'),
(2, 'Booking Update', 'Your booking with Jane Akinyi has been completed', 'booking', FALSE, '/provider/bookings'),
(3, 'New Message', 'Mary Wanjiku sent you a message about your upcoming booking', 'system', FALSE, '/messages'),
(5, 'Special Offer', 'Get 15% off your next booking with John Kamau', 'promotion', FALSE, '/services');

-- Additional activity logs
INSERT INTO activity_logs (user_id, action, table_name, record_id) VALUES
(12, 'Created booking', 'bookings', 4),
(13, 'Viewed service', 'services', 18),
(14, 'Updated profile', 'user_profiles', 9),
(15, 'Made payment', 'payments', 7),
(5, 'Left review', 'reviews', 6),
(6, 'Booked service', 'bookings', 10),
(7, 'Updated availability', 'provider_availability', 1),
(8, 'Added service', 'services', 18);
