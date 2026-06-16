# QuickBite 🍔

### Modern Food Ordering & Restaurant Management System (v2.0)

QuickBite is a premium full-stack food ordering web application developed using **PHP, MySQL, HTML5, CSS3, and JavaScript**. 
The platform allows users to browse restaurants, explore food menus, add items to cart, place orders, write detailed customer reviews, and manage food ordering seamlessly. It also features a fully-fledged admin dashboard for store operations.

---

# 🚀 Features

## 👤 User Features

* **User Registration & Login**: Secure password hashing and authentication system.
* **Browse Restaurants**: Dynamic listing with categories, ratings, opening times, and status (Open/Closed).
* **Explore Restaurant Menus**: Search filters for veg/non-veg options, categories, and live search.
* **Dynamic Cart System**: Add to cart with quantity selection, automatic totals, and saving items for later.
* **Customer Reviews**: Write reviews with titles, comments, and star ratings (1-5★). Includes verified purchase badges.
* **Rating Breakdown**: Interactive rating breakdown bars showing exact percentages and average ratings for dishes.
* **Favorites**: Toggle favorite food items with a single click.
* **Notifications**: Integrated system notifications for order statuses and promotions.
* **Checkout System**: Multi-payment method selector (COD, UPI, Card, Netbanking), address book, and coupon applicability.

---

## 🛠️ Admin Features

* **Admin Dashboard**: Visual stats of total users, orders, foods, restaurants, and total revenue.
* **Manage Restaurants**: Add, update, and manage restaurant details.
* **Food Management**: Create, edit, search, and filter food items. Toggle item availability in real-time.
* **Coupons System**: Manage discount coupons (percentage/flat off, expiry dates, min-orders).
* **Orders Panel**: Real-time status update tracking (Pending, Accepted, Preparing, Ready, Out for Delivery, Delivered, Cancelled).
* **User Management**: View and ban/activate customer accounts.

---

# 📂 Project Structure

```text
QuickBite/
│
├── admin/                  # Admin-specific pages and templates
│   ├── ajax/               # Admin AJAX endpoints (user status, order status)
│   ├── dashboard.php       # Admin analytics and metrics
│   ├── foods.php           # Food management panel
│   ├── add-food.php        # Create a new menu item
│   ├── edit-food.php       # Edit menu item details
│   ├── coupons.php         # Manage discount coupons
│   ├── orders.php          # Manage order statuses
│   ├── restaurants.php     # Manage restaurant lists
│   └── users.php           # User tracking and security control
│
├── assets/                 # Frontend assets
│   ├── css/                # Custom CSS styling (style.css, admin.css, animations.css)
│   ├── js/                 # Vanilla JS scripting (main.js, cart.js)
│   └── images/             # Static graphics and file uploads
│
├── auth/                   # Authentication module (Login, Register, Logout)
│
├── config/                 # Core configuration
│   └── db.php              # MySQLi and PDO database connections
│
├── database/               # SQL Database Migrations
│   ├── quickbite_v2.sql    # Base v2.0 schema structure
│   └── add_reviews_payment.sql # Review & Payment upgrades
│
├── includes/               # Reusable page components and helpers
│   ├── navbar.php
│   ├── footer.php
│   ├── security.php        # CSRF, XSS, and rate limiting helpers
│   └── functions.php       # Global helper functions
│
├── user/                   # Customer dashboard and ordering pages
│   ├── ajax/               # Customer AJAX endpoints (reviews, favorites, cart)
│   ├── dashboard.php
│   ├── menu.php
│   ├── cart.php
│   ├── checkout.php
│   └── orders.php
│
├── about.php
├── contact.php
├── index.php
└── README.md
```

---

# ⚙️ Installation Steps

## 1️⃣ Install XAMPP
Download and install XAMPP with PHP 8.0+ and MySQL.

## 2️⃣ Move Project Folder
Clone/copy the repository into your XAMPP web server directory:
```text
C:\xampp\htdocs\APEXPLANET-26-32\TASK-4
```

## 3️⃣ Start XAMPP Server
Open XAMPP Control Panel and start **Apache** and **MySQL**.

## 4️⃣ Create Database
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Create a database named: `quickbite`.

## 5️⃣ Import SQL Schemas
1. Import `QUICKBITE/database/quickbite_v2.sql` to set up the base tables.
2. Import `QUICKBITE/database/add_reviews_payment.sql` to set up the updated reviews, order items, and payment structures.

---

# ▶️ Run Project

Open your browser and navigate to:
```text
http://localhost/TASK-4/QUICKBITE
```

---

# 🌟 Key Highlights

* **Premium Glassmorphism UI**: Beautiful dark/light mode accents, gradients, and layouts.
* **Secure Database Access**: Dual-mode connections (MySQLi & PDO) with secure prepared statements.
* **Security Built-in**: Protection against CSRF attacks, XSS sanitization, and session timeouts.
* **Interactive Frontend**: Fast asynchronous rating and checkout system without page reloads.

---

# 👨‍💻 Developed By

**Mohan Sri Krishna**  
Full Stack Web Development Project  
ApexPlanet Internship – Task 4  
