# Alerts (UI Notifikasi)

## Lokasi

- `public/js/alerts.js`

## Tujuan

Menampilkan notifikasi sederhana ke pengguna (success/info/warning/error), dipanggil dari frontend atau interceptor Axios.

## API

```javascript
window.showAlert("success", "Berhasil menyimpan", 2500);
window.showAlert("error", "Terjadi kesalahan");
```

## Integrasi

- Dipakai di SPA Router saat online/offline.
- Dipakai di handler form/aksi AJAX untuk umpan balik pengguna.
