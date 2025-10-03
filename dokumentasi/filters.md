# Filters/Middleware

## Lokasi

- `app/Filters/AuthFilter.php`
- `app/Filters/GuestFilter.php`
- Konfigurasi: `app/Config/Filters.php`, `app/Config/Routes.php`

## Tujuan

Mengontrol akses berdasarkan status login dan memastikan rute yang tepat untuk user/guest.

## Pola Umum

- `AuthFilter`: proteksi halaman yang membutuhkan login (redirect ke login bila belum login).
- `GuestFilter`: proteksi halaman guest (redirect ke dashboard bila sudah login).

## Pemetaan

Atur alias dan grup di `Filters.php`, lalu terapkan pada rute di `Routes.php`.
