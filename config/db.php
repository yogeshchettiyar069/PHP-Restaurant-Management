<?php
/**
 * Database connection (MySQLi).
 *
 * Auto-detects environment so the SAME file works both locally (XAMPP) and
 * on the live host — no need to edit this back and forth.
 *   - On localhost / 127.0.0.1  -> uses the XAMPP credentials below.
 *   - Anywhere else (the host)  -> uses the LIVE credentials below.
 */
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = ($host === '' || str_contains($host, 'localhost') || str_contains($host, '127.0.0.1'));

if ($isLocal) {
    // ---- Local XAMPP ----
    $DB_HOST = '127.0.0.1';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_NAME = 'restaurant_db';
    $DB_PORT = 3307;   // XAMPP MariaDB runs on 3307 here (3306 is taken by another MySQL instance)
} else {
    // ---- LIVE host (InfinityFree) — paste the values from your hosting panel ----
    $DB_HOST = 'sql303.infinityfree.com';   // "MySQL Host Name" from the panel
    $DB_USER = 'if0_42283047';              // "MySQL Username"
    $DB_PASS = 'Noopur456';          // "MySQL Password"
    $DB_NAME = 'if0_42283047_restaurant';   // "MySQL Database Name"
    $DB_PORT = 3306;                        // InfinityFree uses the standard MySQL port
}

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error() .
        '<br>Make sure MySQL is running in XAMPP and you have imported <code>database.sql</code>.');
}

mysqli_set_charset($conn, 'utf8mb4');
