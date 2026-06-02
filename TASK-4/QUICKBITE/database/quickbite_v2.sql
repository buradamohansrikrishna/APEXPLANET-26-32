-- ================================================
-- QUICKBITE 2.0 MIGRATION (Safe ALTER-based)
-- Run this to upgrade existing database
-- ================================================

USE quickbite;

-- ── admins table ─────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT 'Administrator',
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    avatar VARCHAR(255) DEFAULT NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO admins (name, email, password, role) VALUES
('Super Admin', 'admin@quickbite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- ── restaurants: add missing columns ─────────
ALTER TABLE restaurants
    ADD COLUMN IF NOT EXISTS address VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS rating DECIMAL(3,1) DEFAULT 4.0,
    ADD COLUMN IF NOT EXISTS category VARCHAR(100) DEFAULT 'Multi-cuisine',
    ADD COLUMN IF NOT EXISTS opening_time TIME DEFAULT '09:00:00',
    ADD COLUMN IF NOT EXISTS closing_time TIME DEFAULT '23:00:00',
    ADD COLUMN IF NOT EXISTS delivery_time VARCHAR(50) DEFAULT '30-45 mins',
    ADD COLUMN IF NOT EXISTS min_order DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') DEFAULT 'active',
    ADD COLUMN IF NOT EXISTS cover_image VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

UPDATE restaurants SET address = location WHERE address IS NULL AND location IS NOT NULL;
UPDATE restaurants SET status = 'active' WHERE status IS NULL;

-- ── users: add missing columns ────────────────
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS reward_points INT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user',
    ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','banned') DEFAULT 'active',
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- ── foods: add missing columns ────────────────
ALTER TABLE foods
    ADD COLUMN IF NOT EXISTS ingredients TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS calories INT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS prep_time VARCHAR(50) DEFAULT '15-20 mins',
    ADD COLUMN IF NOT EXISTS is_veg TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS spice_level ENUM('mild','medium','hot','extra_hot') DEFAULT 'medium',
    ADD COLUMN IF NOT EXISTS availability ENUM('available','unavailable') DEFAULT 'available',
    ADD COLUMN IF NOT EXISTS total_orders INT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS rating DECIMAL(3,1) DEFAULT 4.0,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- ── orders: add missing columns ──────────────
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS order_number VARCHAR(20) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS unit_price DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS delivery_fee DECIMAL(10,2) DEFAULT 40.00,
    ADD COLUMN IF NOT EXISTS tax DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(50) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS discount DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS delivery_address TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'COD',
    ADD COLUMN IF NOT EXISTS estimated_delivery TIMESTAMP NULL;

-- Update order_status to ENUM safely
ALTER TABLE orders MODIFY COLUMN order_status ENUM('Pending','Accepted','Preparing','Ready','Out For Delivery','Delivered','Cancelled') DEFAULT 'Pending';

-- Generate order numbers for existing orders
UPDATE orders SET order_number = CONCAT('QB', DATE_FORMAT(order_date,'%y%m%d'), LPAD(id,4,'0')) WHERE order_number IS NULL;

-- ── cart table (new) ─────────────────────────
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    saved_for_later TINYINT(1) DEFAULT 0,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, food_id)
);

-- ── addresses table (new) ────────────────────
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    label VARCHAR(50) DEFAULT 'Home',
    full_address TEXT NOT NULL,
    city VARCHAR(100) DEFAULT NULL,
    pincode VARCHAR(10) DEFAULT NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── favorites table (new) ───────────────────
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, food_id)
);

-- ── reviews table (new) ─────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_id INT NOT NULL,
    rating INT NOT NULL DEFAULT 5,
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- ── coupons table (new) ─────────────────────
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    discount_type ENUM('percent','flat') DEFAULT 'percent',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_value DECIMAL(10,2) DEFAULT 0.00,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    max_uses INT DEFAULT 100,
    used_count INT DEFAULT 0,
    expiry_date DATE NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, min_order_value, max_uses, expiry_date) VALUES
('WELCOME20', 'Get 20% off on your first order!', 'percent', 20.00, 200.00, 1000, '2027-12-31'),
('FLAT50', 'Flat Rs.50 off on orders above Rs.300', 'flat', 50.00, 300.00, 500, '2027-06-30'),
('QUICKBITE10', '10% off on all orders', 'percent', 10.00, 100.00, 2000, '2027-12-31');

-- ── notifications table (new) ───────────────
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order','promo','system','warning') DEFAULT 'system',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── payments table (new) ────────────────────
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50) DEFAULT 'COD',
    status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── Sample data if tables empty ──────────────
INSERT IGNORE INTO restaurants (id, restaurant_name, description, address, rating, category, opening_time, closing_time, delivery_time, status) VALUES
(1, 'Burger King', 'Home of the Whopper. Flame-grilled burgers with premium toppings.', 'Banjara Hills, Hyderabad', 4.8, 'Fast Food', '10:00:00', '23:00:00', '20-30 mins', 'active'),
(2, 'Pizza Hub', 'Authentic Italian-style pizzas with fresh handmade dough.', 'MG Road, Vijayawada', 4.7, 'Italian', '11:00:00', '23:30:00', '25-35 mins', 'active'),
(3, 'Biryani House', 'Hyderabadi Dum Biryani — the real deal since 1965.', 'Abids, Hyderabad', 4.9, 'Indian', '12:00:00', '22:00:00', '30-40 mins', 'active');

-- Sample foods
INSERT IGNORE INTO foods (id, restaurant_id, food_name, price, category, description, is_veg, spice_level, availability) VALUES
(1, 1, 'Classic Whopper', 299.00, 'Burgers', 'Flame-grilled beef patty with fresh lettuce, tomatoes, and mayo', 0, 'medium', 'available'),
(2, 1, 'Veggie Burger', 199.00, 'Burgers', 'Crispy veggie patty with cheese and special sauce', 1, 'mild', 'available'),
(3, 1, 'Crispy Fries', 99.00, 'Sides', 'Golden crispy fries seasoned with our secret spice blend', 1, 'mild', 'available'),
(4, 2, 'Margherita Pizza', 349.00, 'Pizza', 'Classic tomato sauce, fresh mozzarella, and basil leaves', 1, 'mild', 'available'),
(5, 2, 'Pepperoni Pizza', 449.00, 'Pizza', 'Loaded with premium pepperoni and mozzarella cheese', 0, 'medium', 'available'),
(6, 3, 'Chicken Biryani', 279.00, 'Biryani', 'Aromatic basmati rice cooked with tender chicken and spices', 0, 'hot', 'available'),
(7, 3, 'Veg Biryani', 229.00, 'Biryani', 'Fragrant basmati rice with fresh vegetables and saffron', 1, 'medium', 'available'),
(8, 3, 'Mutton Biryani', 379.00, 'Biryani', 'Slow-cooked mutton with premium spices and basmati rice', 0, 'extra_hot', 'available');

SELECT 'Migration complete! QuickBite v2 database ready.' as Status;
