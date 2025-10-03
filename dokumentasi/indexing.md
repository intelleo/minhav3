# Strategi Indexing & Performa

## Tujuan

Meningkatkan kecepatan query dan skalabilitas dengan pemilihan indeks yang tepat dan pola akses data efisien.

## Jenis Index

- Single column index: untuk kolom filter/sort tunggal (mis. `status`, `created_at`).
- Composite index: untuk kombinasi kondisi (mis. `(status, category)`, `(tgl_mulai, tgl_akhir)`).
- Full-text index: untuk pencarian teks (mis. `judul, deskripsi`).
- Foreign key index: pada kolom relasi (mis. `mading_id`, `user_id`).

## Rekomendasi per Tabel (ringkas)

- `minha_mading_online`:
  - `idx_status (status)`, `idx_category (category)`, `idx_created_at (created_at)`,
  - `idx_status_category (status, category)`, `idx_tgl_range (tgl_mulai, tgl_akhir)`,
  - `FULLTEXT idx_search (judul, deskripsi)`, `idx_views (views)` bila diperlukan sorting popular.
- `minha_mading_comments`:
  - `idx_created_at (created_at)`, `idx_mading_created (mading_id, created_at)`,
  - `idx_mading_parent (mading_id, parent_id)`, `idx_user_created (user_id, created_at)`.
- `minha_layanan_informasi`:
  - `idx_kategori (kategori)`, `idx_created_at (created_at)`, `idx_judul (judul)`,
  - `FULLTEXT idx_search (judul, deskripsi)`.
- `minha_mading_likes`:
  - `idx_user_id (user_id)`, `idx_created_at (created_at)`.
- `minha_user_auth`, `minha_auth_admin`:
  - `idx_created_at (created_at)`, plus index kombinasi sesuai kebutuhan filter (mis. `(status, jurusan)`).

## Praktik Terbaik

- Buat indeks berdasarkan query nyata (filter, join, sort) dan uji dengan `EXPLAIN`.
- Hindari over-indexing: terlalu banyak indeks memperlambat `INSERT/UPDATE` dan menambah storage.
- Letakkan kolom paling selektif di depan komposit.
- Pertimbangkan urutan indeks untuk mendukung `ORDER BY` tanpa filesort.
- Review berkala: gunakan slow query log dan monitoring.

## Migrasi Index

Pastikan migrasi penambahan indeks dijalankan setelah tabel terkait tercipta. Contoh pola:

```php
$this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_status (status)");
```

## Caching

- Pertimbangkan caching hasil query yang mahal (Redis/Memcached) untuk daftar populer.
- Invalidasi cache saat ada perubahan data signifikan.

---

## Panduan Praktis Penggunaan Index (Intinya)

- Index aktif otomatis di MySQL; tidak perlu konfigurasi khusus di CodeIgniter.
- Supaya index benar-benar dipakai, ikuti pola ini:

1. WHERE/JOIN gunakan kolom yang di-index

- Baik: `WHERE status = 'aktif' AND category = 'info'`
- Kurang: `WHERE LOWER(status) = 'aktif'` (fungsi pada kolom menghambat index)

2. Urutan kolom untuk index komposit (leftmost prefix)

- Jika ada index `(status, category)`, maka:
  - Baik: `WHERE status = ? AND category = ?`
  - Cukup: `WHERE status = ?` (masih pakai prefix)
  - Kurang: `WHERE category = ?` saja (tidak memakai index komposit tersebut)
- Untuk `ORDER BY`: idealnya urut sesuai index. Contoh index `(status, created_at)`:
  - Baik: `WHERE status = ? ORDER BY created_at DESC`

3. Hindari fungsi/ekspresi pada kolom terindeks

- Kurang: `WHERE DATE(created_at) = '2025-09-12'`
- Baik: `WHERE created_at >= '2025-09-12 00:00:00' AND created_at < '2025-09-13 00:00:00'`

4. LIKE vs FULLTEXT

- `LIKE '%kata%'` umumnya tidak memakai index B-Tree.
- Gunakan FULLTEXT: `MATCH(judul, deskripsi) AGAINST('kata')` untuk pencarian teks.

5. Samakan tipe data/kolasi

- Bandingkan INT dengan INT (`user_id = 123`, bukan `'123'`).
- Pastikan kolasi string konsisten saat JOIN.

6. Pahami efek operator pada index

- Range/inequality (`>`, `<`, `BETWEEN`) menghentikan pemanfaatan kolom berikutnya dalam index komposit.
- `OR` pada kolom berbeda bisa memaksa full scan; pertimbangkan `UNION`.

## Contoh Query “Sejalan dengan Index”

- Mading list cepat (index `(status, created_at)`):

```php
$db->table('mading_online')
  ->where('status', 'aktif')
  ->orderBy('created_at', 'DESC')
  ->limit(10)->get();
```

- Komentar per mading (index `(mading_id, created_at)`):

```php
$db->table('mading_comments')
  ->where('mading_id', $id)
  ->orderBy('created_at', 'DESC')
  ->limit(20)->get();
```

- Filter tanggal tanpa fungsi pada kolom:

```php
$db->table('mading_online')
  ->where('created_at >=', $start.' 00:00:00')
  ->where('created_at <',  $end.' 00:00:00')
  ->get();
```

## Verifikasi (EXPLAIN)

Jalankan `EXPLAIN` pada query utama dan pastikan kolom `key` terisi index (bukan NULL) dan `rows` mengecil. Contoh:

```sql
EXPLAIN SELECT m.*
FROM minha_mading_online m
LEFT JOIN minha_auth_admin a ON a.id = m.admin_id
WHERE m.status = 'aktif'
  AND m.tgl_akhir >= CURDATE()
ORDER BY m.created_at DESC
LIMIT 10;
```

## Rekomendasi Tambahan

- Tambahkan UNIQUE index pada `user_auth.npm` (untuk login/cek duplikasi):

```sql
ALTER TABLE minha_user_auth ADD UNIQUE INDEX uniq_npm (npm);
```

- Jika sering sort pada `created_at` bersamaan filter `status`, pertimbangkan:

```sql
ALTER TABLE minha_mading_online ADD INDEX idx_status_created (status, created_at);
```
