<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$stmt = $conn->prepare('SELECT id, name, category, price, stock FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string)($_POST['name'] ?? ''));
    $category = trim((string)($_POST['category'] ?? ''));
    $price = trim((string)($_POST['price'] ?? ''));
    $stock = trim((string)($_POST['stock'] ?? ''));

    $errors = validate_product($name, $category, $price, $stock);

    if (!$errors) {
        try {
            $stmt = $conn->prepare('UPDATE products SET name = ?, category = ?, price = ?, stock = ? WHERE id = ?');
            $priceValue = (float)$price;
            $stockValue = (int)$stock;
            $stmt->bind_param('ssdii', $name, $category, $priceValue, $stockValue, $id);
            $stmt->execute();

            flash('success', 'Produk berhasil diperbarui.');
            redirect('index.php');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errors[] = 'Nama produk sudah digunakan. Gunakan nama yang unik.';
            } else {
                $errors[] = 'Produk gagal diperbarui.';
            }
        }
    }

    $product['name'] = $name;
    $product['category'] = $category;
    $product['price'] = $price;
    $product['stock'] = $stock;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container form-page">
    <div class="form-header">
        <div>
            <div class="eyebrow">UPDATE</div>
            <h1>Edit Produk</h1>
            <p>Perbarui data produk dengan aman.</p>
        </div>
        <a class="btn btn-ghost" href="index.php">← Kembali</a>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <strong>Periksa input:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="form-card" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">

        <div class="form-grid">
            <div class="field field-full">
                <label for="name">Nama Produk</label>
                <input id="name" name="name" type="text" minlength="3" maxlength="100" required value="<?= e((string)$product['name']) ?>">
                <small>Minimal 3 karakter dan harus unik.</small>
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" maxlength="50" required value="<?= e((string)$product['category']) ?>">
            </div>

            <div class="field">
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="0.01" step="0.01" required value="<?= e((string)$product['price']) ?>">
            </div>

            <div class="field">
                <label for="stock">Stok</label>
                <input id="stock" name="stock" type="number" min="0" step="1" required value="<?= e((string)$product['stock']) ?>">
            </div>
        </div>

        <div class="form-actions">
            <a class="btn btn-ghost" href="index.php">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</main>
</body>
</html>
