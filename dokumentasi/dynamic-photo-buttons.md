# 📸 Fitur Upload Foto Profil - Dynamic Buttons

## Deskripsi

Tombol "Simpan Perubahan" dan "Batal" di bagian foto profil sekarang disembunyikan secara default dan hanya muncul ketika user memilih foto di input file.

## Perilaku UI

### **Default State (Tidak Ada File Dipilih):**

- ✅ Tombol "Simpan Perubahan" tersembunyi
- ✅ Tombol "Batal" tersembunyi
- ✅ Tombol "Hapus Foto" tetap terlihat (jika ada foto profil)
- ✅ Area preview kosong

### **File Dipilih State:**

- ✅ Tombol "Simpan Perubahan" muncul dengan animasi smooth
- ✅ Tombol "Batal" muncul dengan animasi smooth
- ✅ Preview gambar ditampilkan
- ✅ Tombol "Hapus Foto" tetap terlihat

### **Setelah Upload/Batal:**

- ✅ Tombol kembali tersembunyi
- ✅ Form direset
- ✅ Preview dihapus

## Animasi & Transisi

### **CSS Transitions:**

```css
#savePhotoBtn,
#cancelPhoto {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
```

### **JavaScript Animation:**

- **Show:** `opacity: 0 → 1`, `transform: translateY(-10px) → translateY(0)`
- **Hide:** `opacity: 1 → 0`, `transform: translateY(0) → translateY(-10px)`
- **Duration:** 300ms untuk smooth transition

## Event Handlers

### **File Input Change:**

```javascript
profilePhotoInput.addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (file) {
    // Show preview + buttons
    togglePhotoButtons(true);
  } else {
    // Hide buttons
    togglePhotoButtons(false);
  }
});
```

### **Cancel Button:**

```javascript
cancelPhotoBtn.addEventListener("click", function () {
  photoForm.reset();
  imagePreview.innerHTML = "";
  togglePhotoButtons(false);
});
```

### **After Upload Success:**

```javascript
.then(res => {
  // Update UI
  photoForm.reset();
  togglePhotoButtons(false);
});
```

## Keuntungan UX

1. **Clean Interface:** UI lebih bersih tanpa tombol yang tidak relevan
2. **Clear Actions:** User tahu kapan mereka bisa melakukan aksi
3. **Smooth Animation:** Transisi yang smooth meningkatkan pengalaman
4. **Intuitive Flow:** Alur yang intuitif sesuai dengan ekspektasi user
5. **Prevent Confusion:** Mencegah user bingung dengan tombol yang tidak aktif

## Implementasi

### **HTML:**

```html
<button
  type="submit"
  class="btn btn-primary"
  id="savePhotoBtn"
  style="display: none;"
>
  <i class="ri-save-line"></i>
  Simpan Perubahan
</button>
<button
  type="button"
  class="btn btn-secondary"
  id="cancelPhoto"
  style="display: none;"
>
  <i class="ri-close-line"></i>
  Batal
</button>
```

### **JavaScript Function:**

```javascript
function togglePhotoButtons(show) {
  // Smooth show/hide animation
  // Handle both savePhotoBtn and cancelPhotoBtn
}
```

Fitur ini meningkatkan UX dengan memberikan feedback visual yang jelas dan menghindari kebingungan user dengan tombol yang tidak relevan.
