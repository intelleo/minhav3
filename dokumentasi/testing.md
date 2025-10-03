# Testing

## Lokasi

- Konfigurasi: `phpunit.xml.dist`
- Direktori test: `tests/*`

## Tujuan

Menjamin kualitas lewat unit test untuk business logic dan integration test untuk endpoint.

## Jenis Test

- Unit: menguji fungsi/method terisolasi.
- Integration: menguji controller/route dan interaksi database.

## Menjalankan Test

```bash
vendor/bin/phpunit
```

## Praktik Terbaik

- Cakup skenario happy path dan error path.
- Gunakan seeding/migrations khusus test bila diperlukan.
- Jalankan di CI/CD untuk regresi otomatis.
