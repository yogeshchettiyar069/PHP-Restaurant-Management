<?php
/**
 * 3.3 Session Handling
 * Include this at the top of every protected (internal) page.
 * It starts the session and blocks access for users who are not logged in.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    // Prevent unauthorized access -> bounce to login
    header('Location: login.php');
    exit;
}
