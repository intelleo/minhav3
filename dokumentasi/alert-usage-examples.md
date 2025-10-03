# 📋 Contoh Penggunaan Sistem Alert & Konfirmasi

## Skenario Umum

### **1. Hapus Data**

```javascript
// Hapus komentar
function deleteComment(commentId) {
  window.showConfirm(
    "Hapus Komentar",
    "Apakah Anda yakin ingin menghapus komentar ini?",
    function () {
      // Konfirmasi
      (window.api || axios)
        .delete(`/comments/${commentId}`)
        .then(() => {
          window.showAlert("success", "Komentar berhasil dihapus");
          // Refresh atau update UI
        })
        .catch((err) => {
          window.showAlert("error", "Gagal menghapus komentar");
        });
    },
    function () {
      // Batal
      console.log("Hapus komentar dibatalkan");
    }
  );
}
```

### **2. Logout**

```javascript
// Logout user
function logoutUser() {
  window.showConfirm(
    "Keluar dari Akun",
    "Apakah Anda yakin ingin keluar dari akun?",
    function () {
      // Konfirmasi logout
      window.location.href = "/logout";
    },
    function () {
      // Batal logout
      console.log("Logout dibatalkan");
    }
  );
}
```

### **3. Reset Form**

```javascript
// Reset form dengan konfirmasi
function resetForm() {
  window.showConfirm(
    "Reset Form",
    "Semua data yang sudah diisi akan hilang. Lanjutkan?",
    function () {
      // Konfirmasi reset
      document.getElementById("myForm").reset();
      window.showAlert("info", "Form telah direset");
    },
    function () {
      // Batal reset
      console.log("Reset form dibatalkan");
    }
  );
}
```

### **4. Save Changes**

```javascript
// Simpan perubahan dengan konfirmasi
function saveChanges() {
  window.showConfirm(
    "Simpan Perubahan",
    "Apakah Anda yakin ingin menyimpan perubahan ini?",
    function () {
      // Konfirmasi save
      const formData = new FormData(document.getElementById("editForm"));
      (window.api || axios)
        .post("/save-changes", formData)
        .then(() => {
          window.showAlert("success", "Perubahan berhasil disimpan");
        })
        .catch((err) => {
          window.showAlert("error", "Gagal menyimpan perubahan");
        });
    },
    function () {
      // Batal save
      console.log("Simpan perubahan dibatalkan");
    }
  );
}
```

### **5. Bulk Actions**

```javascript
// Hapus multiple items
function deleteSelectedItems(selectedIds) {
  const count = selectedIds.length;
  window.showConfirm(
    "Hapus Item Terpilih",
    `Apakah Anda yakin ingin menghapus ${count} item yang dipilih?`,
    function () {
      // Konfirmasi bulk delete
      (window.api || axios)
        .post("/bulk-delete", { ids: selectedIds })
        .then(() => {
          window.showAlert("success", `${count} item berhasil dihapus`);
          // Refresh atau update UI
        })
        .catch((err) => {
          window.showAlert("error", "Gagal menghapus item");
        });
    },
    function () {
      // Batal bulk delete
      console.log("Bulk delete dibatalkan");
    }
  );
}
```

## Skenario Form Validation

### **1. Form Submit dengan Validasi**

```javascript
// Submit form dengan validasi
function submitForm() {
  const form = document.getElementById("myForm");
  const formData = new FormData(form);

  // Validasi client-side
  if (!formData.get("email")) {
    window.showAlert("error", "Email harus diisi");
    return;
  }

  if (!formData.get("password")) {
    window.showAlert("error", "Password harus diisi");
    return;
  }

  // Submit dengan konfirmasi
  window.showConfirm(
    "Kirim Data",
    "Apakah data yang diisi sudah benar?",
    function () {
      (window.api || axios)
        .post("/submit-form", formData)
        .then(() => {
          window.showAlert("success", "Data berhasil dikirim");
          form.reset();
        })
        .catch((err) => {
          window.showAlert("error", "Gagal mengirim data");
        });
    },
    function () {
      console.log("Submit form dibatalkan");
    }
  );
}
```

### **2. File Upload dengan Konfirmasi**

```javascript
// Upload file dengan konfirmasi
function uploadFile() {
  const fileInput = document.getElementById("fileInput");
  const file = fileInput.files[0];

  if (!file) {
    window.showAlert("error", "Pilih file terlebih dahulu");
    return;
  }

  // Validasi ukuran file
  if (file.size > 5 * 1024 * 1024) {
    // 5MB
    window.showAlert("error", "Ukuran file maksimal 5MB");
    return;
  }

  window.showConfirm(
    "Upload File",
    `Upload file "${file.name}" (${(file.size / 1024 / 1024).toFixed(2)} MB)?`,
    function () {
      const formData = new FormData();
      formData.append("file", file);

      (window.api || axios)
        .post("/upload", formData)
        .then(() => {
          window.showAlert("success", "File berhasil diupload");
          fileInput.value = "";
        })
        .catch((err) => {
          window.showAlert("error", "Gagal mengupload file");
        });
    },
    function () {
      console.log("Upload file dibatalkan");
    }
  );
}
```

## Skenario Error Handling

### **1. Network Error**

```javascript
// Handle network error
function handleNetworkError(error) {
  if (error.code === "NETWORK_ERROR") {
    window.showAlert("error", "Tidak ada koneksi internet");
  } else if (error.response?.status === 401) {
    window.showAlert(
      "warning",
      "Sesi Anda telah berakhir. Silakan login kembali"
    );
    setTimeout(() => {
      window.location.href = "/login";
    }, 2000);
  } else if (error.response?.status === 403) {
    window.showAlert(
      "error",
      "Anda tidak memiliki izin untuk melakukan aksi ini"
    );
  } else {
    window.showAlert("error", "Terjadi kesalahan yang tidak terduga");
  }
}
```

### **2. Success Feedback**

```javascript
// Berbagai jenis success feedback
function showSuccessFeedback(action) {
  switch (action) {
    case "create":
      window.showAlert("success", "Data berhasil dibuat");
      break;
    case "update":
      window.showAlert("success", "Data berhasil diperbarui");
      break;
    case "delete":
      window.showAlert("success", "Data berhasil dihapus");
      break;
    case "upload":
      window.showAlert("success", "File berhasil diupload");
      break;
    default:
      window.showAlert("success", "Aksi berhasil dilakukan");
  }
}
```

## Best Practices

### **1. Konsistensi Pesan**

```javascript
// Gunakan pesan yang konsisten
const messages = {
  delete: "Apakah Anda yakin ingin menghapus item ini?",
  save: "Apakah Anda yakin ingin menyimpan perubahan?",
  logout: "Apakah Anda yakin ingin keluar dari akun?",
  reset: "Semua data akan hilang. Lanjutkan?",
};

// Penggunaan
window.showConfirm("Konfirmasi", messages.delete, onConfirm, onCancel);
```

### **2. Custom Button Text**

```javascript
// Untuk aksi yang berbeda, gunakan teks yang sesuai
function customConfirm(
  title,
  message,
  confirmText,
  cancelText,
  onConfirm,
  onCancel
) {
  // Implementasi custom dengan button text yang dapat disesuaikan
}
```

### **3. Loading State**

```javascript
// Tampilkan loading saat proses
function deleteWithLoading(itemId) {
  window.showConfirm(
    "Hapus Item",
    "Apakah Anda yakin ingin menghapus item ini?",
    function () {
      // Tampilkan loading
      window.showAlert("info", "Menghapus item...", 0); // 0 = tidak auto close

      (window.api || axios)
        .delete(`/items/${itemId}`)
        .then(() => {
          window.showAlert("success", "Item berhasil dihapus");
        })
        .catch((err) => {
          window.showAlert("error", "Gagal menghapus item");
        });
    },
    function () {
      console.log("Hapus item dibatalkan");
    }
  );
}
```

Sistem ini memberikan fleksibilitas dan konsistensi untuk semua kebutuhan konfirmasi dan notifikasi di aplikasi.
