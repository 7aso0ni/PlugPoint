-- Sample data for Users table
-- Note: Using password_hash for proper password hashing
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `phone`) VALUES
('John Smith', 'john.smith@example.com', '$2y$10$6SLhC5fzLlaAL1ycwuMoNe.JdfGr7caUPmLkGAVrY3FnLiWGDMkYW', 2, '555-123-4567'), -- password: smith123
('Emily Johnson', 'emily.johnson@example.com', '$2y$10$mJjnrXW.k9HoB2Zr5ULfB.YLKz7O3nA2hQxY0G3fQl/TYw5c14MYO', 3, '555-234-5678'), -- password: emily456
('Michael Davis', 'michael.davis@example.com', '$2y$10$l4KQf06LBNIyDeT2tSPmTOpvYhUOblQcMJNPm4qLEGjm7.RIJKr2a', 2, '555-345-6789'), -- password: michael789
('Sarah Wilson', 'sarah.wilson@example.com', '$2y$10$GxEY3BGWpT5UmQFvB0oGQOByvSu5wEpqSDACUYfGqZ0rlfwSwmS9O', 3, '555-456-7890'), -- password: sarah321
('Robert Brown', 'robert.brown@example.com', '$2y$10$pL9wAx5rqElZsgNO0tlvqO3oKXnSpSkVW0CxOBsB0Vdm5usfUSHSS', 1, '555-567-8901'); -- password: robert654

-- Sample data for ChargePoints table
INSERT INTO `ChargePoints` (`owner_id`, `address`, `latitude`, `longitude`, `price_per_kWh`, `availability`, `image_url`) VALUES
(18, '123 Oak Street, Seattle, WA 98101', 47.606209, -122.332071, 0.22, 1, 'images/chargepoint1.jpg'),
(18, '456 Pine Avenue, Portland, OR 97201', 45.523064, -122.676483, 0.19, 1, 'images/chargepoint2.jpg'),
(18, '789 Maple Boulevard, San Francisco, CA 94103', 37.773972, -122.431297, 0.24, 0, 'images/chargepoint3.jpg'),
(18, '321 Cedar Lane, Los Angeles, CA 90001', 34.052235, -118.243683, 0.21, 1, 'images/chargepoint4.jpg'),
(18, '654 Elm Court, New York, NY 10001', 40.712776, -74.005974, 0.27, 1, 'images/chargepoint5.jpg');

-- Sample data for Bookings table
INSERT INTO `Bookings` (`user_id`, `charge_point_id`, `booking_date`, `due_date`, `status`) VALUES
(18, 14, '2025-05-15 09:00:00', '2025-05-15 11:00:00', 'Approved'),
(18, 15, '2025-05-16 13:00:00', '2025-05-16 15:30:00', 'Pending'),
(18, 16, '2025-05-17 10:00:00', '2025-05-17 12:00:00', 'Declined'),
(18, 17, '2025-05-18 14:00:00', '2025-05-18 16:00:00', 'Approved'),
(18, 18, '2025-05-19 11:00:00', '2025-05-19 13:30:00', 'Canceled');

-- Sample data for Messages table
-- INSERT INTO `Messages` (`sender_id`, `receiver_id`, `message`) VALUES
-- (2, 1, 'Hello, I would like to inquire about your charging station availability next weekend.'),
-- (1, 2, 'Hi, yes the charging station will be available. What time would you like to book?'),
-- (3, 1, 'Is your charger compatible with a Tesla Model 3?'),
-- (1, 3, 'Yes, my charger has a J1772 connector which is compatible with Tesla using the adapter that came with your car.'),
-- (4, 5, 'Thank you for approving my booking request!');

-- Sample data for ChargerProducts table (created earlier)
-- If you haven't yet created this table, uncomment and run the table creation SQL first
/*
CREATE TABLE `ChargerProducts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `specs` varchar(255) NOT NULL,
  `connector` varchar(100) NOT NULL,
  `status` enum('Available', 'Limited', 'Premium') DEFAULT 'Available',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `reviews` int NOT NULL DEFAULT '0',
  `price` decimal(6,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
*/

INSERT INTO `ChargerProducts` (`title`, `type`, `specs`, `connector`, `status`, `rating`, `reviews`, `price`, `image_url`) VALUES
('Portable Level 2 Charger', 'Level 2', 'J1772 Connector • 7.2kW', 'J1772', 'Available', 4.7, 32, 15.99, 'images/portable_level2.jpg'),
('Home Wall Connector', 'Level 2', 'J1772 Connector • 11.5kW', 'J1772', 'Limited', 4.9, 45, 18.50, 'images/wall_connector.jpg'),
('Tesla Destination Charger', 'Level 2', 'Tesla Connector • 11.5kW', 'Tesla', 'Premium', 5.0, 28, 22.99, 'images/tesla_destination.jpg'),
('Compact Travel Charger', 'Level 1', 'Standard Outlet • 1.4kW', 'Standard', 'Available', 4.2, 19, 9.99, 'images/compact_travel.jpg'),
('DC Fast Charger', 'DC Fast', 'CCS & CHAdeMO • 50kW', 'CCS|CHAdeMO', 'Limited', 4.8, 12, 39.99, 'images/dc_fast.jpg');

-- Sample data for ProductRentals table
-- If you haven't yet created this table, uncomment and run the table creation SQL first
/*
CREATE TABLE `ProductRentals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `rental_start` datetime NOT NULL,
  `rental_end` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Canceled','Completed') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `productrentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productrentals_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `ChargerProducts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
*/

INSERT INTO `ProductRentals` (`user_id`, `product_id`, `rental_start`, `rental_end`, `total_price`, `status`) VALUES
(18, 1, '2025-05-10 12:00:00', '2025-05-15 12:00:00', 79.95, 'Confirmed'),
(18, 2, '2025-05-12 10:00:00', '2025-05-14 10:00:00', 37.00, 'Pending'),
(18, 3, '2025-05-20 09:00:00', '2025-05-25 09:00:00', 114.95, 'Confirmed'),
(18, 4, '2025-05-08 14:00:00', '2025-05-09 14:00:00', 9.99, 'Completed'),
(18, 5, '2025-05-15 11:00:00', '2025-05-17 11:00:00', 79.98, 'Canceled');