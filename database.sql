-- Restaurant Menu Management System - Database Schema
-- Run this in phpMyAdmin (http://localhost/phpmyadmin) or via the MySQL CLI.

CREATE DATABASE IF NOT EXISTS restaurant_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE restaurant_db;

-- 5. Database Design -> users Table
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,            -- bcrypt hash from password_hash()
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- 5. Database Design -> menu_items Table
CREATE TABLE IF NOT EXISTS menu_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150)   NOT NULL,
    price      DECIMAL(10,2)  NOT NULL,
    category   VARCHAR(80)    NOT NULL,
    image      VARCHAR(255)   DEFAULT NULL,       -- filename stored in /uploads
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);
