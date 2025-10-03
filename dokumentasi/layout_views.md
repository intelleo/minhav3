# Layout & Struktur Views

## Lokasi

- Layout utama: `app/Views/layout/usertemplate.php`
- Partial umum: `app/Views/partials/*`
- Halaman: `app/Views/*`

## Tujuan

Membagi tampilan menjadi layout (shell), partial (komponen reuse), dan halaman konten.

## Prinsip

- Layout memuat CSS/JS global, header/footer, registrasi service worker.
- Partial menyimpan komponen mandiri (contoh: `button_spinner.php`).
- Halaman hanya fokus pada konten per-fitur.

## Tips

- Sertakan skrip spesifik halaman di dalam `.content` agar bekerja dengan SPA.
- Gunakan helper `base_url()`/`site_url()` untuk path.
