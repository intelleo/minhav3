# Fitur Mading

## Tujuan

Mengelola posting mading, komentar bertingkat (thread), like, dan metrik views.

## Lokasi Terkait

- Controller: `app/Controllers/UserController/*`
- Models: `app/Models/MadingModel.php`, `MadingCommentModel.php`, `MadingLikeModel.php`
- Views: `app/Views/user/*` (termasuk `user_mading_detail.php`)
- Migrasi: `app/Database/Migrations/*`

## Struktur Inti

- `minha_mading_online`: tabel utama posting mading (status, category, judul, deskripsi, tgl_mulai, tgl_akhir, views).
- `minha_mading_comments`: komentar dengan `parent_id` untuk threading.
- `minha_mading_likes`: relasi like antar user dan mading.

## Pola Penggunaan

- Daftar mading: pagination + filter by status/category.
- Detail mading: tampilkan posting, komentar threaded, form komentar, dan like.
- Komentar: kirim via AJAX (lihat `user_mading_detail.php`), update partial komentar.

## Performa

- Indexing pada kolom query umum (status, category, created_at, tgl range, fulltext judul/deskripsi).
- Gunakan pagination, lazy loading, dan batasan kedalaman komentar.

## Keamanan

- Validasi input komentar, sanitasi HTML jika diperlukan.
- Batasi frekuensi submit (rate limit) untuk komentar/like.
