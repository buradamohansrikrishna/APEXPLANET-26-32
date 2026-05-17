# QuickBite 🍔

### Modern Food Ordering & Restaurant Management System

QuickBite is a modern full-stack food ordering web application developed using **PHP, MySQL, HTML, CSS, and JavaScript**.
The platform allows users to browse restaurants, explore food menus, add items to cart, place orders, and manage food ordering seamlessly.

---

# 🚀 Features

## 👤 User Features

* User Registration & Login
* Secure Authentication System
* Browse Restaurants
* Explore Restaurant Menus
* Add Food to Cart
* Checkout System
* Order Management
* Responsive Dashboard
* Modern Responsive UI

---

## 🛠️ Admin Features

* Admin Dashboard
* Manage Restaurants
* Add / Delete Foods
* View Orders
* Track Users
* Food Management System

---

# 💻 Technologies Used

* HTML5
* CSS3
* JavaScript
* PHP
* MySQL
* Responsive Web Design

---

# 📂 Project Structure

```text
QuickBite/
│
├── admin/
│   ├── dashboard.php
│   ├── foods.php
│   ├── add-food.php
│   ├── delete-food.php
│   └── orders.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   │
│   ├── js/
│   │   ├── main.js
│   │   └── cart.js
│   │
│   └── images/
│       ├── foods/
│       └── restaurants/
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   └── db.php
│
├── includes/
│   ├── navbar.php
│   ├── footer.php
│   └── session.php
│
├── user/
│   ├── dashboard.php
│   ├── restaurants.php
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

Download and install:

* Apache
* MySQL

---

## 2️⃣ Move Project Folder

Move the project folder into:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\QuickBite
```

---

## 3️⃣ Start XAMPP

Open XAMPP Control Panel and start:

* Apache ✅
* MySQL ✅

---

## 4️⃣ Create Database

Open:

```text
http://localhost/phpmyadmin
```

Create database:

```sql
quickbite
```

---

## 5️⃣ Import SQL Tables

Open SQL tab and run:

```sql
CREATE TABLE users(

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100),

    email VARCHAR(100) UNIQUE,

    password VARCHAR(255)
);

CREATE TABLE restaurants(

    id INT AUTO_INCREMENT PRIMARY KEY,

    restaurant_name VARCHAR(100),

    location VARCHAR(255),

    image VARCHAR(255),

    description TEXT
);

CREATE TABLE foods(

    id INT AUTO_INCREMENT PRIMARY KEY,

    restaurant_id INT,

    food_name VARCHAR(100),

    price DECIMAL(10,2),

    category VARCHAR(100),

    image VARCHAR(255),

    description TEXT
);

CREATE TABLE orders(

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT,

    food_id INT,

    quantity INT,

    total_price DECIMAL(10,2),

    order_status VARCHAR(50),

    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

# ▶️ Run Project

Open browser:

```text
http://localhost/QuickBite
```

---

# 📸 Main Pages

* Home Page
* Login / Register
* Restaurants Page
* Menu Page
* Cart System
* Checkout System
* Orders Page
* Admin Dashboard

---

# 🌟 Key Highlights

* Modern Glassmorphism UI
* Responsive Design
* Smooth Animations
* Dynamic Cart System
* Secure Password Hashing
* Session Authentication
* Premium Frontend Design

---

# 🎯 Project Objective

QuickBite was developed to simplify digital food ordering and restaurant management using modern web technologies while providing users with a seamless and responsive ordering experience.

---

# 📌 Future Enhancements

* Online Payment Integration
* Live Order Tracking
* AI Food Recommendations
* Real-time Notifications
* Delivery Partner Module
* Admin Analytics Dashboard

---

# 👨‍💻 Developed By

**Mohan Sri Krishna**
Full Stack Web Development Project
ApexPlanet Internship – Task 4

---

# 📄 License

This project is developed for educational and internship purposes.
