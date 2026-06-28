<?php
/**
 * 3.4 Logout - destroys the session and returns to the login page.
 */
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
