# 🧪 HRMS - Test Scenarios & Workflows

## ✅ Complete Test Plan

Use these scenarios to verify your HRMS is working correctly.

---

## Test Scenario 1: Admin Registration & Login

### Objective
Verify that new companies can register and admin can login.

### Steps
1. Open http://localhost/hrms/auth/register.php
2. Enter:
   - Company Name: "Test Company"
   - Admin Name: "John Manager"
   - Email: "john@testcompany.com"
   - Password: "test123"
3. Click "Register"

### Expected Results
✅ Automatically logged in
✅ Redirected to admin dashboard
✅ User session created
✅ Can see "Test Company" in admin profile

---

## Test Scenario 2: Admin Login with Default Credentials

### Objective
Verify default admin account works.

### Steps
1. Open http://localhost/hrms/auth/login.php
2. Enter:
   - Email: admin@demo.com
   - Password: password
3. Click "Login"

### Expected Results
✅ Login successful
✅ Redirected to admin dashboard
✅ Session created
✅ Navigation menu shows

---

## Test Scenario 3: Add Employee

### Objective
Admin adds a new employee to the system.

### Steps
1. Login as admin
2. Navigate to "Employees"
3. Click "+ Add Employee"
4. Fill in:
   - First Name: "Robert"
   - Last Name: "Johnson"
   - Email: "robert@company.com"
   - Password: "emp123"
   - Department: "IT"
   - Designation: "Developer"
5. Click "Add Employee"

### Expected Results
✅ Employee added to database
✅ Redirected to employees list
✅ New employee visible in list
✅ Email appears in user table
✅ Can login with new credentials

---

## Test Scenario 4: Employee Dashboard

### Objective
Verify employee can view their dashboard.

### Steps
1. Login as the new employee created in Scenario 3
   - Email: robert@company.com
   - Password: emp123
2. Check dashboard loads

### Expected Results
✅ Redirected to employee dashboard
✅ Shows: Today Attendance, Pending Leaves, This Month, Salary
✅ Shows employee's first name
✅ Quick action buttons available

---

## Test Scenario 5: Employee Check-In

### Objective
Employee checks in for the day.

### Steps
1. Login as employee
2. Go to "Attendance"
3. Click "Checkin"

### Expected Results
✅ Check-in time recorded
✅ Status shows "Checked In"
✅ Today's attendance shows in history
✅ Record appears in database

---

## Test Scenario 6: Employee Check-Out

### Objective
Employee checks out at end of day.

### Prerequisites
- Must have checked in (Test Scenario 5)

### Steps
1. Go to "Attendance"
2. Click "Checkout"

### Expected Results
✅ Check-out time recorded
✅ Work hours calculated
✅ Status shows "Checked Out"
✅ Attendance history updated
✅ Work hours visible in record

---

## Test Scenario 7: Apply for Leave

### Objective
Employee applies for leave.

### Steps
1. Login as employee
2. Go to "My Leaves"
3. Click "Apply Leave" button
4. Fill in:
   - Leave Type: "Paid Leave"
   - From Date: (future date)
   - To Date: (future date, 3 days)
   - Reason: "Medical appointment"
5. Click "Apply Leave"

### Expected Results
✅ Application submitted
✅ Status shows "Pending"
✅ Applied date recorded
✅ Visible in leave history
✅ Notification in admin panel

---

## Test Scenario 8: Approve Leave (Admin)

### Objective
Admin approves pending leave.

### Prerequisites
- Employee has applied for leave (Test Scenario 7)

### Steps
1. Login as admin
2. Go to "Leaves"
3. Check "Pending" tab
4. Find the leave application
5. Click "Approve"

### Expected Results
✅ Leave status changes to "Approved"
✅ Removed from Pending tab
✅ Appears in Approved tab
✅ Employee can see approval

---

## Test Scenario 9: Reject Leave (Admin)

### Objective
Admin rejects a leave application.

### Prerequisites
- Employee has applied for another leave

### Steps
1. Login as admin
2. Go to "Leaves"
3. Find a pending leave
4. Click "Reject"

### Expected Results
✅ Leave status changes to "Rejected"
✅ Removed from Pending tab
✅ Appears in Rejected tab
✅ Employee notified

---

## Test Scenario 10: View Attendance Report

### Objective
Admin views attendance for a specific date.

### Steps
1. Login as admin
2. Go to "Attendance"
3. Select today's date from calendar
4. Click "Filter"

### Expected Results
✅ Shows attendance for selected date
✅ Lists all employees
✅ Shows check-in, check-out, work hours
✅ Monthly report displays

---

## Test Scenario 11: View Salary Information

### Objective
Employee views salary information.

### Steps
1. Login as employee
2. Go to "Salary"
3. View salary details

### Expected Results
✅ Shows salary structure
✅ Displays: Basic, HRA, Allowance, Gross
✅ Shows deductions: PF, Tax
✅ Shows Net salary

---

## Test Scenario 12: Update Employee Details (Admin)

### Objective
Admin edits employee information.

### Steps
1. Login as admin
2. Go to "Employees"
3. Find employee in list
4. Click "Edit"
5. Update some fields
6. Click "Update"

### Expected Results
✅ Changes saved to database
✅ Employee list updated
✅ Employee can see changes in profile

---

## Test Scenario 13: Logout

### Objective
User successfully logs out.

### Steps
1. Click profile icon (top-right)
2. Click "Logout"

### Expected Results
✅ Session destroyed
✅ Redirected to login page
✅ Cannot access protected pages
✅ Must login again

---

## Test Scenario 14: Session Timeout

### Objective
Verify protected pages redirect to login.

### Steps
1. Don't logout properly
2. Manually edit URL to admin page
3. Try to access admin/dashboard.php directly

### Expected Results
✅ Redirected to login page
✅ Session check catches missing session
✅ Cannot access protected page

---

## Test Scenario 15: Invalid Login

### Objective
Verify invalid credentials are rejected.

### Steps
1. Open login page
2. Enter:
   - Email: admin@demo.com
   - Password: wrongpassword
3. Click Login

### Expected Results
✅ Login fails
✅ Error message displayed
✅ Redirected back to login
✅ No session created

---

## Test Scenario 16: Admin Dashboard Analytics

### Objective
Verify dashboard shows correct statistics.

### Steps
1. Login as admin
2. View dashboard

### Expected Results
✅ Total Employees count correct
✅ HR Officers count correct
✅ Pending Leaves count correct
✅ Today Present count correct
✅ Recent employees list shows
✅ Recent leave applications show

---

## Test Scenario 17: Employee Profile View

### Objective
Employee views their profile.

### Steps
1. Login as employee
2. Go to "Profile"
3. View information

### Expected Results
✅ Name displays correctly
✅ Email shows
✅ Role shows
✅ Company name shows
✅ Employee details show (if filled)
✅ Account status shows

---

## Test Scenario 18: Multiple Users

### Objective
Verify multiple users can login independently.

### Steps
1. Register 2 different companies
2. Add employees to each
3. Login as different users
4. Verify data isolation

### Expected Results
✅ Each user sees own data only
✅ No data leakage between users
✅ Company data separate
✅ Sessions independent

---

## Test Scenario 19: Database Persistence

### Objective
Verify data persists after logout/login.

### Steps
1. Add employee as admin
2. Login as that employee
3. Check-in
4. Logout
5. Login again
6. View attendance

### Expected Results
✅ Check-in data still there
✅ Attendance record persists
✅ No data loss
✅ Consistent across sessions

---

## Test Scenario 20: Responsive Design

### Objective
Verify application works on different screen sizes.

### Steps
1. Open on desktop (1920px)
2. Open on tablet (768px - in Dev Tools)
3. Open on mobile (375px - in Dev Tools)
4. Test navigation and forms

### Expected Results
✅ Desktop: Full layout
✅ Tablet: Two-column layout
✅ Mobile: Single-column layout
✅ All buttons accessible
✅ Forms readable
✅ Navigation menu adapts

---

## Summary Testing Checklist

- [ ] Admin can register company
- [ ] Admin can login
- [ ] Admin can add employees
- [ ] Employee can login
- [ ] Employee can check-in
- [ ] Employee can check-out
- [ ] Employee can apply leave
- [ ] Admin can approve leave
- [ ] Admin can view attendance
- [ ] Employee can view salary
- [ ] Profile shows correct info
- [ ] Logout works
- [ ] Invalid login rejected
- [ ] Dashboard analytics correct
- [ ] Data persists
- [ ] Responsive on all devices

---

## 🎯 Performance Notes

**Expected Response Times:**
- Login: < 1 second
- Dashboard Load: < 2 seconds
- Employee List: < 2 seconds
- Data Operations: < 1 second

**Database Operations:**
- ✅ Queries optimized
- ✅ Foreign keys set
- ✅ Indexes on key fields
- ✅ Auto-increment working

---

## 🐛 Issues to Check

If any test fails, verify:
1. Database imported correctly
2. MySQL is running
3. Apache is running
4. No PHP errors in console
5. Check XAMPP logs
6. Verify file paths are correct
7. Check session handling

---

**All tests should pass for full functionality!**
