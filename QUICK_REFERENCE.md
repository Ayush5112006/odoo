# ⚡ HRMS Quick Reference

## 🔐 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@demo.com | password |

## 🌐 URLs

| Page | URL |
|------|-----|
| Login | http://localhost/hrms/auth/login.php |
| Register | http://localhost/hrms/auth/register.php |
| Home | http://localhost/hrms/ |
| Admin Dashboard | http://localhost/hrms/admin/dashboard.php |
| Employee Dashboard | http://localhost/hrms/employee/dashboard.php |

## 📚 Database Info

| Item | Value |
|------|-------|
| Database Name | hrms_db |
| Host | localhost |
| User | root |
| Password | (empty) |
| Port | 3306 |

## 👥 User Roles

### Admin
- Full system access
- Can add/manage employees
- Can approve/reject leaves
- Can manage attendance
- Can manage payroll
- Access to all reports

### HR
- Can view employees
- Can manage attendance
- Can approve/reject leaves
- Limited payroll access

### Employee
- View personal dashboard
- Check-in/Check-out
- Apply for leave
- View salary
- View profile

## 🎯 Main Features

### Admin Panel
```
Dashboard → Analytics & Quick Stats
Employees → Manage all employees
Attendance → View/manage daily records
Leaves → Approve/reject applications
Payroll → Manage salaries
```

### Employee Panel
```
Dashboard → Personal statistics
Attendance → Check-in/Check-out & history
Leaves → Apply & track applications
Salary → View salary details
Profile → Update personal information
```

## 📝 Common Tasks

### Add Employee
```
Admin > Employees > + Add Employee
→ Fill Form → Click "Add Employee"
```

### Check In/Out
```
Employee > Attendance
→ Click "Checkin" (morning)
→ Click "Checkout" (evening)
```

### Apply Leave
```
Employee > My Leaves > Apply Leave
→ Select Type, Dates, Reason
→ Click "Apply Leave"
```

### Approve Leave
```
Admin > Leaves > Pending
→ Click "Approve" or "Reject"
```

## 🔧 Database Tables

### users
- Store user accounts
- Roles: admin, hr, employee
- Email and password

### attendance
- Daily check-in/out
- Work hours tracking
- Status tracking

### leaves
- Leave applications
- Types: paid, sick, unpaid
- Status: pending, approved, rejected

### salary
- Salary structure
- Basic, HRA, Allowance
- Deductions: PF, Tax

### employee_details
- Department, designation
- Manager, location, join_date

## 🔑 Key Files

| File | Purpose |
|------|---------|
| config/db.php | Database connection |
| auth/login_process.php | Login logic |
| auth/register_process.php | Registration logic |
| includes/header.php | Navigation menu |
| admin/dashboard.php | Admin home |
| employee/dashboard.php | Employee home |

## 🆘 Troubleshooting

### Can't login?
- Check MySQL is running
- Verify admin@demo.com in users table
- Check password hashing

### Database connection error?
- Verify MySQL is running
- Check database name: hrms_db
- Check config/db.php

### 404 errors?
- Check file is in correct directory
- Verify path in code
- Check file names (case-sensitive)

## ✅ Setup Checklist

- [ ] MySQL running
- [ ] Apache running
- [ ] hrms_db database imported
- [ ] All tables created
- [ ] Can access http://localhost/hrms/
- [ ] Can login with admin@demo.com
- [ ] Dashboard shows

## 💡 Tips

- Default profile shows initials (no image needed)
- Work hours < 4 = Half-day status
- All times in 24-hour format
- Dates in YYYY-MM-DD format
- Currency symbol: ₹

## 📱 Responsive Design

- ✅ Works on Desktop (1920px+)
- ✅ Works on Tablet (768px-1024px)
- ✅ Works on Mobile (< 768px)
- ✅ Bootstrap 5 responsive grid

## 🔐 Security Notes

- Passwords hashed with BCrypt
- SQL injection prevention enabled
- Session-based authentication
- Role-based access control
- Input validation on all forms

## 📞 File Locations

```
C:\xampp\htdocs\hrms\
├── auth/           (login/register)
├── admin/          (admin pages)
├── employee/       (employee pages)
├── config/         (database config)
├── includes/       (header/footer)
├── attendance/     (checkin/checkout)
├── leave/          (leave management)
└── assets/         (css, js, images)
```

## 🚀 First Time User

1. **Login** with admin@demo.com
2. **Go to Employees** page
3. **Add an Employee** for testing
4. **Logout** from Admin
5. **Login** as new Employee
6. **Test Attendance** (Check-in/out)
7. **Test Leave** (Apply for leave)
8. **Login as Admin** to approve

## 📊 Data Flow

```
User Registration
    ↓
Create User Account
    ↓
Login with Credentials
    ↓
Role-based Dashboard
    ↓
Access Features Based on Role
    ↓
Actions (Attendance, Leave, etc.)
    ↓
Data Stored in MySQL
```

---

**For detailed help, see README.md and SETUP.md**
