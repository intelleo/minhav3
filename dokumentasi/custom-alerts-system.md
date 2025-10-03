# 🔔 Sistem Alert & Konfirmasi Custom

## Deskripsi

Sistem alert dan konfirmasi custom yang dapat digunakan di seluruh aplikasi Minha-AI untuk memberikan feedback dan konfirmasi kepada user.

## Fitur

### **1. Alert Notifikasi**

- ✅ Success, Error, Warning, Info
- ✅ Auto-close dengan timeout
- ✅ Manual close dengan tombol X
- ✅ Smooth animations
- ✅ Responsive design

### **2. Konfirmasi Dialog**

- ✅ Modal overlay dengan backdrop
- ✅ Custom title dan message
- ✅ Callback functions untuk konfirmasi/batal
- ✅ Keyboard support (Escape untuk tutup)
- ✅ Click outside untuk tutup
- ✅ Smooth animations

## API Usage

### **Alert Notifikasi**

```javascript
// Success alert
window.showAlert("success", "Data berhasil disimpan!");

// Error alert
window.showAlert("error", "Terjadi kesalahan saat menyimpan data");

// Warning alert
window.showAlert("warning", "Perhatian: Data akan dihapus");

// Info alert
window.showAlert("info", "Informasi: Fitur baru tersedia");

// Custom timeout (default: 3000ms)
window.showAlert("success", "Data tersimpan", 5000);
```

### **Konfirmasi Dialog**

```javascript
window.showConfirm(
  "Hapus Data", // Title
  "Apakah Anda yakin ingin menghapus data ini?", // Message
  function () {
    // On Confirm
    console.log("User mengkonfirmasi");
    // Lakukan aksi yang diinginkan
  },
  function () {
    // On Cancel
    console.log("User membatalkan");
    // Aksi jika dibatalkan (opsional)
  }
);
```

## Implementasi di User Profile

### **Hapus Foto Profil:**

```javascript
deletePhotoBtn.addEventListener("click", function () {
  window.showConfirm(
    "Hapus Foto Profil",
    "Apakah Anda yakin ingin menghapus foto profil? Tindakan ini tidak dapat dibatalkan.",
    function () {
      // Konfirmasi - hapus foto
      (window.api || axios)
        .post("/Profile/delete-photo")
        .then(() => {
          // Update UI dan tampilkan success
          window.showAlert("success", "Foto profil berhasil dihapus!");
        })
        .catch((err) => {
          window.showAlert("error", "Gagal menghapus foto profil");
        });
    },
    function () {
      // Batal - tidak melakukan apa-apa
      console.log("Hapus foto dibatalkan");
    }
  );
});
```

## Styling & Design

### **Alert Colors:**

- **Success:** Green (`#dcfce7` background, `#16a34a` border)
- **Error:** Red (`#fee2e2` background, `#dc2626` border)
- **Warning:** Yellow (`#fef9c3` background, `#ca8a04` border)
- **Info:** Blue (`#e0f2fe` background, `#0284c7` border)

### **Konfirmasi Dialog:**

- **Background:** White dengan rounded corners
- **Overlay:** Semi-transparent black
- **Buttons:** Cancel (gray) dan Confirm (red)
- **Typography:** Clean dan readable

## Animasi & Transisi

### **Alert:**

- Fade in/out dengan opacity transition
- Slide animation untuk multiple alerts

### **Konfirmasi:**

- Overlay fade in/out
- Dialog scale animation (0.95 → 1.0)
- Smooth transitions (300ms)

## Keyboard Support

### **Konfirmasi Dialog:**

- **Escape:** Tutup dialog dan trigger onCancel
- **Enter:** Tidak ada default action (user harus klik tombol)

## Responsive Design

### **Mobile:**

- Dialog width: 90% dengan max-width 400px
- Touch-friendly button sizes
- Proper spacing untuk mobile

### **Desktop:**

- Centered dialog
- Hover effects pada buttons
- Proper z-index layering

## Keuntungan

1. **Konsistensi:** UI yang konsisten di seluruh aplikasi
2. **Reusability:** Dapat digunakan di mana saja
3. **Accessibility:** Keyboard support dan proper ARIA labels
4. **Modern Design:** Clean dan professional appearance
5. **Smooth UX:** Animasi yang smooth dan tidak mengganggu
6. **Customizable:** Mudah disesuaikan dengan kebutuhan

## File Location

- **Source:** `public/js/alerts.js`
- **Usage:** Otomatis tersedia sebagai `window.showAlert()` dan `window.showConfirm()`
- **Integration:** Sudah terintegrasi dengan Axios interceptor untuk error handling
