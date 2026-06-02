# 🎓 CampusConnect - Student Management System

CampusConnect is a modern Student Management System built using PHP, MySQL, HTML, CSS, and XAMPP.

This project provides a secure and responsive platform where users can register, login, manage profiles, and administrators can manage users through a modern dashboard interface.

---

# 🚀 Features

## 🔐 Authentication System
- User Registration
- User Login
- Secure Logout
- Session Management
- Password Hashing
- Authentication Protection

## 👤 Profile Management
- View User Profile
- Upload Profile Picture
- Profile Information Display

## 👥 User Management
- View All Users
- Add Users
- Edit Users
- Delete Users

## 🎨 Modern UI/UX
- Glassmorphism Design
- Responsive Layout
- Sidebar Navigation
- Modern Navbar
- Dashboard Cards
- Mobile Responsive Design
- Smooth Animations

## 🛡️ Security Features
- Prepared Statements
- Secure Sessions
- Password Encryption
- Session Timeout Protection

---

# 🛠️ Technologies Used

| Technology | Purpose |
|------------|---------|
| HTML5 | Structure |
| CSS3 | Styling |
| PHP | Backend Logic |
| MySQL | Database |
| XAMPP | Local Server |
| Font Awesome | Icons |

---

# 📂 Project Structure

```bash
campusconnect/
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
│
├── config/
│   ├── db.php
│   └── session.php
│
├── database/
│   └── database.sql
│
├── auth/
│   ├── login_process.php
│   ├── register_process.php
│   └── auth_check.php
│
├── users/
│   ├── add_user.php
│   ├── edit_user.php
│   ├── delete_user.php
│   └── view_users.php
│
├── profile/
│   ├── upload_photo.php
│   └── profile_view.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── sidebar.php
│
├── uploads/
│   └── profile_pictures/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   └── images/
│
└── README.md