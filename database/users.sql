CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_id` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','hr','employee') DEFAULT 'employee',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `otp` varchar(6) DEFAULT NULL,
  `otp_time` datetime DEFAULT NULL,
  `email_verified` int(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
