<?php
/**
 * 3.10 PDF / Excel Export Feature.
 * Streams the menu_items table as an Excel-readable spreadsheet (.xls / CSV),
 * downloaded directly by the browser. No external library required.
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

$result = mysqli_query($conn, 'SELECT id, name, category, price, created_at FROM menu_items ORDER BY id');

// Download headers
$filename = 'menu_items_' . date('Y-m-d') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Excel reads a simple HTML table as a worksheet
echo "\xEF\xBB\xBF"; // UTF-8 BOM
?>
<table border="1">
    <thead>
        <tr style="background:#343a40;color:#fff;font-weight:bold;">
            <th>ID</th><th>Item Name</th><th>Category</th><th>Price (INR)</th><th>Added On</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
