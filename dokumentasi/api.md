# API Endpoints & Format Respons

## Tujuan

Menstandarkan endpoint API, format respons, dan praktik terbaik.

## Lokasi Terkait

- Controller API: `app/Controllers/Api/*`
- Trait respons: `system/API/ResponseTrait.php`

## Konvensi

- Base path: `/api/...`
- Gunakan method HTTP sesuai semantik (GET/POST/PUT/DELETE).
- Respons JSON dengan bentuk umum:

```json
{ "success": true, "message": "...", "data": {} }
```

- Gunakan status code yang tepat (200, 201, 400, 401, 404, 422, 500).

## Autentikasi

- Jika ada endpoint terbatas, gunakan session/token sesuai kebutuhan.

## Error Handling

- Sertakan `message` yang jelas dan `errors` terstruktur untuk validasi.

## Pagination

- Tanggapi parameter `page`, `per_page` dan sertakan metadata pagination di respons.
