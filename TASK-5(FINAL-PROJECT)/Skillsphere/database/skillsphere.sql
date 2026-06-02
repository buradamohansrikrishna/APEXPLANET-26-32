-- =========================================
-- SKILLSPHERE ADVANCED DATABASE
-- =========================================

CREATE DATABASE IF NOT EXISTS skillsphere
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE skillsphere;

-- =========================================
-- USERS TABLE
-- =========================================

CREATE TABLE users (

    id INT PRIMARY KEY AUTO_INCREMENT,

    full_name VARCHAR(120) NOT NULL,

    username VARCHAR(80) UNIQUE,

    email VARCHAR(150) UNIQUE NOT NULL,

    phone VARCHAR(20),

    password VARCHAR(255) NOT NULL,

    profile_image VARCHAR(255)
    DEFAULT 'default.png',

    bio TEXT,

    role ENUM(
        'student',
        'instructor',
        'admin'
    ) DEFAULT 'student',

    status ENUM(
        'active',
        'blocked',
        'pending'
    ) DEFAULT 'active',

    email_verified TINYINT(1)
    DEFAULT 0,

    remember_token VARCHAR(255),

    last_login TIMESTAMP NULL,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

);

-- =========================================
-- COURSE CATEGORIES
-- =========================================

CREATE TABLE categories (

    id INT PRIMARY KEY AUTO_INCREMENT,

    category_name VARCHAR(100)
    UNIQUE NOT NULL,

    slug VARCHAR(120)
    UNIQUE,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP

);

-- =========================================
-- COURSES TABLE
-- =========================================

CREATE TABLE courses (

    id INT PRIMARY KEY AUTO_INCREMENT,

    category_id INT,

    instructor_id INT,

    title VARCHAR(255) NOT NULL,

    slug VARCHAR(255) UNIQUE,

    short_description VARCHAR(500),

    description LONGTEXT,

    thumbnail VARCHAR(255),

    intro_video VARCHAR(255),

    level ENUM(
        'beginner',
        'intermediate',
        'advanced'
    ) DEFAULT 'beginner',

    language VARCHAR(50)
    DEFAULT 'English',

    duration VARCHAR(50),

    price DECIMAL(10,2)
    DEFAULT 0,

    discount_price DECIMAL(10,2)
    DEFAULT 0,

    requirements TEXT,

    learning_outcomes TEXT,

    status ENUM(
        'draft',
        'published',
        'archived'
    ) DEFAULT 'published',

    total_students INT
    DEFAULT 0,

    average_rating DECIMAL(3,2)
    DEFAULT 0,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY(category_id)
    REFERENCES categories(id)
    ON DELETE SET NULL,

    FOREIGN KEY(instructor_id)
    REFERENCES users(id)
    ON DELETE SET NULL

);

-- =========================================
-- COURSE LESSONS
-- =========================================

CREATE TABLE lessons (

    id INT PRIMARY KEY AUTO_INCREMENT,

    course_id INT NOT NULL,

    title VARCHAR(255) NOT NULL,

    video_url VARCHAR(255),

    lesson_content LONGTEXT,

    lesson_order INT DEFAULT 1,

    duration VARCHAR(50),

    is_preview TINYINT(1)
    DEFAULT 0,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- ENROLLMENTS
-- =========================================

CREATE TABLE enrollments (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    course_id INT NOT NULL,

    payment_status ENUM(
        'pending',
        'paid',
        'failed'
    ) DEFAULT 'pending',

    enrolled_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    completed_at TIMESTAMP NULL,

    UNIQUE(user_id, course_id),

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- LESSON PROGRESS
-- =========================================

CREATE TABLE lesson_progress (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    lesson_id INT NOT NULL,

    completed TINYINT(1)
    DEFAULT 0,

    watched_time INT
    DEFAULT 0,

    completed_at TIMESTAMP NULL,

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(lesson_id)
    REFERENCES lessons(id)
    ON DELETE CASCADE

);

-- =========================================
-- WISHLIST
-- =========================================

CREATE TABLE wishlist (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    course_id INT NOT NULL,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(user_id, course_id),

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- COURSE REVIEWS
-- =========================================

CREATE TABLE reviews (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    course_id INT NOT NULL,

    rating INT CHECK(rating >=1 AND rating <=5),

    review TEXT,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- CERTIFICATES
-- =========================================

CREATE TABLE certificates (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    course_id INT NOT NULL,

    certificate_code VARCHAR(100)
    UNIQUE,

    issued_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- PAYMENTS
-- =========================================

CREATE TABLE payments (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    course_id INT NOT NULL,

    amount DECIMAL(10,2),

    payment_method VARCHAR(50),

    transaction_id VARCHAR(255),

    payment_status ENUM(
        'pending',
        'success',
        'failed'
    ) DEFAULT 'pending',

    paid_at TIMESTAMP NULL,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY(course_id)
    REFERENCES courses(id)
    ON DELETE CASCADE

);

-- =========================================
-- NOTIFICATIONS
-- =========================================

CREATE TABLE notifications (

    id INT PRIMARY KEY AUTO_INCREMENT,

    user_id INT NOT NULL,

    title VARCHAR(255),

    message TEXT,

    is_read TINYINT(1)
    DEFAULT 0,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE

);

-- =========================================
-- CONTACT MESSAGES
-- =========================================

CREATE TABLE contact_messages (

    id INT PRIMARY KEY AUTO_INCREMENT,

    name VARCHAR(120),

    email VARCHAR(150),

    subject VARCHAR(255),

    message TEXT,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP

);

-- =========================================
-- PASSWORD RESETS
-- =========================================

CREATE TABLE password_resets (

    id INT PRIMARY KEY AUTO_INCREMENT,

    email VARCHAR(150),

    token VARCHAR(255),

    expires_at TIMESTAMP,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP

);

-- =========================================
-- SAMPLE CATEGORIES
-- =========================================

INSERT INTO categories(category_name, slug)
VALUES
('Web Development', 'web-development'),
('Data Science', 'data-science'),
('Cyber Security', 'cyber-security'),
('UI UX Design', 'ui-ux-design');

-- =========================================
-- SAMPLE ADMIN
-- PASSWORD = password
-- =========================================

INSERT INTO users(

    full_name,
    username,
    email,
    password,
    role,
    email_verified

)

VALUES(

    'SkillSphere Admin',
    'admin',
    'admin@skillsphere.com',

    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',

    'admin',

    1

);

-- =========================================
-- INDEXES FOR PERFORMANCE
-- =========================================

CREATE INDEX idx_course_title
ON courses(title);

CREATE INDEX idx_user_email
ON users(email);

CREATE INDEX idx_category_slug
ON categories(slug);

CREATE INDEX idx_course_slug
ON courses(slug);

CREATE INDEX idx_notification_user
ON notifications(user_id);