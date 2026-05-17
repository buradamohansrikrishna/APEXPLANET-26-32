CREATE DATABASE quickbite;
USE quickbite;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    password VARCHAR(255)
);

CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_name VARCHAR(100),
    image VARCHAR(255),
    location VARCHAR(100)
);

CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT,
    food_name VARCHAR(100),
    price DECIMAL(10,2),
    category VARCHAR(50),
    image VARCHAR(255),
    description TEXT,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    food_id INT,
    quantity INT,
    total_price DECIMAL(10,2),
    order_status VARCHAR(50) DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (food_id) REFERENCES foods(id)
);