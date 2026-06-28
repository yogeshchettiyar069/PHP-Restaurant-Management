-- Restaurant Menu Management System — LIVE HOST import file
-- Use this on InfinityFree (or any host where the database already exists).
-- In the host's phpMyAdmin: select your database on the left, open the
-- "Import" tab, choose this file, and click "Go".
--
-- NOTE: there is intentionally NO "CREATE DATABASE" / "USE" here — the host
-- already created the database for you and you import INTO it.

-- users table
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,            -- bcrypt hash from password_hash()
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- menu_items table
CREATE TABLE IF NOT EXISTS menu_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150)   NOT NULL,
    price      DECIMAL(10,2)  NOT NULL,
    category   VARCHAR(80)    NOT NULL,
    image      VARCHAR(255)   DEFAULT NULL,        -- filename stored in /uploads
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- Demo admin so recruiters can log in:  admin@demo.com / admin123
INSERT INTO users (name, email, password)
SELECT 'Demo Admin', 'admin@demo.com', '$2y$10$zrXU2O7Q/n5ZSwEr29egNe31tOAOWly.Fo17YduAJuAW.GAv3LiUe'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@demo.com');

-- Sample menu items so the page isn't empty
INSERT INTO menu_items (name, price, category) VALUES
    ('Margherita Pizza',   299.00, 'Main Course'),
    ('Veg Hakka Noodles',  189.00, 'Main Course'),
    ('Chocolate Brownie',  149.00, 'Dessert'),
    ('Masala Chai',         49.00, 'Beverage'),
    ('Paneer Tikka',       249.00, 'Starter');
