# Database & Migrasi

## Tujuan

Mengelola skema database secara versioned menggunakan migrasi CI4.

## Lokasi Terkait

- Konfigurasi DB: `app/Config/Database.php`, `.env`
- Migrasi: `app/Database/Migrations/*`
- Seeds: `app/Database/Seeds/*`

## Perintah Dasar

```bash
php spark migrate         # jalankan semua migrasi
php spark migrate:status  # status migrasi
php spark migrate:rollback # rollback satu langkah
php spark migrate:refresh  # rollback semua lalu migrate lagi
```

## Praktik Terbaik

- Penamaan file migrasi dengan timestamp urut.
- Pastikan migrasi index dijalankan setelah tabel tercipta.
- Gunakan transaksi untuk operasi kompleks.
- Tambahkan index sesuai pola query (lihat dokumen Indexing).

## Konfigurasi Koneksi

- Gunakan `.env` untuk host, dbname, username, password, port, driver (`MySQLi`).
- Sesuaikan `DBPrefix` jika dipakai (mis. `Minha_`).
