<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string)($_POST['name'] ?? ''));
    $category = trim((string)($_POST['category'] ?? ''));
    $price = trim((string)($_POST['price'] ?? ''));
    $stock = trim((string)($_POST['stock'] ?? ''));

    set_old(compact('name', 'category', 'price', 'stock'));
    $errors = validate_product($name, $category, $price, $stock);

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO products (name, category, price, stock) VALUES (?, ?, ?, ?)');
            $priceValue = (float)$price;
            $stockValue = (int)$stock;
            $stmt->execute([$name, $category, $priceValue, $stockValue]);

            clear_old();
            flash('success', 'Produk berhasil ditambahkan.');
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000' && ($e->errorInfo[1] ?? null) === 1062) {
                $errors[] = 'Nama produk sudah digunakan. Gunakan nama yang unik.';
            } else {
                $errors[] = 'Produk gagal disimpan. Periksa koneksi atau struktur database.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container form-page">
    <div class="form-header">
        <div>
            <div class="eyebrow">CREATE</div>
            <h1>Tambah Produk</h1>
            <p>Isi data produk lalu simpan ke database.</p>
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

        <div class="form-grid">
            <div class="field field-full">
                <label for="name">Nama Produk</label>
                <input id="name" name="name" type="text" minlength="3" maxlength="100" required value="<?= old('name') ?>" placeholder="Contoh: Kopi Gula Aren">
                <small>Minimal 3 karakter dan harus unik.</small>
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" maxlength="50" required value="<?= old('category') ?>" placeholder="Contoh: Minuman">
            </div>

            <div class="field">
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="0.01" step="0.01" required value="<?= old('price') ?>" placeholder="10000">
            </div>

            <div class="field">
                <label for="stock">Stok</label>
                <input id="stock" name="stock" type="number" min="0" step="1" required value="<?= old('stock') ?>" placeholder="20">
            </div>
        </div>

        <div class="form-actions">
            <a class="btn btn-ghost" href="index.php">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan Produk</button>
        </div>
    </form>
</main>
</body>
</html>
