CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `work_hours` varchar(10) DEFAULT NULL,
  `status` enum('present','absent','halfday') DEFAULT 'present',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
