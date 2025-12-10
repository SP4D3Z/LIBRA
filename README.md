# 📚 Smart Library Management System

A comprehensive, modern library management system built with PHP MVC architecture. 
This system streamlines book borrowing, user management, reservations, penalties, and clearance processes for educational institutions.

## 🌟 Features

### 👥 User Management
- **Four User Roles**: Student, Teacher, Staff, Librarian
- **Role-based Access Control**: Different permissions for each role
- **User Registration & Authentication**: Secure login with password hashing
- **Soft Delete Functionality**: Deactivate users without permanent deletion

### 📖 Book Management
- Complete book inventory system
- ISBN, author, publisher, edition tracking
- Category-based organization
- Available copies tracking
- Archive/Unarchive functionality

### 🔄 Borrowing System
- **Smart Borrowing Rules**:
  - Students: Max 3 books per semester
  - Teachers: 180-day borrowing period
  - Staff/Librarians: Standard 30-day period
- Automatic due date calculation
- Return management with staff tracking
- Overdue book detection

### 📅 Reservation System
- Book reservation with 7-day expiry
- Reservation status tracking (pending/approved/cancelled/expired)
- User-specific reservation views

### ⚖️ Penalty System
- Automatic overdue penalty calculation (₱10/day)
- Manual penalty marking (damage/lost books)
- Payment tracking with staff verification
- Unpaid penalty detection

### ✅ Clearance System
- Automated clearance eligibility checking
- Checks for:
  - Active borrowings
  - Unpaid penalties
  - Overdue books
- Staff/librarian approval process

### 📊 Dashboard & Reports
- User-specific statistics
- Active borrowings count
- Unpaid penalties summary
- Overdue books tracking
- Semester-based reporting for students

## 🏗️ System Architecture

### MVC Structure
```
app/
├── Controllers/     # Business logic controllers
├── Models/         # Database models and business rules
└── Views/          # Presentation templates
public/             # Web-accessible entry points
```

### Database Schema
- **users**: User accounts and authentication
- **books**: Book inventory and details
- **categories**: Book categorization
- **borrowing_transactions**: Borrowing records
- **reservations**: Book reservations
- **penalties**: Fine and penalty tracking

## 🚀 Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional)

### Step 1: Clone or Download
```bash
git clone <repository-url>
cd LIBRA
```

### Step 2: Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE smart_library CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

2. Import the SQL schema (see `database/schema.sql`)

3. Update database credentials in `/app/Models/Database.php`:
```php
$host = '127.0.0.1';
$db   = 'smart_library';
$user = 'root';
$pass = '';
```

### Step 3: Web Server Configuration
1. Point your web server to the `/public` directory
2. Ensure mod_rewrite is enabled (for clean URLs)
3. Set proper file permissions:
```bash
chmod -R 755 app/
chmod -R 755 public/
```

### Step 4: Initial Setup
1. Access the application: `http://your-domain.com/login.php`
2. Register the first librarian/admin account
3. Add initial book categories and sample data

## 👨‍💻 User Roles & Permissions

### Librarian
- Full system access
- Book management
- System configuration

### Staff
- Reservation management
- Penalty processing
- Clearance checking
- Basic user assistance

### Teacher
- Extended borrowing period (180 days)
- Unlimited book borrowing
- View personal borrowings/reservations
- Pay penalties

### Student
- Limited borrowing (3 books/semester)
- Standard borrowing period (30 days)
- View personal borrowings/reservations
- Pay penalties
- Semester-based quota system

## 🔧 Configuration

### Main Configuration File
`/public/config.php` - Central configuration for:
- Database connections
- Session management
- Autoloading
- Path definitions

### Environment Variables
Create `.env` file for sensitive data:
```env
DB_HOST=localhost
DB_NAME=smart_library
DB_USER=root
DB_PASS=
APP_ENV=production
```

## 📱 Usage Guide

### For Librarians/Staff
1. **Add Books**: Navigate to Books → Add Book
2. **Manage Users**: Users → Add/Delete users
3. **Process Borrowings**: Borrow → Scan/enter book and user IDs
4. **Handle Returns**: Click "Return" on active borrowings
5. **Check Clearance**: Clearance → Select user → Approve/Deny

### For Students/Teachers
1. **Browse Books**: View available books
2. **Borrow Books**: Select book → Confirm borrowing
3. **View History**: Check borrowing history and due dates
4. **Pay Penalties**: View and pay outstanding fines
5. **Request Clearance**: Apply for end-of-semester clearance

## ⚙️ System Rules & Policies

### Borrowing Rules
- **Students**: 3 books max per semester, 30-day period
- **Teachers**: Unlimited books, 180-day period
- **Staff/Librarians**: Standard 30-day period

### Penalty Calculation
- Overdue books: ₱10.00 per day
- Lost books: Book price + processing fee
- Damaged books: Assessment-based penalty

### Clearance Requirements
- No active borrowings
- No unpaid penalties
- No overdue books
- Staff/librarian approval

## 🛡️ Security Features

- Password hashing with bcrypt
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars output encoding)
- Session-based authentication
- Role-based access control
- Input validation and sanitization
- CSRF protection (recommended to implement)

## 🔍 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check Database.php credentials
   - Verify MySQL service is running
   - Ensure database exists

2. **Session Issues**
   - Check php.ini session settings
   - Verify write permissions on session directory
   - Clear browser cookies

3. **Permission Errors**
   - Verify user role assignments
   - Check session variables
   - Review require_login() function

4. **File Not Found Errors**
   - Check include paths in config.php
   - Verify file permissions
   - Review .htaccess configuration

### Debug Mode
Enable debug mode in `config.php`:
```php
define('DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Future Enhancements

### Planned Features
- [ ] Email notifications for due dates
- [ ] Barcode/RFID integration
- [ ] Advanced reporting and analytics
- [ ] Mobile-responsive design
- [ ] REST API for mobile apps
- [ ] Bulk import/export features
- [ ] Multi-language support
- [ ] Audit logging system

### Technical Improvements
- [ ] Implement Composer for dependencies
- [ ] Add unit tests
- [ ] Implement caching layer
- [ ] Add API documentation
- [ ] Docker containerization

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit changes with descriptive messages
4. Push to the branch
5. Create a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Update documentation for new features
- Add tests for new functionality


## 🙏 Acknowledgments

- Bootstrap 5 for frontend components
- Font Awesome for icons
- PHP community for best practices
- All contributors and testers

---

**Version**: 1.0.0  
**Last Updated**: December 2025
**Developed By**: Allen Gabriel R. Briones

---
*"Empowering libraries with smart digital solutions"*
