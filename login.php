<?php
/**
 * 3.2 Login Module (6 Marks)
 * Verifies the password with password_verify() and starts a session.
 */
session_start();
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, name, password FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            // Valid credentials -> establish session
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurant Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="card auth-card p-4">
        <div class="text-center mb-3">
            <h3 class="fw-bold mb-0"><i class="bi bi-egg-fried text-warning"></i> Tasty Bites</h3>
            <p class="text-muted">Admin Login</p>
        </div>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger py-2 mb-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success py-2 mb-2">Account created. Please log in.</div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-dark w-100">Login</button>
        </form>
        <p class="text-center mt-3 mb-0">No account yet? <a href="register.php">Register</a></p>
    </div>
</div>
</body>
</html>
