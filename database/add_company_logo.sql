-- Add company_logo column to users table if it doesn't exist
ALTER TABLE `users` ADD COLUMN `company_logo` varchar(255) DEFAULT NULL AFTER `company_name`;
