# Product Manager — Mini Project PHP + MySQL

Aplikasi CRUD manajemen produk sesuai tugas akhir Product Manager.

## Fitur yang dipenuhi

- **Create**: nama, kategori, harga, stok.
- **Read**: daftar produk berbentuk card dan pencarian nama/kategori.
- **Update**: edit data produk.
- **Delete**: hapus lewat `POST` + CSRF.
- Validasi nama minimal 3 karakter, harga > 0, stok >= 0.
- Nama produk harus unik melalui `UNIQUE` pada database dan pengecekan error `1062`.
- Semua query database menggunakan **prepared statement** untuk input dari pengguna.
- Output memakai `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` sehingga teks seperti `<b>Promo</b>` ditampilkan sebagai teks, bukan HTML.
- Setelah create/update/delete, aplikasi memakai pola **POST-Redirect-GET** agar refresh tidak mengirim ulang form.
- Tampilan responsive: card membungkus rapi pada layar sempit.

## Instalasi di XAMPP

1. Ekstrak folder `product-manager` ke:
   `C:/xampp/htdocs/product-manager`
2. Jalankan **Apache** dan **MySQL** dari XAMPP.
3. Buka `http://localhost/phpmyadmin`.
4. Import file `database.sql` atau buka SQL lalu jalankan seluruh isinya.
5. Pastikan database bernama `product_manager` sudah ada.
6. Buka:
   `http://localhost/product-manager/`

## Konfigurasi database

File `config.php` memakai konfigurasi XAMPP standar:

- Host: `127.0.0.1`
- User: `root`
- Password: kosong
- Database: `product_manager`

Kalau konfigurasi MySQL Anda berbeda, ubah empat variabel tersebut di `config.php`.

## Alur demo untuk dosen

1. Tambah produk valid → produk muncul di daftar.
2. Masukkan nama kurang dari 3 karakter → ditolak dan tidak tersimpan.
3. Masukkan harga negatif → ditolak.
4. Masukkan stok negatif → ditolak.
5. Tambah produk lalu refresh halaman → tidak ada duplikasi.
6. Masukkan nama `<b>Promo</b>` → akan ditampilkan sebagai teks `&lt;b&gt;Promo&lt;/b&gt;` di database/output yang aman, bukan menjadi HTML tebal.
7. Ubah produk → data berubah pada daftar.
8. Hapus produk → data terhapus setelah konfirmasi.
9. Uji layar sempit → card berubah menjadi satu kolom.

## Struktur file

```text
product-manager/
├── config.php
├── create.php
├── database.sql
├── delete.php
├── edit.php
├── functions.php
├── index.php
├── README.md
└── style.css
```
