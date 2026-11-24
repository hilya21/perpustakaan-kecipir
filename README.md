# Sistem Perpustakaan Kecipir

Aplikasi web Sistem Informasi Perpustakaan berbasis **Laravel** untuk mengelola data buku, keanggotaan, serta proses peminjaman dan pengembalian buku.

## Fitur Utama
- **Manajemen Data Buku:** Tambah, ubah, hapus, dan pencarian koleksi buku.
- **Manajemen Anggota:** Pengelolaan data anggota perpustakaan.
- **Transaksi Peminjaman & Pengembalian:** Pencatatan sirkulasi peminjaman buku oleh admin dan anggota.
- **REST API & Dokumentasi:** Pengujian endpoint menggunakan Postman dan dokumentasi API menggunakan Swagger/OpenAPI.

## Alur Sistem
Repositori ini juga dilengkapi diagram alur sistem:
- `Alur Sistem Perpustakaan Anggota dan Admin.png`
- `Alur Sistem Perpustakaan Berbasis Swimlane.png`

## Teknologi yang Digunakan
- **Framework:** Laravel (PHP)
- **Frontend:** Blade Template, HTML, CSS, JavaScript
- **Testing & Dokumentasi API:** Postman, Swagger

## Cara Menjalankan Proyek (Lokal)
1. Clone atau unduh repositori ini.
2. Jalankan `composer install` dan `npm install`.
3. Salin file `.env.example` menjadi `.env`, lalu atur koneksi database.
4. Jalankan `php artisan key:generate` dan `php artisan migrate`.
5. Jalankan server lokal dengan `php artisan serve`.
