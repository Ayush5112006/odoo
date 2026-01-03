CREATE TABLE IF NOT EXISTS `salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL UNIQUE,
  `basic` decimal(10,2) DEFAULT 0,
  `hra` decimal(10,2) DEFAULT 0,
  `allowance` decimal(10,2) DEFAULT 0,
  `pf` decimal(10,2) DEFAULT 0,
  `tax` decimal(10,2) DEFAULT 0,
  `gross` decimal(10,2) GENERATED ALWAYS AS (basic + hra + allowance) STORED,
  `net` decimal(10,2) GENERATED ALWAYS AS (gross - pf - tax) STORED,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
