# Layanan Informasi

## Tujuan

Konten informasi terstruktur berdasarkan kategori dengan dukungan pencarian.

## Lokasi Terkait

- Model: `app/Models/LayananModel.php`
- Views/Controllers: sesuai implementasi halaman layanan (user-facing)
- Migrasi: `app/Database/Migrations/*`

## Fitur

- Kategori-based listing dan filter.
- Pencarian (rekomendasi FULLTEXT pada `judul, deskripsi`).
- Pagination dan urutan berdasarkan `created_at`.

## Performa

- Index untuk `kategori`, `created_at`, dan FULLTEXT untuk pencarian.

## Keamanan

- Validasi input pencarian untuk mencegah query berbahaya.
