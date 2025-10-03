# Axios Setup & Pola Request

## Lokasi

- `public/js/axios-setup.js`

## Tujuan

Menyediakan instance Axios global (`window.api`) dengan konfigurasi dasar: base URL, header CSRF, interceptors untuk error/alert.

## Penggunaan

```javascript
// GET
const res = await window.api.get("/path");

// POST (form-url-encoded)
const form = new URLSearchParams({ a: 1, b: 2 });
const res2 = await window.api.post("/path", form);
```

## CSRF

- Token diambil dari meta `<meta name="csrf-token" content="...">` atau dari respons.
- Dikirim via header (mis. `X-CSRF-TOKEN`) atau field form sesuai konfigurasi.

## Error Handling

- Interceptor dapat memanggil `window.showAlert(type, message)` bila tersedia.
- Tangani 401/419 (expired CSRF/session) dengan redirect ke login/reload sesuai kebijakan.
