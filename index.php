<?php
/**
 * Home page (protected).
 * 3.9 Bootstrap Carousel  +  3.7 Display Data in Card Format.
 */
require_once __DIR__ . '/config/auth.php';   // 3.3 session guard
require_once __DIR__ . '/config/db.php';

// Latest items for the carousel (only those that have an image)
$carousel = mysqli_query($conn,
    "SELECT name, price, category, image FROM menu_items
     WHERE image IS NOT NULL AND image <> ''
     ORDER BY created_at DESC LIMIT 5");

// All items for the cards
$items = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY created_at DESC");

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===== 3.9 Bootstrap Carousel ===== -->
<?php if ($carousel && mysqli_num_rows($carousel) > 0): ?>
<div id="heroCarousel" class="carousel slide hero-carousel rounded-4 overflow-hidden shadow mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php for ($i = 0; $i < mysqli_num_rows($carousel); $i++): ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
                    class="<?= $i === 0 ? 'active' : '' ?>"></button>
        <?php endfor; ?>
    </div>
    <div class="carousel-inner">
        <?php $first = true; while ($c = mysqli_fetch_assoc($carousel)): ?>
            <div class="carousel-item <?= $first ? 'active' : '' ?>">
                <img src="uploads/<?= htmlspecialchars($c['image']) ?>" class="d-block w-100" alt="">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="fw-bold"><?= htmlspecialchars($c['name']) ?></h3>
                    <p><span class="badge bg-warning text-dark">Special Offer</span>
                       &mdash; <?= htmlspecialchars($c['category']) ?> &middot; &#8377;<?= number_format($c['price'], 2) ?></p>
                </div>
            </div>
            <?php $first = false; endwhile; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <h2 class="fw-bold mb-0">Our Menu</h2>
    <a href="add.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Add Menu Item</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<!-- ===== 3.7 Card Format ===== -->
<?php if ($items && mysqli_num_rows($items) > 0): ?>
<div class="row g-4">
    <?php while ($item = mysqli_fetch_assoc($items)): ?>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card menu-card h-100">
            <?php if (!empty($item['image']) && file_exists(__DIR__ . '/uploads/' . $item['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="">
            <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center text-white">
                    <i class="bi bi-image fs-1"></i>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                    <span class="badge bg-success price-badge">&#8377;<?= number_format($item['price'], 2) ?></span>
                </div>
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="bi bi-pencil"></i> Edit</a>
                <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger flex-fill"
                   onclick="return confirm('Delete this menu item?');">
                    <i class="bi bi-trash"></i> Delete</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php else: ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox fs-1"></i>
        <p class="mt-2">No menu items yet. <a href="add.php">Add the first one</a>.</p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
