-- Enhanced bookings with realistic Kenyan scenarios
INSERT INTO bookings (booking_number, customer_id, provider_id, service_id, booking_date, booking_time, duration, total_amount, status, payment_status, location_type, service_address) VALUES
('BK001', 10, 2, 1, '2024-01-20', '10:30:00', 60, 1200.00, 'completed', 'paid', 'at_provider', 'Muthomi Barbershop, Tom Mboya Street, Nairobi CBD'),
('BK002', 11, 3, 3, '2024-01-22', '14:00:00', 180, 5000.00, 'confirmed', 'paid', 'at_provider', 'Glamour Beauty Salon, Madaraka, Nairobi'),
('BK003', 12, 4, 5, '2024-01-25', '09:00:00', 60, 2500.00, 'in_progress', 'paid', 'at_customer', 'Hurlingham Apartments, Flat 5B'),
('BK004', 13, 5, 7, '2024-01-28', '11:00:00', 60, 2500.00, 'pending', 'pending', 'at_provider', 'Tech Fixers Shop, Westlands, Nairobi'),
('BK005', 14, 6, 9, '2024-02-01', '09:30:00', 240, 3500.00, 'confirmed', 'paid', 'at_customer', 'South B Residence, Nairobi'),
('BK006', 10, 7, 11, '2024-02-03', '15:00:00', 60, 1200.00, 'completed', 'paid', 'both', 'Online session via Zoom'),
('BK007', 11, 8, 13, '2024-02-05', '07:00:00', 60, 2000.00, 'confirmed', 'paid', 'at_provider', 'FitLife Gym, Lavington, Nairobi'),
('BK008', 12, 9, 15, '2024-02-10', '10:00:00', 0, 50000.00, 'pending', 'pending', 'both', 'TBD - Planning stage');

-- Payments for bookings
INSERT INTO payments (booking_id, payment_method, transaction_id, amount, status, processed_at) VALUES
(1, 'mpesa', 'MPESA_BK001', 1200.00, 'completed', '2024-01-20 10:00:00'),
(2, 'card', 'CARD_BK002', 5000.00, 'completed', '2024-01-22 13:30:00'),
(3, 'mpesa', 'MPESA_BK003', 2500.00, 'completed', '2024-01-25 08:30:00'),
(4, 'paypal', 'PAYPAL_BK004', 2500.00, 'pending', NULL),
(5, 'bank_transfer', 'BANK_BK005', 3500.00, 'completed', '2024-02-01 09:00:00'),
(6, 'mpesa', 'MPESA_BK006', 1200.00, 'completed', '2024-02-03 14:30:00'),
(7, 'card', 'CARD_BK007', 2000.00, 'completed', '2024-02-05 06:30:00');

-- Reviews and ratings with realistic feedback
INSERT INTO reviews (booking_id, reviewer_id, reviewee_id, rating, review_text, is_public) VALUES
(1, 10, 2, 5, 'Exceptional service! Muthomi is a true professional. The hot towel treatment was incredibly relaxing and my haircut looks perfect. Will definitely be back!', TRUE),
(2, 11, 3, 5, 'Outstanding bridal package. Grace transformed me for my wedding day. The makeup lasted all day and the hairstyle was exactly what I wanted. Highly recommended!', TRUE),
(3, 12, 4, 4, 'Quick response to my plumbing emergency. Peter arrived within 30 minutes as promised and fixed the issue efficiently. Fair pricing and professional service.', TRUE),
(6, 10, 7, 5, 'David is an excellent tutor. My son''s math grades have improved significantly since starting sessions with him. Patient, knowledgeable, and adapts to learning style.', TRUE),
(7, 11, 8, 4, 'Great personal training session with Ann. She''s motivating and creates a workout plan tailored to your goals. The gym is well-equipped and clean.', TRUE);

-- Notifications for users
INSERT INTO notifications (user_id, title, message, type, is_read, action_url) VALUES
(2, 'New Booking Request', 'You have a new booking request for Premium Haircut & Beard Grooming on Feb 5th at 2:00 PM', 'booking', FALSE, '/provider/bookings'),
(3, 'Booking Confirmation', 'Your booking for Bridal Makeup & Hair Styling has been confirmed for Jan 22nd', 'booking', TRUE, '/customer/bookings'),
(10, 'Service Reminder', 'Reminder: Your Premium Haircut appointment is tomorrow at 10:30 AM', 'booking', FALSE, '/customer/bookings'),
(5, 'Payment Received', 'Payment of KES 2,500 received for Smartphone Screen Replacement booking', 'payment', TRUE, '/provider/payments'),
(14, 'New Message', 'Glamour Beauty Salon has sent you a message regarding your booking', 'message', FALSE, '/customer/messages');

-- Conversations between users
INSERT INTO conversations (booking_id, customer_id, provider_id, subject) VALUES
(2, 11, 3, 'Bridal Makeup Consultation'),
(4, 13, 5, 'Laptop Repair Inquiry'),
(8, 12, 9, 'Wedding Planning Consultation');

-- Messages in conversations
INSERT INTO messages (conversation_id, sender_id, message, is_read) VALUES
(1, 11, 'Hi Grace, I''m interested in the bridal package for my wedding on March 15th. Can we schedule a consultation?', FALSE),
(1, 3, 'Hello Faith, congratulations on your upcoming wedding! I''d be happy to schedule a consultation. How about this Friday at 2 PM?', FALSE),
(2, 13, 'Hi Samuel, my laptop screen is cracked. Do you offer screen replacement for Dell laptops?', TRUE),
(2, 5, 'Hi Dennis, yes we do offer Dell screen replacements. Please bring your laptop to our shop and we''ll give you a quote.', TRUE),
(3, 12, 'Hello James, I''m planning a wedding for June and would like to discuss packages.', FALSE),
(3, 9, 'Hi Carol, I''d love to help with your wedding planning. When would you like to meet to discuss details?', FALSE);

-- Favorites (services saved by customers)
INSERT INTO favorites (user_id, service_id) VALUES
(10, 1),
(10, 3),
(11, 5),
(11, 7),
(12, 9),
(13, 11),
(14, 13),
(14, 15);

-- Coupons with realistic Kenyan promotions
INSERT INTO coupons (code, description, discount_type, discount_value, minimum_amount, usage_limit, used_count, valid_from, valid_until, is_active, created_by) VALUES
('WELCOME20', 'Welcome discount for new customers', 'percentage', 20.00, 1000.00, 200, 15, '2024-01-01', '2024-12-31', TRUE, 1),
('SAVE100', 'Save KES 100 on bookings above KES 2000', 'fixed', 100.00, 2000.00, 100, 8, '2024-01-01', '2024-06-30', TRUE, 1),
('FEBRUARY15', 'February special discount', 'percentage', 15.00, 1500.00, 50, 3, '2024-02-01', '2024-02-29', TRUE, 1),
('LOYALTY10', 'Loyalty discount for returning customers', 'percentage', 10.00, 500.00, 300, 22, '2024-01-01', '2024-12-31', TRUE, 1);

-- User coupon usage
INSERT INTO user_coupon_usage (user_id, coupon_id, used_at) VALUES
(10, 1, '2024-01-20 09:30:00'),
(11, 4, '2024-01-22 13:00:00'),
(13, 2, '2024-01-28 10:30:00'),
(14, 1, '2024-02-01 08:45:00');

-- Subscription plans for service providers
INSERT INTO subscription_plans (name, description, price, duration_days, features, is_active, sort_order) VALUES
('Basic Plan', 'Essential plan for new service providers', 1500.00, 30, '["List up to 5 services", "Basic analytics", "Email support"]', TRUE, 1),
('Professional Plan', 'Advanced features for growing businesses', 3500.00, 30, '["List up to 20 services", "Advanced analytics", "Priority support", "Featured listing"]', TRUE, 2),
('Premium Plan', 'All features for established businesses', 6500.00, 30, '["Unlimited services", "Premium analytics", "24/7 support", "Top placement", "Marketing boost"]', TRUE, 3);

-- User subscriptions
INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, payment_id) VALUES
(2, 2, '2024-01-01', '2024-01-31', 'active', 1),
(3, 3, '2024-01-01', '2024-01-31', 'active', 2),
(4, 2, '2024-01-15', '2024-02-14', 'active', 3),
(5, 1, '2024-01-10', '2024-02-09', 'active', 4);

-- System activity logs
INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address) VALUES
(10, 'created', 'bookings', 1, NULL, '{"booking_number":"BK001","customer_id":10,"provider_id":2,"service_id":1}', '192.168.1.100'),
(2, 'updated', 'bookings', 1, '{"status":"pending"}', '{"status":"completed"}', '192.168.1.101'),
(11, 'created', 'bookings', 2, NULL, '{"booking_number":"BK002","customer_id":11,"provider_id":3,"service_id":3}', '192.168.1.102'),
(3, 'updated', 'bookings', 2, '{"status":"pending"}', '{"status":"confirmed"}', '192.168.1.103'),
(1, 'created', 'services', 16, NULL, '{"provider_id":9,"category_id":8,"title":"Wedding Event Planning"}', '192.168.1.1');