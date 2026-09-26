<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    flash('success', 'Produk berhasil dihapus.');
} else {
    flash('error', 'Produk tidak ditemukan atau sudah dihapus.');
}

redirect('index.php');
