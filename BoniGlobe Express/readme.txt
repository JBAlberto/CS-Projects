1. RUN XAMPP 
2. START MYSQL & APACHE
3. OPEN PHPMYADMIN
4. CREATE DATABASE : testdbproject

5. GO TO SQL TAB
RUN:
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$5JA086YZtIZyVYocl8yoO.owGr4cYO9QRjyTDQdu5lJcvd/AdZGdO', '2025-05-22 07:10:37', 'admin');

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `number` int(50) NOT NULL,
  `location_1` varchar(255) NOT NULL,
  `location_2` varchar(255) NOT NULL,
  `packtype` varchar(100) NOT NULL,
  `packagingType` enum('Primary','Secondary','Tertiary') NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0,
  `sender_id` int(11) NOT NULL,
  `rejected` tinyint(1) DEFAULT 0,
  `delivered` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`);

ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE packages
MODIFY COLUMN number VARCHAR(15);

COMMIT;
