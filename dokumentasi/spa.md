# SPA Router (public/js/spa-router.js)

## Tujuan

Router SPA ringan (gaya PJAX) yang mencegat klik link internal, mengambil HTML halaman, lalu hanya mengganti isi kontainer `.content` tanpa reload penuh. Mendukung history API, cache sederhana, eksekusi ulang skrip, dan fallback offline.

## Lokasi File

- `public/js/spa-router.js`

## Cara Kerja

1. Mencegat klik link internal (domain sama, bukan `target="_blank"`, bukan `download`, bukan anchor `#`).
2. Memanggil `navigate(url)` yang akan mengambil halaman via `axios` (pakai `window.api` jika ada) dan mem-parsing response menjadi DOM.
3. Mengambil elemen `.content` dari halaman target dan mengganti isi `.content` saat ini.
4. Memperbarui `document.title`, menulis riwayat via `history.pushState`, memasang ulang handler link, dan menjalankan skrip di konten baru.
5. Menyimpan ke localStorage sebagai cache untuk dukungan offline.
6. Jika gagal dan ada cache, menampilkan konten dari cache serta banner offline; jika tidak ada cache, fallback ke hard navigation (reload).

## API Internal Penting

- `navigate(url, push)`
- `__spaAttachLinks(root)` untuk memasang handler link pada kontainer yang disisipkan dinamis.

## Integrasi

- Pastikan halaman Anda memiliki kontainer utama dengan class `.content`.
- Sertakan `axios` dan inisialisasi `window.api` (opsional) via `public/js/axios-setup.js` untuk header/CSRF.
- Pastikan link yang ingin diabaikan SPA diberi `data-no-spa="true"` atau mengarah ke hash `#`.

## Fitur Tambahan

- Penanda menu aktif berdasarkan URL pada elemen `a.site`.
- Notifikasi online/offline (opsional) menggunakan `window.showAlert` bila tersedia.
- Cache per-URL dengan prefix `spa_cache:`.

## Contoh Minimal Struktur Layout

```html
<div class="content">
  <!-- konten halaman yang akan diganti -->
</div>
<script src="<?= base_url('vendor/axios.min.js') ?>"></script>
<script src="<?= base_url('js/axios-setup.js') ?>"></script>
<script src="<?= base_url('js/spa-router.js') ?>"></script>
```

## Tips

- Untuk halaman yang memerlukan skrip khusus, letakkan skrip tersebut di dalam kontainer `.content` agar dieksekusi ulang setelah navigasi SPA.
- Gunakan `data-no-spa="true"` untuk link yang tidak ingin dicegat (misalnya unduhan).
- Pastikan server mengembalikan HTML lengkap agar parser dapat mengambil `.content` dan `<title>`.
