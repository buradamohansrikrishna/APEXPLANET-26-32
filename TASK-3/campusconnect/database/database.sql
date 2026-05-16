/* =========================
   CREATE DATABASE
========================= */

CREATE DATABASE IF NOT EXISTS campusconnect;

USE campusconnect;

/* =========================
   USERS TABLE
========================= */

CREATE TABLE users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    email VARCHAR(120) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM('admin','student')
    DEFAULT 'student',

    profile_pic VARCHAR(255)
    DEFAULT 'default.webp',

    status ENUM('active','inactive')
    DEFAULT 'active',

    last_login TIMESTAMP NULL,

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

);

/* =========================
   DEFAULT ADMIN USER
========================= */

/*
PASSWORD = admin123

(Generate real hash using PHP later)
*/

INSERT INTO users (

    name,
    email,
    password,
    role

)

VALUES(

    'Administrator',

    'admin@campusconnect.com',

    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',

    'admin'
);