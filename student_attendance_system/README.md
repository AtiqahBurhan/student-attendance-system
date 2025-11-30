# Student Attendance System (SAS)

## Description
SAS is a modern PHP web application for managing student attendance.  
It allows admins and users to record daily attendance, view reports, and track trends.

## Test Login
- Username: admin
- Email   : admin@gmail.com
- Password: password123

## How to run locally
1. Place the `student-attendance` folder inside your web server root (e.g., XAMPP htdocs).  
2. Start Apache & MySQL (XAMPP/WAMP/Laragon).  
3. Import `database.sql` into your MySQL database.  
4. Configure your database credentials in `db.php`.  
5. Visit `http://localhost/student-attendance/index.php` in your browser.

## Files included
- `index.php`, `register.php`, `logout.php`, `dashboard.php`  
- `students.php`, `attendance.php`, `reports.php`  
- `db.php`, `navbar.php`  
- `assets/css/style.css`, `assets/js/script.js`
- `charts/` (if any chart JS files)

## Features
- User login & registration
- Add/edit/delete students
- Take daily attendance (Present, Absent, Late)
- Attendance dashboard with charts (Chart.js)
- Filter attendance by date
- Responsive design using Bootstrap 5
- Color-coded status badges

