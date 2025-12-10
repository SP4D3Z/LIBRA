# **Smart Library Management System - User Manual**

## **Table of Contents**
1. [Introduction](#introduction)
2. [System Overview](#system-overview)
3. [User Roles and Permissions](#user-roles-and-permissions)
4. [Getting Started](#getting-started)
5. [Core Features](#core-features)
6. [Clearance System](#clearance-system)
7. [Troubleshooting](#troubleshooting)
8. [Security Guidelines](#security-guidelines)

---

## **1. Introduction**

Welcome to the **Smart Library Management System**, a comprehensive web-based platform designed for educational institutions to manage library operations efficiently. This system handles book borrowing, reservations, penalties, and semester clearance processes.

### **Key Benefits:**
- Streamlined book borrowing and returning
- Automated penalty calculation
- Semester clearance tracking
- Role-based access control
- Real-time inventory management

## **2. System Overview**

### **System Architecture:**
- **Frontend**: HTML, CSS (Bootstrap 5), JavaScript
- **Backend**: PHP with MVC-like structure
- **Database**: MySQL/MariaDB
- **Authentication**: Session-based with password hashing

### **Browser Requirements:**
- Google Chrome 90+
- Firefox 88+
- Safari 14+
- Microsoft Edge 90+
- JavaScript must be enabled

## **3. User Roles and Permissions**

### **3.1 Student**
**Permissions:**
- View personal borrowing history
- Reserve available books
- Check clearance status
- View personal penalties
- Borrow books within semester limits

**Restrictions:**
- Cannot manage other users
- Cannot add/remove books
- Maximum 3 books per semester (configurable)

### **3.2 Teacher**
**Permissions:**
- All student permissions
- Extended borrowing privileges
- View teaching-related resources

**Special Notes:**
- Must return all books for semester clearance
- No semester borrowing limit

### **3.3 Staff**
**Permissions:**
- All teacher permissions
- View all users
- Deactivate users (except own account)
- Process basic library operations

### **3.4 Librarian**
**Permissions:**
- All staff permissions
- Full book management (add/edit/remove)
- Generate reports
- Manage all reservations
- Process clearance approvals
- Manage all penalties

## **4. Getting Started**

### **4.1 Registration Process**
1. Navigate to `register.php`
2. Fill in required information:
   - Username (must be unique)
   - First and Last Name
   - Phone Number
   - Address
   - Password (minimum 8 characters)
   - User Type

3. **Additional fields based on user type:**
   - **Student**: Program, Year Level
   - **Teacher/Staff**: Department, Position
   - **Librarian**: Department (default: Library)

4. Click "Register" to create account

### **4.2 Login Process**
1. Navigate to `login.php`
2. Enter your username and password
3. Click "Login"
4. You will be redirected to your dashboard

### **4.3 Password Requirements:**
- Minimum 8 characters
- Include uppercase and lowercase letters
- Include numbers
- Include special characters (recommended)

### **4.4 Account Recovery:**
Contact your librarian or system administrator if you forget your password.

## **5. Core Features**

### **5.1 Dashboard (`index.php`)**
**Access**: After successful login

**Features:**
- Welcome message with your name
- Role display
- Quick access navigation based on role

### **5.2 Book Management**

#### **For Students/Teachers:**
1. **Browse Books**: Navigate to books section
2. **Check Availability**: View available copies
3. **Borrow Books**: Select book and confirm borrowing
4. **Return Books**: Return before due date

#### **For Librarians/Staff:**
1. **Add New Book**: 
   - Enter ISBN, Title, Author
   - Set total copies and price
   - Assign category and location

2. **Edit Book Details**:
   - Update availability
   - Modify book information
   - Change condition status

3. **Archive Books**:
   - Mark books as archived when no longer available
   - Archived books are not borrowable

### **5.3 Borrowing Process**

#### **Borrowing Rules:**
- **Students**: Maximum 3 books per semester
- **Teachers**: No limit, but must return for clearance
- **Borrowing Period**: 30 days (configurable)
- **Renewals**: Contact library staff

#### **Steps to Borrow:**
1. Search for desired book
2. Check availability status
3. Click "Borrow" button
4. Confirm borrowing details
5. Receive due date notification

#### **Return Process:**
1. Go to "My Borrowings" section
2. Select book to return
3. Click "Return" button
4. Get return confirmation

### **5.4 Reservations System**

#### **Making a Reservation:**
1. Navigate to `reservations.php`
2. Select user and book
3. Click "Reserve"
4. Reservation lasts 7 days

#### **Reservation Statuses:**
- **Pending**: Waiting for availability
- **Ready**: Book available for pickup
- **Fulfilled**: Book borrowed
- **Expired**: Not picked up within 7 days
- **Cancelled**: User cancelled reservation

### **5.5 Penalties Management**

#### **Penalty Types:**
1. **Overdue**: ₱10 per day after due date
2. **Lost Book**: Replacement cost + processing fee
3. **Damaged Book**: Repair cost assessment
4. **Other**: Miscellaneous fines

#### **Viewing Penalties:**
- Navigate to `penalties.php`
- View all penalties (librarian/staff)
- View personal penalties (students/teachers)

#### **Paying Penalties:**
1. Go to penalties section
2. Click "Pay Penalties"
3. Confirm payment
4. Get receipt from library staff

### **5.6 User Management**

#### **For Librarians/Staff Only:**
1. **View All Users**: Complete user list
2. **Add New User**: Create accounts for others
3. **Deactivate User**: Mark as inactive
4. **Cannot delete**: System uses soft deletion

#### **Adding Users:**
1. Navigate to `users.php`
2. Fill registration form
3. Set appropriate user type
4. Click "Create"

#### **Deactivation Rules:**
- Cannot deactivate own account
- Deactivated users cannot login
- Records are preserved for reporting

## **6. Clearance System**

### **6.1 What is Clearance?**
Clearance is a semester-end process where students and teachers must:
- Return all borrowed books
- Pay all outstanding penalties
- Get approval for next semester enrollment

### **6.2 Checking Clearance Status**
1. Navigate to `clearance_status.php`
2. View current status:
   - **CLEARED**: Green checkmark, no issues
   - **NOT CLEARED**: Red X, outstanding issues listed

### **6.3 Clearance Requirements**

#### **For Students:**
- No active book borrowings
- No overdue books
- All penalties paid
- Within semester borrowing limit (3 books)

#### **For Teachers:**
- No active book borrowings (even if not overdue)
- All penalties paid

### **6.4 Clearance Issues Resolution**

#### **Common Issues & Solutions:**

1. **Active Borrowings**:
   - Return all borrowed books at library counter
   - Use "Return" button in clearance status page

2. **Overdue Books**:
   - Return immediately
   - Pay overdue penalties
   - Contact library for extensions

3. **Unpaid Penalties**:
   - Go to penalties section
   - Pay outstanding amounts
   - Get payment confirmation

4. **Reached Borrowing Limit**:
   - Return some books
   - Wait for next semester reset
   - Request special permission (teachers only)

### **6.5 Clearance Process Steps**

1. **Self-Check**: Verify status on clearance page
2. **Issue Resolution**: Address all listed problems
3. **Library Visit**: Get final verification from staff
4. **Clearance Approval**: Staff marks as cleared in system
5. **Next Semester**: Ready for new borrowing

### **6.6 Semester Borrowing Summary**
- View borrowed vs. allowed books
- Progress bar shows usage percentage
- Warnings when approaching limit
- Resets each semester

## **7. Troubleshooting**

### **7.1 Common Issues**

#### **Login Problems:**
1. **Invalid credentials**: Check username/password
2. **Account deactivated**: Contact librarian
3. **Session expired**: Relogin

#### **Borrowing Issues:**
1. **Book not available**: Reserve or check back later
2. **Reached limit**: Return some books first
3. **Clearance block**: Resolve clearance issues

#### **Clearance Issues:**
1. **Status not updating**: Refresh page or contact staff
2. **Discrepancies**: Report to library immediately

### **7.2 Error Messages**

#### **"Access Denied"**:
- Insufficient permissions for current action
- Contact librarian for access request

#### **"Book Not Available"**:
- All copies currently borrowed or reserved
- Try reservation system

#### **"Clearance Required"**:
- Semester clearance pending
- Resolve outstanding issues

### **7.3 System Maintenance**
- Regular backups: Daily at 2:00 AM
- System updates: Scheduled during breaks
- Report issues: Use system log or contact IT

## **8. Security Guidelines**

### **8.1 Account Security**
- Never share login credentials
- Logout after each session
- Use strong passwords
- Report suspicious activity immediately

### **8.2 Data Privacy**
- Personal information is encrypted
- Borrowing history is confidential
- Only librarians see full user data

### **8.3 Safe Practices**
1. **Public Computers**: Always logout completely
2. **Password Changes**: Every 90 days recommended
3. **Phishing Awareness**: System never emails for passwords
4. **Session Timeout**: 30 minutes of inactivity

### **8.4 Reporting Security Issues**
Contact your librarian immediately if you notice:
- Unauthorized access attempts
- Data discrepancies
- System vulnerabilities
- Suspicious user behavior

---

## **Appendices**

### **A. Quick Reference Guide**

| **Action** | **Location** | **Permission Required** |
|------------|--------------|------------------------|
| Login | `login.php` | None |
| Register | `register.php` | None |
| View Books | `books.php` | All users |
| Borrow Books | `borrow.php` | Student/Teacher |
| Check Clearance | `clearance_status.php` | Student/Teacher |
| View Penalties | `penalties.php` | All users |
| Make Reservation | `reservations.php` | All users |
| Manage Users | `users.php` | Staff/Librarian |
| Generate Reports | `reports.php` | Librarian only |


### **B. Glossary**

- **Clearance**: Semester-end approval process
- **Penalty**: Fine for overdue/lost/damaged books
- **Reservation**: Holding a book for future borrowing
- **Transaction**: Single borrowing/returning action
- **Semester**: Academic term (typically 4-5 months)

---

**Last Updated**: December 10, 2025  
**System Version**: 2.1  
**Document Version**: 1.0
