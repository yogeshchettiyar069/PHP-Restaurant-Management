<?php
/**
 * 3.4 Navbar & Logout
 * Shared top navigation. Expects a session to already be started
 * (protected pages include config/auth.php first).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$adminName = $_SESSION['user_name'] ?? 'Admin';
$current   = basename($_SERVER['PHP_SELF']);
function navActive($file, $current) {
    return $file === $current ? 'active fw-bold' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Menu Management</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-egg-fried text-warning"></i> Tasty Bites
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= navActive('index.php', $current) ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('add.php', $current) ?>" href="add.php">
                        <i class="bi bi-plus-circle"></i> Add Menu Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('view.php', $current) ?>" href="view.php">
                        <i class="bi bi-table"></i> View Menu</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= navActive('export.php', $current) ?>" href="#"
                       data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Export Data</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="export.php">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="export_pdf.php">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF (.pdf)</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($adminName) ?></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
