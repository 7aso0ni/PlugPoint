-- Insert roles first
INSERT INTO Roles (id, role_name) VALUES
(1, 'admin'),
(2, 'homeowner'),
(3, 'user');

-- Insert users
INSERT INTO Users (id, name, email, password, role_id, phone) VALUES
(1, 'John Smith', 'john.smith@example.com', '$2y$10$6SLhC5fzLlaAL1ycwuMoNe.JdfGr7caUPmLkGAVrY3FnLiWGDMkYW', 2, '555-123-4567'),
(2, 'Emily Johnson', 'emily.johnson@example.com', '$2y$10$mJjnrXW.k9HoB2Zr5ULfB.YLKz7O3nA2hQxY0G3fQl/TYw5c14MYO', 3, '555-234-5678'),
(3, 'Michael Davis', 'michael.davis@example.com', '$2y$10$l4KQf06LBNIyDeT2tSPmTOpvYhUOblQcMJNPm4qLEGjm7.RIJKr2a', 2, '555-345-6789'),
(4, 'Sarah Wilson', 'sarah.wilson@example.com', '$2y$10$GxEY3BGWpT5UmQFvB0oGQOByvSu5wEpqSDACUYfGqZ0rlfwSwmS9O', 3, '555-456-7890'),
(5, 'Robert Brown', 'robert.brown@example.com', '$2y$10$pL9wAx5rqElZsgNO0tlvqO3oKXnSpSkVW0CxOBsB0Vdm5usfUSHSS', 1, '555-567-8901');

-- Insert charge points (use owner_id = 1 for homeowner)
INSERT INTO ChargePoints (owner_id, address, latitude, longitude, price_per_kWh, availability, image_url) VALUES
(1, '123 Oak Street, Seattle, WA 98101', 47.606209, -122.332071, 0.22, 1, 'images/chargepoint1.jpg'),
(1, '456 Pine Avenue, Portland, OR 97201', 45.523064, -122.676483, 0.19, 1, 'images/chargepoint2.jpg'),
(1, '789 Maple Boulevard, San Francisco, CA 94103', 37.773972, -122.431297, 0.24, 0, 'images/chargepoint3.jpg'),
(1, '321 Cedar Lane, Los Angeles, CA 90001', 34.052235, -118.243683, 0.21, 1, 'images/chargepoint4.jpg'),
(1, '654 Elm Court, New York, NY 10001', 40.712776, -74.005974, 0.27, 1, 'images/chargepoint5.jpg');

-- Insert bookings (use user_id = 2 for normal user)
INSERT INTO Bookings (user_id, charge_point_id, booking_date, due_date, status) VALUES
(2, 1, '2025-05-15 09:00:00', '2025-05-15 11:00:00', 'Approved'),
(2, 2, '2025-05-16 13:00:00', '2025-05-16 15:30:00', 'Pending'),
(2, 3, '2025-05-17 10:00:00', '2025-05-17 12:00:00', 'Declined'),
(2, 4, '2025-05-18 14:00:00', '2025-05-18 16:00:00', 'Approved'),
(2, 5, '2025-05-19 11:00:00', '2025-05-19 13:30:00', 'Canceled');

-- Insert charger products
INSERT INTO ChargerProducts (title, type, specs, connector, status, rating, reviews, price, image_url) VALUES
('Portable Level 2 Charger', 'Level 2', 'J1772 Connector • 7.2kW', 'J1772', 'Available', 4.7, 32, 15.99, 'images/portable_level2.jpg'),
('Home Wall Connector', 'Level 2', 'J1772 Connector • 11.5kW', 'J1772', 'Limited', 4.9, 45, 18.50, 'images/wall_connector.jpg'),
('Tesla Destination Charger', 'Level 2', 'Tesla Connector • 11.5kW', 'Tesla', 'Premium', 5.0, 28, 22.99, 'images/tesla_destination.jpg'),
('Compact Travel Charger', 'Level 1', 'Standard Outlet • 1.4kW', 'Standard', 'Available', 4.2, 19, 9.99, 'images/compact_travel.jpg'),
('DC Fast Charger', 'DC Fast', 'CCS & CHAdeMO • 50kW', 'CCS|CHAdeMO', 'Limited', 4.8, 12, 39.99, 'images/dc_fast.jpg');

-- Insert rentals (use user_id = 2)
INSERT INTO ProductRentals (user_id, product_id, rental_start, rental_end, total_price, status) VALUES
(2, 1, '2025-05-10 12:00:00', '2025-05-15 12:00:00', 79.95, 'Confirmed'),
(2, 2, '2025-05-12 10:00:00', '2025-05-14 10:00:00', 37.00, 'Pending'),
(2, 3, '2025-05-20 09:00:00', '2025-05-25 09:00:00', 114.95, 'Confirmed'),
(2, 4, '2025-05-08 14:00:00', '2025-05-09 14:00:00', 9.99, 'Completed'),
(2, 5, '2025-05-15 11:00:00', '2025-05-17 11:00:00', 79.98, 'Canceled');
