// Contoh penggunaan sistem alert dan konfirmasi
// File: public/js/alert-examples.js

// 1. Alert Notifikasi
function showSuccessAlert() {
  window.showAlert("success", "Data berhasil disimpan!");
}

function showErrorAlert() {
  window.showAlert("error", "Terjadi kesalahan saat menyimpan data");
}

function showWarningAlert() {
  window.showAlert("warning", "Perhatian: Data akan dihapus");
}

function showInfoAlert() {
  window.showAlert("info", "Informasi: Fitur baru tersedia");
}

// 2. Konfirmasi Sederhana
function confirmDelete() {
  window.showConfirm(
    "Hapus Data",
    "Apakah Anda yakin ingin menghapus data ini?",
    function () {
      console.log("User mengkonfirmasi hapus");
      // Lakukan aksi hapus
    },
    function () {
      console.log("User membatalkan hapus");
    }
  );
}

// 3. Konfirmasi dengan AJAX
function confirmDeleteWithAjax(itemId) {
  window.showConfirm(
    "Hapus Item",
    "Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.",
    function () {
      // Konfirmasi - lakukan hapus
      fetch(`/api/items/${itemId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      })
        .then((response) => response.json())
        .then((data) => {
          window.showAlert("success", "Item berhasil dihapus");
          // Update UI atau redirect
        })
        .catch((error) => {
          window.showAlert("error", "Gagal menghapus item");
        });
    },
    function () {
      // Batal - tidak melakukan apa-apa
      console.log("Hapus item dibatalkan");
    }
  );
}

// 4. Konfirmasi Form Submit
function confirmFormSubmit(formId) {
  const form = document.getElementById(formId);
  const formData = new FormData(form);

  window.showConfirm(
    "Kirim Form",
    "Apakah data yang diisi sudah benar?",
    function () {
      // Konfirmasi - submit form
      fetch("/api/submit-form", {
        method: "POST",
        body: formData,
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      })
        .then((response) => response.json())
        .then((data) => {
          window.showAlert("success", "Form berhasil dikirim");
          form.reset();
        })
        .catch((error) => {
          window.showAlert("error", "Gagal mengirim form");
        });
    },
    function () {
      // Batal - tidak submit
      console.log("Submit form dibatalkan");
    }
  );
}

// 5. Konfirmasi Logout
function confirmLogout() {
  window.showConfirm(
    "Keluar dari Akun",
    "Apakah Anda yakin ingin keluar dari akun?",
    function () {
      // Konfirmasi - logout
      window.location.href = "/logout";
    },
    function () {
      // Batal - tetap di halaman
      console.log("Logout dibatalkan");
    }
  );
}

// 6. Konfirmasi dengan Loading State
function confirmWithLoading(action) {
  window.showConfirm(
    "Konfirmasi Aksi",
    "Apakah Anda yakin ingin melanjutkan?",
    function () {
      // Tampilkan loading
      window.showAlert("info", "Memproses...", 0); // 0 = tidak auto close

      // Simulasi proses
      setTimeout(() => {
        window.showAlert("success", "Aksi berhasil dilakukan");
      }, 2000);
    },
    function () {
      console.log("Aksi dibatalkan");
    }
  );
}

// 7. Multiple Alerts
function showMultipleAlerts() {
  window.showAlert("info", "Memulai proses...");

  setTimeout(() => {
    window.showAlert("warning", "Proses sedang berjalan...");
  }, 1000);

  setTimeout(() => {
    window.showAlert("success", "Proses selesai!");
  }, 3000);
}

// 8. Custom Alert dengan Timeout
function showCustomAlert() {
  window.showAlert("success", "Data tersimpan", 5000); // 5 detik
}

// 9. Error Handling dengan Alert
function handleApiError(error) {
  if (error.response?.status === 401) {
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
  } else if (error.response?.status === 404) {
    window.showAlert("error", "Data tidak ditemukan");
  } else if (error.response?.status >= 500) {
    window.showAlert("error", "Terjadi kesalahan server. Silakan coba lagi");
  } else {
    window.showAlert("error", "Terjadi kesalahan yang tidak terduga");
  }
}

// 10. Form Validation dengan Alert
function validateForm(formId) {
  const form = document.getElementById(formId);
  const formData = new FormData(form);

  // Validasi email
  if (!formData.get("email")) {
    window.showAlert("error", "Email harus diisi");
    return false;
  }

  // Validasi password
  if (!formData.get("password")) {
    window.showAlert("error", "Password harus diisi");
    return false;
  }

  // Validasi konfirmasi password
  if (formData.get("password") !== formData.get("confirm_password")) {
    window.showAlert("error", "Password dan konfirmasi password tidak cocok");
    return false;
  }

  return true;
}

// Export untuk penggunaan di modul lain
if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    showSuccessAlert,
    showErrorAlert,
    showWarningAlert,
    showInfoAlert,
    confirmDelete,
    confirmDeleteWithAjax,
    confirmFormSubmit,
    confirmLogout,
    confirmWithLoading,
    showMultipleAlerts,
    showCustomAlert,
    handleApiError,
    validateForm,
  };
}
