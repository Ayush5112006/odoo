#!/bin/bash
# HRMS Database Setup

echo "Installing HRMS Database..."

# Create database and import SQL
mysql -u root -e "CREATE DATABASE IF NOT EXISTS hrms_db;"
mysql -u root hrms_db < ../hrms_db.sql

echo "Database setup complete!"
echo ""
echo "Admin Login:"
echo "Email: admin@demo.com"
echo "Password: password"
echo ""
echo "To start using HRMS, open: http://localhost/hrms/"
