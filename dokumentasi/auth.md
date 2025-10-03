# Autentikasi (Login/Register) & Filters

## Tujuan

Menangani proses login, register, dan proteksi rute menggunakan filter.

## Lokasi Terkait

- Controller: `app/Controllers/AuthController.php`
- Views: `app/Views/auth/login.php`, `app/Views/auth/register.php`
- Filters: `app/Filters/AuthFilter.php`, `app/Filters/GuestFilter.php`
- Model: `app/Models/UserAuthModel.php`
- Routes: `app/Config/Routes.php`

## Alur Login

1. User submit form login (`/login`).
2. Controller memvalidasi input dan kredensial, set session jika sukses.
3. Redirect ke halaman user.

Contoh submit minimal:

```html
<form method="post" action="<?= site_url('login') ?>">
  <input name="username" required />
  <input name="password" type="password" required />
  <button type="submit" data-spinner="button">Login</button>
</form>
```

## Alur Register

1. User submit form register (`/register`).
2. Validasi, hashing password (bcrypt/argon2), simpan ke tabel user.
3. Redirect ke login atau auto-login sesuai kebijakan.

## Filters

- `AuthFilter`: memastikan user telah login untuk mengakses rute tertentu.
- `GuestFilter`: mencegah user yang sudah login mengakses halaman guest (mis. login/register).

Konfigurasi filters: `app/Config/Filters.php` dan pemetaan di `app/Config/Routes.php`.

## Keamanan

- Hash password (bcrypt/argon2).
- CSRF aktif untuk form (lihat dokumen Security & CSRF).
- Rate limiting pada endpoint auth direkomendasikan.

## Sesi

- Simpan minimal info user (id, role) di session.
- Hapus dan regenerasi session saat logout.
