<?php
/**
 * 3.5 Menu Management - Insert  +  3.6 Image Upload Feature.
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

$errors = [];
$name = $price = $category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imageName = null;

    // --- Validation ---
    if ($name === '')                       $errors[] = 'Item name is required.';
    if ($price === '' || !is_numeric($price) || $price < 0)
                                            $errors[] = 'Enter a valid price.';
    if ($category === '')                   $errors[] = 'Category is required.';

    // --- 3.6 Image upload ---
    if (!empty($_FILES['image']['name'])) {
        $imageName = handleImageUpload($_FILES['image'], $errors);
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn,
            'INSERT INTO menu_items (name, price, category, image) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sdss', $name, $price, $category, $imageName);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?msg=' . urlencode('Menu item added successfully.'));
            exit;
        }
        $errors[] = 'Could not save the item. Please try again.';
        mysqli_stmt_close($stmt);
    }
}

/**
 * Validates and stores an uploaded image in /uploads.
 * Returns the stored filename, or null on failure (errors pushed by reference).
 */
function handleImageUpload(array $file, array &$errors): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Image upload failed.';
        return null;
    }
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',  'gif'  => 'image/gif',  'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!array_key_exists($ext, $allowed)) {
        $errors[] = 'Image must be JPG, PNG, GIF or WEBP.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {   // 2 MB cap
        $errors[] = 'Image must be smaller than 2 MB.';
        return null;
    }

    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $newName = uniqid('item_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $newName)) {
        $errors[] = 'Could not move the uploaded image.';
        return null;
    }
    return $newName;
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="fw-bold mb-4"><i class="bi bi-plus-circle"></i> Add Menu Item</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (&#8377;)</label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control"
                                   value="<?= htmlspecialchars($price) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" list="catlist"
                                   value="<?= htmlspecialchars($category) ?>" placeholder="e.g. Starter, Main, Dessert" required>
                            <datalist id="catlist">
                                <option>Starter</option><option>Main Course</option>
                                <option>Dessert</option><option>Beverage</option><option>Snack</option>
                            </datalist>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Food Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF or WEBP &middot; max 2 MB.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-dark"><i class="bi bi-save"></i> Save Item</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
