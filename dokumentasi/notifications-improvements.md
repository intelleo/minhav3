# Catatan Perbaikan Notifikasi (Minha-AI)

## Ringkasan

Fitur notifikasi balasan komentar diperbarui agar lebih responsif, akurat, dan konsisten:

- Long-press untuk hapus notifikasi (persisten di server).
- Klik notifikasi langsung menandai "seen" dan menyinkronkan indikator (dot).
- Indikator dot (navbar & card) diperbarui real-time via SPA tanpa reload penuh.
- Avatar notifikasi dinormalisasi URL-nya (mengikuti konsep avatar di halaman profil) + lazy-load.

## Perubahan Teknis

### 1) Persistensi Penghapusan (Dismiss)

- Migration baru: `notif_dismissed` (unique per `user_id`, `comment_id`).
  - File: `app/Database/Migrations/2025-09-16-000001_CreateNotifDismissed.php`
- Route backend:
  - `POST /Notifications/dismiss/(:num)` → tandai notifikasi balasan sebagai dihapus.
- Controller: `App\Controllers\UserController\UCNotifications`
  - `dismiss($id)`: insert ke `notif_dismissed`.
  - Query `index()` dan `count()` mengecualikan yang sudah dismissed (via subquery `NOT EXISTS` + `prefixTable`).

### 2) Status Tertandai (Seen)

- `POST /Notifications/seen/(:num)` menambahkan ID notifikasi ke `session('notif_seen_ids')`.
- `GET /Notifications/count` kini juga mengecualikan ID notifikasi yang ada di session `notif_seen_ids` sehingga dot hilang ketika item diklik.

### 3) Interaksi Frontend

- Halaman: `app/Views/user/user_notifications.php`
  - Long-press (600ms) pada item notifikasi untuk menghapus.
  - Klik link notifikasi: menandai seen (optimistic UI: latar biru hilang, dot disembunyikan), lalu navigasi.
  - Setelah dismiss/seen, panggil `window.__refreshNotifCount()` untuk sinkronisasi dot.
  - Avatar replier ditampilkan dengan normalisasi URL (absolut → ambil path → `base_url`), fallback inisial, lazy-load.

### 4) Indikator Notifikasi (Dot)

- Navbar (desktop: `#navNotifDot`, mobile: `#navNotifDotMobile`) & card Notifikasi di profil (`#cardNotifDot`).
- Fungsi global: `window.__refreshNotifCount()` di `app/Views/layout/usertemplate.php`:
  - Memanggil `/Notifications/count` (cache-buster) dan mengatur visibilitas dot navbar & card.
  - Dipanggil saat page load dan setelah navigasi SPA (`public/js/spa-router.js`).

### 5) SPA Router

- `public/js/spa-router.js`: setelah mengganti konten, memanggil `updateActiveNav(url)` dan `window.__refreshNotifCount()` supaya state UI sinkron tanpa reload.

### 6) CSRF & Axios

- CSRF di CI4 memakai mode cookie + header `X-CSRF-TOKEN`.
- `public/js/axios-setup.js`:
  - Membaca token awal dari meta `<meta name="csrf-token">`.
  - Menyisipkan header `X-CSRF-TOKEN` dan `X-Requested-With` untuk semua request.
  - Memperbarui token jika backend mengembalikan `csrf`/`csrf_hash` pada body response.

## File yang Diubah/Baru

- Backend:
  - `app/Controllers/UserController/UCNotifications.php` (index, count, seen, dismiss)
  - `app/Config/Routes.php` (rute Notifications)
  - `app/Database/Migrations/2025-09-16-000001_CreateNotifDismissed.php`
- Frontend:
  - `app/Views/user/user_notifications.php` (long-press, seen, UI, avatar normalisasi)
  - `app/Views/layout/usertemplate.php` (`__refreshNotifCount`, dot navbar & card)
  - `public/js/spa-router.js` (panggil `__refreshNotifCount` setelah navigasi)
  - `public/js/axios-setup.js` (setup CSRF global)

## Cara Kerja Akhir

1. Dot navbar & card dihitung dari `/Notifications/count`, mengecualikan dismissed (DB) dan seen (session).
2. Klik notifikasi:
   - UI langsung hilangkan latar biru & dot (optimistic), kirim `seen` ke server, navigasi.
3. Long-press item:
   - Animasi hapus + panggil `dismiss` untuk persist, dot diperbarui.
4. Navigasi SPA manapun memicu refresh dot otomatis.

## Catatan Tambahan

- Bila ingin Undo yang juga mengembalikan status server, tambahkan endpoint restore untuk menghapus row di `notif_dismissed` dan panggil saat tombol "Urungkan" ditekan.
- Jika menggunakan prefix tabel DB, kode sudah memakai `prefixTable('notif_dismissed')` untuk kompatibilitas.
