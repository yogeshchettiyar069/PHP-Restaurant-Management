<?php
/**
 * 3.8 Table Display with CSS Gradient Animation.
 * Animated gradient header, hover effects (see assets/css/style.css).
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

$items = mysqli_query($conn, 'SELECT * FROM menu_items ORDER BY id DESC');

require_once __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-table"></i> Menu Items</h2>
    <div class="d-flex flex-wrap gap-2">
        <a href="add.php" class="btn btn-dark btn-sm"><i class="bi bi-plus-circle"></i> Add Item</a>
        <a href="export.php" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Excel</a>
        <a href="export_pdf.php" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
    </div>
</div>

<div class="gradient-table-wrap">
<table class="table table-hover align-middle mb-0 gradient-table menu-table bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Added On</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($items && mysqli_num_rows($items) > 0): $i = 1; ?>
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td data-label="#"><?= $i++ ?></td>
                <td data-label="Image">
                    <?php if (!empty($item['image']) && file_exists(__DIR__ . '/uploads/' . $item['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="table-thumb">
                    <?php else: ?>
                        <span class="text-muted"><i class="bi bi-image"></i></span>
                    <?php endif; ?>
                </td>
                <td data-label="Item Name" class="fw-semibold"><?= htmlspecialchars($item['name']) ?></td>
                <td data-label="Category"><span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span></td>
                <td data-label="Price">&#8377;<?= number_format($item['price'], 2) ?></td>
                <td data-label="Added On"><?= htmlspecialchars(date('d M Y', strtotime($item['created_at']))) ?></td>
                <td data-label="Actions" class="text-center text-nowrap">
                    <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> <span class="d-lg-none">Edit</span></a>
                    <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this menu item?');">
                        <i class="bi bi-trash"></i> <span class="d-lg-none">Delete</span></a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No menu items found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
