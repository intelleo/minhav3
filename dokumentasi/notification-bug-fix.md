# Perbaikan Bug Notifikasi - Logout/Login Issue

## Masalah yang Ditemukan

Bug terjadi pada sistem notifikasi dimana notifikasi yang sudah dilihat (seen) akan muncul kembali setelah user logout dan login lagi. Hal ini terjadi karena:

1. **Sistem notifikasi menggunakan dua mekanisme berbeda:**

   - `notif_dismissed` table - untuk notifikasi yang dihapus (dismiss) - **PERSISTEN** ✅
   - `session('notif_seen_ids')` - untuk notifikasi yang dilihat (seen) - **SEMENTARA** ❌

2. **Ketika logout:** Session dihancurkan (`session()->destroy()`), sehingga `notif_seen_ids` hilang

3. **Ketika login lagi:** Notifikasi yang sudah dilihat sebelumnya akan muncul kembali karena tidak ada record di `notif_dismissed` table

## Solusi yang Diterapkan

### 1. Membuat Tabel `notif_seen` Baru

**File:** `app/Database/Migrations/2025-09-17-000001_CreateNotifSeen.php`

```sql
CREATE TABLE notif_seen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    comment_id INT UNSIGNED NOT NULL,
    created_at DATETIME NULL,
    UNIQUE KEY unique_user_comment (user_id, comment_id)
);
```

### 2. Update Controller Notifikasi

**File:** `app/Controllers/UserController/UCNotifications.php`

#### Perubahan pada method `index()`:

- **Sebelum:** Mengambil `seenIds` dari session
- **Sesudah:** Mengambil `seenIds` dari database `notif_seen`

#### Perubahan pada method `count()`:

- **Sebelum:** Mengecualikan seen berdasarkan session
- **Sesudah:** Mengecualikan seen berdasarkan database dengan subquery

#### Perubahan pada method `seen()`:

- **Sebelum:** Menyimpan ke session `notif_seen_ids`
- **Sesudah:** Menyimpan ke database `notif_seen`

### 3. Migrasi Data Existing

**File:** `app/Commands/MigrateNotifSeen.php`

Command untuk memigrasikan data `notif_seen_ids` yang sudah ada dari session files ke database:

```bash
php spark notif:migrate-seen
```

**Hasil migrasi:** 73 records berhasil dimigrasikan dari 10 session files.

## Keuntungan Solusi Ini

1. **Persistensi Data:** Status "seen" sekarang tersimpan di database, tidak hilang saat logout
2. **Konsistensi:** Semua status notifikasi (dismiss & seen) sekarang menggunakan database
3. **Backward Compatibility:** Data existing berhasil dimigrasikan tanpa kehilangan informasi
4. **Performance:** Query database lebih efisien daripada parsing session files

## Testing

Setelah perbaikan ini:

1. ✅ Notifikasi yang sudah dilihat tidak akan muncul lagi setelah logout/login
2. ✅ Notifikasi yang dihapus (dismiss) tetap tidak muncul (sudah berfungsi sebelumnya)
3. ✅ Dot notifikasi di navbar dan sidebar akan hilang dengan benar
4. ✅ Data existing berhasil dimigrasikan tanpa kehilangan informasi

## File yang Dimodifikasi

1. `app/Database/Migrations/2025-09-17-000001_CreateNotifSeen.php` - Migration baru
2. `app/Controllers/UserController/UCNotifications.php` - Update controller
3. `app/Commands/MigrateNotifSeen.php` - Command migrasi data
4. `dokumentasi/notification-bug-fix.md` - Dokumentasi ini

## Catatan Penting

- Migration sudah dijalankan dan data berhasil dimigrasikan
- Tidak ada breaking changes pada frontend
- Sistem notifikasi sekarang 100% menggunakan database untuk persistensi
