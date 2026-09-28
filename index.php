<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

$search = trim((string)($_GET['q'] ?? ''));

if ($search !== '') {
    $stmt = $pdo->prepare('SELECT id, name, category, price, stock, created_at FROM products WHERE name LIKE :name_search OR category LIKE :category_search ORDER BY id DESC');
    $searchLike = '%' . $search . '%';
    $stmt->execute([
        ':name_search' => $searchLike,
        ':category_search' => $searchLike,
    ]);
} else {
    // Tetap gunakan prepared statement walaupun query ini tidak menerima input pengguna.
    $stmt = $pdo->prepare('SELECT id, name, category, price, stock, created_at FROM products ORDER BY id DESC');
    $stmt->execute();
}

$products = $stmt->fetchAll();
$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <div>
            <div class="eyebrow">WEB MANAJEMEN</div>
            <h1>MANAJEMEN PRODUK</h1>
            <p>SISTEM PENGELOLAAN DATA PRODUK</p>
        </div>
        <a class="btn btn-primary" href="create.php">+ Tambah Produk</a>
    </div>
</header>

<main class="container page">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <section class="toolbar">
        <form method="get" class="search-form">
            <label for="q">Cari produk</label>
            <div class="search-row">
                <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Nama atau kategori...">
                <button class="btn btn-secondary" type="submit">Cari</button>
                <?php if ($search !== ''): ?>
                    <a class="btn btn-ghost" href="index.php">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="section-heading">
        <div>
            <h2>Daftar Produk</h2>
            <p><?= count($products) ?> produk ditemukan.</p>
        </div>
    </section>

    <?php if (!$products): ?>
        <div class="empty-state">
            <h3>Belum ada produk</h3>
            <p>Tambahkan produk pertama untuk mengisi daftar.</p>
            <a class="btn btn-primary" href="create.php">Tambah Produk</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <div class="card-top">
                        <span class="badge"><?= e((string)$product['category']) ?></span>
                        <span class="product-id">#<?= e((string)$product['id']) ?></span>
                    </div>

                    <h3><?= e((string)$product['name']) ?></h3>
                    <div class="price"><?= e(format_rupiah((float)$product['price'])) ?></div>

                    <div class="meta-row">
                        <span>Stok</span>
                        <strong><?= e((string)$product['stock']) ?></strong>
                    </div>

                    <div class="card-actions">
                        <a class="btn btn-secondary btn-small" href="edit.php?id=<?= (int)$product['id'] ?>">Edit</a>
                        <form method="post" action="delete.php" onsubmit="return confirm('Hapus produk ini?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                            <button class="btn btn-danger btn-small" type="submit">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
