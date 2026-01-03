-- Add profile_photo column to users table if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN `profile_photo` VARCHAR(255) DEFAULT NULL 
AFTER `company_logo`;
