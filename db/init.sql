-- ============================================
-- GRIT FIT NUTRI - Local development schema + seed
-- ============================================
-- The application (config.php) uses these tables. There was no committed
-- schema, so this file reconstructs it from the queries in the codebase.
-- Safe to run repeatedly: tables use IF NOT EXISTS and seed rows use INSERT IGNORE.
--
-- Usage (see AGENTS.md for the full local setup):
--   mysql u582313683_gritfitnutri < db/init.sql

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT '',
    description TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category_id INT NOT NULL,
    description TEXT,
    short_desc VARCHAR(500) DEFAULT '',
    price DECIMAL(10,2) DEFAULT NULL,
    badge VARCHAR(50) DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    image1 VARCHAR(255) DEFAULT '',
    image2 VARCHAR(255) DEFAULT '',
    image3 VARCHAR(255) DEFAULT '',
    image4 VARCHAR(255) DEFAULT '',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    KEY idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) DEFAULT '',
    avatar VARCHAR(255) DEFAULT '',
    rating INT NOT NULL DEFAULT 5,
    review_text TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (username: admin / password: admin123). Change in prod.
INSERT IGNORE INTO admin_users (username, password)
VALUES ('admin', '$2y$10$bW6Hnl.OcmXFbxCbVGC6heXKlnPzgzaRUcwAISZO05VDyY4H/FBJG');

-- Minimal sample content so the storefront renders with data locally.
INSERT IGNORE INTO categories (name, slug, image, description, sort_order, is_active)
VALUES ('Energy Bars', 'energy-bars', '', 'Premium superfood energy bars.', 1, 1);

INSERT IGNORE INTO reviews (name, location, avatar, rating, review_text, is_active)
VALUES ('Aarav Sharma', 'New Delhi', '', 5, 'Best energy bars I have ever tried. Clean ingredients and great taste!', 1);
