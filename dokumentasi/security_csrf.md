# Security & CSRF

## Tujuan

Melindungi aplikasi dari serangan umum (CSRF, XSS, brute force) dan memastikan praktik aman.

## CSRF

- Aktifkan CSRF di konfigurasi CI4 (default aktif).
- Pastikan semua form memiliki token CSRF (CI4 otomatis menyisipkan bila helper/form digunakan).
- Untuk AJAX, sinkronkan token dari meta atau respons, lalu kirim sebagai header/field (lihat `public/js/axios-setup.js`).

## Input Validation & Sanitization

- Gunakan validation rules pada controller/model untuk input user.
- Escape output di views (`esc()`), batasi HTML yang diizinkan.

## Auth Hardening

- Hash password (bcrypt/argon2).
- Rate limit endpoint login/register.
- Regenerasi session ID setelah login.

## Headers & Transport

- Gunakan HTTPS di production.
- Atur CSP (Content Security Policy) bila memungkinkan.

## Session & Cookie

- HttpOnly, Secure, SameSite sesuai kebutuhan.
- Timeout session yang wajar dan logout yang jelas.
