# Perintah CLI & Cache

## Lokasi

- Commands kustom: `app/Commands/*` (mis. `CleanupCache.php`, `SyncCache.php`, `TestPerformance.php`)
- Skrip helper: `run_optimization.php`, `sync_cache.php`

## Tujuan

Menyediakan utilitas operasional untuk maintenance cache, sinkronisasi, dan pengujian performa.

## Penggunaan Umum

```bash
php spark cleanup:cache       # contoh pembersihan cache
php spark sync:cache          # contoh sinkronisasi cache
php spark test:performance    # contoh uji performa
```

Catatan: sesuaikan nama perintah dengan implementasi di setiap Command.
