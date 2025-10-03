# PWA & Service Worker

## Lokasi

- `public/manifest.webmanifest`
- `public/sw.js`
- Referensi di layout: `app/Views/layout/usertemplate.php`

## Tujuan

Menambahkan kemampuan dasar PWA: manifest, ikon, warna tema, dan service worker untuk caching ringan/offline support.

## Integrasi

- Sertakan manifest dan theme-color di `<head>`.
- Daftarkan service worker pada `DOMContentLoaded`:

```javascript
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(() => {});
}
```

## Catatan

- Pastikan scope path benar (biasanya root `/`).
- Uji di HTTPS/localhost.
