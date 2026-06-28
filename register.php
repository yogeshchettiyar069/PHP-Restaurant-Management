<?php
/**
 * 3.1 User Registration Module (8 Marks)
 * Fields: Admin Name, Email, Password.
 * Password is hashed with password_hash() before storage.
 */
session_start();
require_once __DIR__ . '/config/db.php';

// Already logged in? Go home.
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors  = [];
$success = '';
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    // --- Validation ---
    if ($name === '')                                  $errors[] = 'Admin name is required.';
    if ($email === '')                                 $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))$errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 6)                         $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)                        $errors[] = 'Passwords do not match.';

    // --- Unique email check ---
    if (!$errors) {
        $stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'An account with this email already exists.';
        }
        mysqli_stmt_close($stmt);
    }

    // --- Insert ---
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Registration successful! You can now log in.';
            $name = $email = '';
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Restaurant Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="card auth-card p-4">
        <div class="text-center mb-3">
            <h3 class="fw-bold mb-0"><i class="bi bi-egg-fried text-warning"></i> Tasty Bites</h3>
            <p class="text-muted">Admin Registration</p>
        </div>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger py-2 mb-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 mb-2"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Admin Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button class="btn btn-dark w-100">Register</button>
        </form>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
</body>
</html>
