<?php
/**
 * One-time setup helper.
 * Open http://localhost/PHP%20Restaurant%20Management/setup.php in a browser,
 * OR run:  php setup.php   from the project folder.
 *
 * Creates the database, tables, and a demo admin account.
 * Safe to run more than once. Delete this file after setup if you like.
 */
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = 3307;   // matches config/db.php

$cli = (php_sapi_name() === 'cli');
$nl  = $cli ? "\n" : "<br>";

$conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, '', $DB_PORT);
if (!$conn) {
    exit('Connection failed: ' . mysqli_connect_error() . $nl);
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS restaurant_db
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
mysqli_select_db($conn, 'restaurant_db');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(80) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

echo "Database and tables ready." . $nl;

// Demo admin: admin@demo.com / admin123
$res = mysqli_query($conn, "SELECT id FROM users WHERE email = 'admin@demo.com'");
if (mysqli_num_rows($res) === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $name = 'Demo Admin'; $email = 'admin@demo.com';
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);
    mysqli_stmt_execute($stmt);
    echo "Demo admin created -> email: admin@demo.com  password: admin123" . $nl;
} else {
    echo "Demo admin already exists (admin@demo.com / admin123)." . $nl;
}

// A few sample menu items so the page isn't empty
$count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM menu_items"))[0];
if ($count == 0) {
    $samples = [
        ['Margherita Pizza', 299.00, 'Main Course'],
        ['Veg Hakka Noodles', 189.00, 'Main Course'],
        ['Chocolate Brownie', 149.00, 'Dessert'],
        ['Masala Chai',        49.00, 'Beverage'],
        ['Paneer Tikka',      249.00, 'Starter'],
    ];
    $stmt = mysqli_prepare($conn, "INSERT INTO menu_items (name, price, category) VALUES (?, ?, ?)");
    foreach ($samples as $s) {
        mysqli_stmt_bind_param($stmt, 'sds', $s[0], $s[1], $s[2]);
        mysqli_stmt_execute($stmt);
    }
    echo "Inserted " . count($samples) . " sample menu items." . $nl;
}

echo $nl . "Setup complete. " . ($cli ? "" : '<a href="login.php">Go to Login</a>');
