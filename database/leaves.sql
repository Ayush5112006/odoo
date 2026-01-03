CREATE TABLE IF NOT EXISTS `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('paid','sick','unpaid') DEFAULT 'paid',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
