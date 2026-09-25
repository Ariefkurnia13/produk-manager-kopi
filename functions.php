<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Permintaan ditolak: token keamanan tidak valid.');
    }
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function validate_product(string $name, string $category, string $price, string $stock): array
{
    $errors = [];

    $name = trim($name);
    $category = trim($category);

    if (mb_strlen($name) < 3) {
        $errors[] = 'Nama produk minimal 3 karakter.';
    }

    if ($category === '') {
        $errors[] = 'Kategori wajib diisi.';
    }

    if ($price === '' || !is_numeric($price)) {
        $errors[] = 'Harga harus berupa angka.';
    } elseif ((float)$price <= 0) {
        $errors[] = 'Harga harus lebih dari 0.';
    }

    if ($stock === '' || filter_var($stock, FILTER_VALIDATE_INT) === false) {
        $errors[] = 'Stok harus berupa bilangan bulat.';
    } elseif ((int)$stock < 0) {
        $errors[] = 'Stok tidak boleh negatif.';
    }

    return $errors;
}

function old(string $key, string $default = ''): string
{
    return e((string)($_SESSION['old'][$key] ?? $default));
}

function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function format_rupiah(float $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}
