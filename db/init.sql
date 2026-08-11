-- ============================================
-- GRIT FIT NUTRI - Schema + seed (CMS-enabled)
-- ============================================
-- Reconstructed / extended schema for the database-backed CMS.
-- Safe to run repeatedly:
--   * tables use CREATE TABLE IF NOT EXISTS
--   * new columns use ADD COLUMN IF NOT EXISTS (MariaDB)
--   * singleton seed rows use INSERT IGNORE
--   * list seed rows only insert when the table is still empty
--
-- Usage (see AGENTS.md):
--   mysql u582313683_gritfitnutri < db/init.sql

-- ---------- Core content ----------
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

-- Extra product attributes for the CMS-driven product page.
ALTER TABLE products ADD COLUMN IF NOT EXISTS grams VARCHAR(50) DEFAULT '';
ALTER TABLE products ADD COLUMN IF NOT EXISTS flavour VARCHAR(120) DEFAULT '';
ALTER TABLE products ADD COLUMN IF NOT EXISTS whatsapp_number VARCHAR(30) DEFAULT '';
ALTER TABLE products ADD COLUMN IF NOT EXISTS contact_number VARCHAR(30) DEFAULT '';

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
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS answer TEXT;
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0;

-- ---------- CMS: key/value settings (singletons) ----------
CREATE TABLE IF NOT EXISTS settings (
    skey VARCHAR(100) NOT NULL PRIMARY KEY,
    svalue TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- CMS: repeatable content ----------
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(120) NOT NULL,
    url VARCHAR(255) NOT NULL DEFAULT '/',
    new_tab TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(30) NOT NULL DEFAULT 'mid',
    image VARCHAR(255) DEFAULT '',
    title VARCHAR(255) DEFAULT '',
    subtitle VARCHAR(500) DEFAULT '',
    button_text VARCHAR(120) DEFAULT '',
    button_link VARCHAR(255) DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Slider slides can be image or video, and carry an optional second button.
ALTER TABLE banners ADD COLUMN IF NOT EXISTS video VARCHAR(500) DEFAULT '';
ALTER TABLE banners ADD COLUMN IF NOT EXISTS button2_text VARCHAR(120) DEFAULT '';
ALTER TABLE banners ADD COLUMN IF NOT EXISTS button2_link VARCHAR(255) DEFAULT '';

CREATE TABLE IF NOT EXISTS icons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(500) DEFAULT '',
    image VARCHAR(255) DEFAULT '',
    link VARCHAR(255) DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    image VARCHAR(255) DEFAULT '',
    link VARCHAR(255) DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(500) DEFAULT '',
    icon VARCHAR(255) DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- CMS: product sub-content ----------
CREATE TABLE IF NOT EXISTS product_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    feature VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    KEY idx_pf_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_variations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    pack_size VARCHAR(80) NOT NULL,
    price DECIMAL(10,2) DEFAULT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'In Stock',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    KEY idx_pv_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    KEY idx_pi_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- SEED DATA
-- ==========================================================

-- Default admin (username: admin / password: admin123). Change in prod.
INSERT IGNORE INTO admin_users (username, password)
VALUES ('admin', '$2y$10$bW6Hnl.OcmXFbxCbVGC6heXKlnPzgzaRUcwAISZO05VDyY4H/FBJG');

-- Sample category / review (only inserted if not present by unique slug / kept minimal).
INSERT IGNORE INTO categories (name, slug, image, description, sort_order, is_active)
VALUES ('Energy Bars', 'energy-bars', '', 'Premium superfood energy bars.', 1, 1);

-- Menu items (seed only when empty).
INSERT INTO menu_items (label, url, new_tab, sort_order, is_active)
SELECT * FROM (
    SELECT 'Home' AS label, '/' AS url, 0 AS new_tab, 1 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'About Us', '/about-us', 0, 2, 1
    UNION ALL SELECT 'Products', '/products', 0, 3, 1
    UNION ALL SELECT 'Contact', '/contact', 0, 4, 1
) t
WHERE NOT EXISTS (SELECT 1 FROM menu_items);

-- Homepage "Clean Energy" feature cards (icons; seed only when empty).
INSERT INTO icons (title, subtitle, image, link, sort_order, is_active)
SELECT * FROM (
    SELECT 'Zero Preservatives' AS title,
           'All our products are free from any form of preservatives or additives' AS subtitle,
           '' AS image, '' AS link, 1 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'Best Quality Ingredients', 'We have used the best quality ingredients sourcing them sustainably.', '', '', 2, 1
    UNION ALL SELECT 'Exceptional Quality', 'Our QC processes are very well defined and executed.', '', '', 3, 1
) t
WHERE NOT EXISTS (SELECT 1 FROM icons);

-- USPS unique selling points (seed only when empty).
INSERT INTO usps (title, description, icon, sort_order, is_active)
SELECT * FROM (
    SELECT '100% Natural' AS title, 'No preservatives, no artificial additives.' AS description, '' AS icon, 1 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'Lab Tested', 'Every batch is quality-checked and lab tested.', '', 2, 1
    UNION ALL SELECT 'Made in India', 'Proudly manufactured in world-class facilities.', '', 3, 1
    UNION ALL SELECT 'Fast Delivery', 'Quick and reliable shipping across the country.', '', 4, 1
) t
WHERE NOT EXISTS (SELECT 1 FROM usps);

-- Certificate/poster strip (seed only when empty).
INSERT INTO posters (title, image, link, sort_order, is_active)
SELECT * FROM (
    SELECT 'Certificate' AS title, 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=346,fit=crop/silemQfqUS99dRJ2/1500px_-removebg-preview-YyvZW0MyxRt7BLW8.jpg' AS image, '' AS link, 1 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'Certificate', 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=324,fit=crop/silemQfqUS99dRJ2/5-removebg-preview-AE0a8r6yZ6F2yqvm.jpg', '', 2, 1
    UNION ALL SELECT 'Certificate', 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=324,fit=crop/silemQfqUS99dRJ2/6-removebg-preview-mxBM6XD0vLTyG6gb.jpg', '', 3, 1
    UNION ALL SELECT 'Certificate', 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=312,fit=crop/silemQfqUS99dRJ2/4-removebg-preview-YNqPa24LZpFnE7xL.jpg', '', 4, 1
) t
WHERE NOT EXISTS (SELECT 1 FROM posters);

-- Homepage top slider slides (seed only when no slider banners exist).
-- First slide is a VIDEO slide (the original hero), the rest are image slides.
INSERT INTO banners (position, image, video, title, subtitle, button_text, button_link, button2_text, button2_link, sort_order, is_active)
SELECT * FROM (
    SELECT 'slider' AS position, '' AS image,
           'https://videos.pexels.com/video-files/8844271/8844271-uhd_4096_2160_24fps.mp4' AS video,
           'Superfood Bars' AS title, 'Fuel Your Day with Natural Energy and Health' AS subtitle,
           'About Us' AS button_text, '/about-us' AS button_link,
           'Products' AS button2_text, '/products' AS button2_link, 0 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'slider',
           'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-Yleqa574RZUoMZPw.jpg', '',
           'Superfood Energy Bars', 'Fuel your day the natural way with clean, powerful nutrition.',
           'Shop Products', '/products', '', '', 1, 1
    UNION ALL SELECT 'slider',
           'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,h=583,fit=crop/silemQfqUS99dRJ2/strawberry-about-mini-banner2-A3QOVoO0VXfMgyEo.jpg', '',
           'Clean Ingredients, Real Energy', 'Zero preservatives. Lab tested. Crafted for an active lifestyle.',
           'About Us', '/about-us', '', '', 2, 1
    UNION ALL SELECT 'slider',
           'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-dJo5gEZMqxU6QJ28.jpg', '',
           'Taste the Difference', 'Premium superfood bars in a range of delicious flavours.',
           'Explore Flavours', '/products', '', '', 3, 1
) t
WHERE NOT EXISTS (SELECT 1 FROM banners WHERE position='slider');

-- Homepage mid banner (seed only when empty).
INSERT INTO banners (position, image, title, subtitle, button_text, button_link, sort_order, is_active)
SELECT * FROM (
    SELECT 'mid' AS position,
           'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-Yleqa574RZUoMZPw.jpg' AS image,
           '' AS title, '' AS subtitle, '' AS button_text, '' AS button_link, 1 AS sort_order, 1 AS is_active
) t
WHERE NOT EXISTS (SELECT 1 FROM banners);

-- Default review (seed only when reviews table is empty).
INSERT INTO reviews (name, location, avatar, rating, review_text, answer, sort_order, is_active)
SELECT * FROM (
    SELECT 'Aarav Sharma' AS name, 'New Delhi' AS location, '' AS avatar, 5 AS rating,
           'Best energy bars I have ever tried. Clean ingredients and great taste!' AS review_text,
           '' AS answer, 1 AS sort_order, 1 AS is_active
) t
WHERE NOT EXISTS (SELECT 1 FROM reviews);
