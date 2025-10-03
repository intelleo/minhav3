# Dokumentasi Spinner Button

## Ringkasan

Spinner button ini menampilkan lingkaran berputar menggantikan teks tombol ketika tombol diklik. Implementasi ringan, tidak mengubah tampilan tombol selain mengganti konten sementara menjadi spinner, dan otomatis aktif untuk tombol yang memiliki atribut `data-spinner="button"`.

## File Terkait

- `app/Views/partials/button_spinner.php` — berisi CSS spinner dan JS inisialisasi.

## Cara Menggunakan

### 1) Sertakan Partial

Tambahkan include partial pada view yang membutuhkan (biasanya di bagian `<head>` atau sebelum tombol digunakan):

```php
<?= $this->include('partials/button_spinner') ?>
```

### 2) Tandai Tombol

Tambahkan atribut `data-spinner="button"` pada tombol yang ingin diberi spinner saat diklik:

```html
<button type="submit" data-spinner="button">Login</button>
```

Berlaku juga untuk tombol lain seperti register, kirim, simpan, dan sebagainya:

```html
<button type="submit" data-spinner="button">Register</button>
<button type="button" data-spinner="button">Simpan</button>
```

## Perilaku

- Saat tombol diklik:
  - Menyimpan HTML asli tombol
  - Men-disable tombol
  - Mengganti isi tombol menjadi elemen spinner
- Jika halaman berpindah (submit form normal), spinner akan hilang saat halaman berganti.
- Jika Anda menggunakan AJAX dan ingin mengembalikan tombol ke keadaan semula setelah permintaan selesai, gunakan helper di bawah.

## Integrasi dengan AJAX

Jika melakukan request AJAX, Anda bisa mengembalikan tombol seperti semula setelah request selesai (sukses/gagal):

```javascript
// Misal: setelah fetch/axios selesai
restoreButtonSpinner("#btn-login"); // atau pass langsung element tombol
```

Contoh pola umum:

```javascript
const btn = document.querySelector("#btn-login");
// klik otomatis memicu spinner karena ada data-spinner="button"

try {
  const res = await axios.post("/login", payload);
  // ... handle sukses ...
} catch (e) {
  // ... handle error ...
} finally {
  restoreButtonSpinner(btn);
}
```

## Kustomisasi

- Spinner menggunakan `currentColor` untuk warna garis atas, sehingga otomatis mengikuti warna teks tombol.
- Ukuran default 16px. Anda dapat menimpa dengan CSS lokal jika diperlukan:

```css
.btn-spinner {
  width: 20px;
  height: 20px;
  border-width: 2px;
}
```

## Tips

- Pastikan hanya tombol yang memang memicu proses (submit/login/register) yang diberi `data-spinner="button"`.
- Pada proses yang cepat (navigasi/submit normal), spinner hanya tampil sejenak—ini normal dan memberi umpan balik instan kepada pengguna.
- Pada permintaan AJAX, selalu panggil `restoreButtonSpinner(...)` di blok `finally` agar tombol dipulihkan walaupun terjadi error.

## Troubleshooting

- Spinner tidak muncul: pastikan partial sudah di-include dan tombol memiliki `data-spinner="button"`.
- Tombol tidak kembali ke teks semula pada AJAX: panggil `restoreButtonSpinner(elementAtauSelector)` setelah request selesai.
- Warna spinner tidak sesuai: sesuaikan warna teks tombol (spinner mengikuti `currentColor`).
