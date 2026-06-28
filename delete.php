<?php
/**
 * 3.5 Menu Management - Delete (also removes the image file).
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch image filename first so we can clean it up
$stmt = mysqli_prepare($conn, 'SELECT image FROM menu_items WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($row) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM menu_items WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])) {
        @unlink(__DIR__ . '/uploads/' . $row['image']);
    }
}

header('Location: index.php?msg=' . urlencode('Menu item deleted.'));
exit;
