<?php
declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'product_manager';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan database product_manager sudah dibuat.');
}
