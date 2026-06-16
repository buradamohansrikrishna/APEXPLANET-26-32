-- ================================================
-- QUICKBITE — Reviews & Payment Migration
-- Run this on top of quickbite_v2.sql
-- ================================================

USE quickbite;

-- ── order_items table (fixes flat orders schema) ─
CREATE TABLE IF NOT EXISTS order_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT NOT NULL,
    food_id     INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(10,2) NOT NULL,
    subtotal    DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id)  REFERENCES foods(id)  ON DELETE CASCADE
);

-- ── reviews table ────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    food_id    INT NOT NULL,
    order_id   INT DEFAULT NULL,
    rating     TINYINT NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
    title      VARCHAR(120) DEFAULT NULL,
    comment    TEXT DEFAULT NULL,
    is_verified TINYINT(1) DEFAULT 0,   -- 1 = user actually ordered this food
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id)  ON DELETE CASCADE,
    UNIQUE KEY unique_user_food_review (user_id, food_id)
);

-- ── payments table ───────────────────────────────
CREATE TABLE IF NOT EXISTS payments (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    order_id       INT NOT NULL,
    user_id        INT NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    method         ENUM('COD','UPI','Card','Wallet','Netbanking') DEFAULT 'COD',
    status         ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) DEFAULT NULL,
    gateway_ref    VARCHAR(100) DEFAULT NULL,
    paid_at        TIMESTAMP NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE
);

-- ── orders: add missing columns safely ──────────
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS order_number     VARCHAR(20) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS delivery_fee     DECIMAL(10,2) DEFAULT 40.00,
    ADD COLUMN IF NOT EXISTS tax              DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS coupon_code      VARCHAR(50) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS discount         DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS delivery_address TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS payment_method   VARCHAR(50) DEFAULT 'COD',
    ADD COLUMN IF NOT EXISTS estimated_delivery TIMESTAMP NULL,
    ADD COLUMN IF NOT EXISTS created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- ── foods: ensure rating column exists ──────────
ALTER TABLE foods
    ADD COLUMN IF NOT EXISTS rating      DECIMAL(3,1) DEFAULT 4.0,
    ADD COLUMN IF NOT EXISTS total_orders INT DEFAULT 0;

-- Backfill order numbers for existing orders
UPDATE orders
SET order_number = CONCAT('QB', DATE_FORMAT(COALESCE(order_date, NOW()), '%y%m%d'), LPAD(id, 4, '0'))
WHERE order_number IS NULL;

SELECT 'Reviews & Payment migration complete!' AS Status;
