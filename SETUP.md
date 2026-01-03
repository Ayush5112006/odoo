# HRMS Setup Guide - Step by Step

## 🎯 Complete Setup Instructions

### Step 1: Database Setup

1. **Open phpMyAdmin**
   - Go to `http://localhost/phpmyadmin`
   - Username: `root`
   - Password: (leave blank)

2. **Create Database**
   - Click "New" on the left sidebar
   - Database name: `hrms_db`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"

3. **Import SQL File**
   - Click on the new `hrms_db` database
   - Click "Import" tab
   - Choose file: `hrms_db.sql` (from Downloads or project database folder)
   - Click "Import"

4. **Verify Tables Created**
   - You should see these tables:
     * users
     * attendance
     * leaves
     * salary
     * employee_details

### Step 2: File Placement

Ensure HRMS folder is in the correct location:
- **Path**: `C:\xampp\htdocs\hrms\`
- All files should be placed here

### Step 3: Database Configuration

The configuration file is already set up correctly:
- **File**: `config/db.php`
- **Database**: `hrms_db`
- **User**: `root`
- **Password**: (empty)
- **Host**: `localhost`

### Step 4: Start Services

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" next to Apache
   - Click "Start" next to MySQL

2. **Verify Services**
   - Check that both show "Running" status
   - Port for Apache: 80
   - Port for MySQL: 3306

### Step 5: Access Application

1. **Open Web Browser**
   - Go to: `http://localhost/hrms/`
   - You should see the HRMS login page

2. **Login with Default Credentials**
   - Email: `admin@demo.com`
   - Password: `password`

3. **You're In!**
   - You should see the Admin Dashboard

## 🆘 If Something Goes Wrong

### Database Import Failed?
- Make sure `hrms_db` database exists
- Check file format is `.sql`
- Try importing again from phpMyAdmin

### Login Not Working?
- Verify database imported successfully (check users table)
- Check if admin user exists in database
- Clear browser cache and try again

### Can't Access localhost?
- Check if Apache is running (green status in XAMPP)
- Check port 80 is not in use by another application
- Restart XAMPP services

### PHP Errors?
- Check XAMPP logs: `C:\xampp\apache\logs\`
- Check MySQL is running
- Verify database connection in `config/db.php`

## 📋 Database Import Details

The imported `hrms_db.sql` includes:

### Pre-configured User
- **Email**: admin@demo.com
- **Password**: password (hashed as: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
- **Role**: admin
- **Status**: active
- **Company**: Demo Company

### Tables Created
1. **users** - User accounts and authentication
2. **attendance** - Daily attendance records
3. **leaves** - Leave applications and approvals
4. **salary** - Employee salary structure
5. **employee_details** - Employee information

### Indexes & Constraints
- All tables have primary keys
- Foreign key relationships configured
- Unique constraints on email and custom_id
- Auto-increment for IDs

## ✅ Verification Checklist

After setup, verify:

- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] `hrms_db` database exists in MySQL
- [ ] All 5 tables are present in `hrms_db`
- [ ] Can access `http://localhost/hrms/`
- [ ] Login page displays correctly
- [ ] Can login with admin@demo.com / password
- [ ] Admin dashboard loads
- [ ] Navigation menu appears
- [ ] Can navigate to different pages

## 🚀 First Steps After Login

1. **Explore Admin Dashboard**
   - View employee count
   - Check pending leaves
   - View attendance stats

2. **Add an Employee**
   - Click "Employees" in sidebar
   - Click "+ Add Employee"
   - Fill in details and save

3. **Test Employee Login**
   - Logout (click profile icon)
   - Register new company or create employee
   - Login with employee account
   - View employee dashboard

4. **Test Attendance**
   - As employee, click "Attendance"
   - Click "Checkin"
   - Click "Checkout"
   - Verify work hours calculated

5. **Test Leave Application**
   - Click "My Leaves"
   - Click "Apply Leave"
   - Submit application
   - Login as admin to approve

## 📞 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't connect to database | Check MySQL running, verify database exists |
| Login fails | Ensure users table has admin record |
| 404 errors | Check file paths, files must be in correct directories |
| Images not showing | Not critical - system shows initials instead |
| Attendance button disabled | Must check in first before checking out |

## 🎓 What's Included

✅ Full-functioning HRMS system
✅ Pre-configured database with sample data
✅ Admin account ready to use
✅ All pages and functionality working
✅ Security measures implemented
✅ Responsive design for all devices
✅ Complete documentation

---

**Setup should take 5-10 minutes. You're ready to use HRMS!**
