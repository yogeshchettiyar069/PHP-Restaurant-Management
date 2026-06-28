<?php
/**
 * 3.5 Menu Management - Update (optionally replaces the image, 3.6).
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Load current record
$stmt = mysqli_prepare($conn, 'SELECT * FROM menu_items WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$item) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = $item['name']; $price = $item['price']; $category = $item['category'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imageName = $item['image'];   // keep existing unless replaced

    if ($name === '')                       $errors[] = 'Item name is required.';
    if ($price === '' || !is_numeric($price) || $price < 0)
                                            $errors[] = 'Enter a valid price.';
    if ($category === '')                   $errors[] = 'Category is required.';

    // Optional new image
    if (!empty($_FILES['image']['name'])) {
        $new = handleImageUpload($_FILES['image'], $errors);
        if ($new) {
            // remove old file
            if (!empty($item['image']) && file_exists(__DIR__ . '/uploads/' . $item['image'])) {
                @unlink(__DIR__ . '/uploads/' . $item['image']);
            }
            $imageName = $new;
        }
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn,
            'UPDATE menu_items SET name = ?, price = ?, category = ?, image = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sdssi', $name, $price, $category, $imageName, $id);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?msg=' . urlencode('Menu item updated.'));
            exit;
        }
        $errors[] = 'Could not update the item.';
        mysqli_stmt_close($stmt);
    }
}

/** Same uploader as add.php */
function handleImageUpload(array $file, array &$errors): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Image upload failed.'; return null; }
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) { $errors[] = 'Image must be JPG, PNG, GIF or WEBP.'; return null; }
    if ($file['size'] > 2 * 1024 * 1024)  { $errors[] = 'Image must be smaller than 2 MB.'; return null; }
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $newName = uniqid('item_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $newName)) {
        $errors[] = 'Could not move the uploaded image.'; return null;
    }
    return $newName;
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="fw-bold mb-4"><i class="bi bi-pencil-square"></i> Edit Menu Item</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="id" value="<?= $id ?>">
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
                            <input type="text" name="category" class="form-control"
                                   value="<?= htmlspecialchars($category) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Food Image</label>
                        <?php if (!empty($item['image']) && file_exists(__DIR__ . '/uploads/' . $item['image'])): ?>
                            <div class="mb-2">
                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="table-thumb">
                                <small class="text-muted ms-2">Current image &mdash; choose a file to replace it.</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-dark"><i class="bi bi-save"></i> Update Item</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
