# 📝 Fitur Edit Bio User Profile

## Deskripsi

Fitur untuk mengedit bio user di halaman profile dengan validasi dan karakter counter yang real-time.

## Perubahan yang Dibuat

### **1. Header Profile**

- ✅ Mengubah `profile-role` dari status menjadi bio
- ✅ Menampilkan bio user atau "Belum ada bio" jika kosong
- ✅ Update real-time setelah bio disimpan

### **2. Tab Baru**

- ✅ Menambahkan tab "Edit Bio" di antara "Foto Profil" dan "Password"
- ✅ Tab aktif dapat di-switch dengan smooth transition

### **3. Form Edit Bio**

- ✅ Textarea dengan placeholder yang informatif
- ✅ Maksimal 500 karakter dengan validasi
- ✅ Karakter counter real-time dengan warna indikator
- ✅ Tombol "Simpan Bio" dan "Batal"

## Fitur Detail

### **Karakter Counter**

```javascript
// Real-time counter dengan warna indikator
function updateBioCounter() {
  const length = bioTextarea.value.length;
  bioCounter.textContent = length;

  // Warna berdasarkan panjang karakter
  if (length > 450) {
    bioCounter.style.color = "#ef4444"; // Merah - hampir limit
  } else if (length > 400) {
    bioCounter.style.color = "#f59e0b"; // Kuning - peringatan
  } else {
    bioCounter.style.color = "#6b7280"; // Abu-abu - normal
  }
}
```

### **Validasi**

- ✅ Maksimal 500 karakter
- ✅ Trim whitespace
- ✅ Server-side validation dengan CodeIgniter

### **UI/UX**

- ✅ Textarea dengan resize vertical
- ✅ Placeholder yang informatif
- ✅ Karakter counter dengan warna indikator
- ✅ Smooth transitions

## API Endpoints

### **Update Bio**

```php
POST /Profile/update-bio
Content-Type: application/x-www-form-urlencoded

bio=Isi bio user...
```

**Response:**

```json
{
  "message": "Bio berhasil diperbarui"
}
```

**Error Response:**

```json
{
  "message": "Validasi gagal",
  "errors": {
    "bio": "Bio maksimal 500 karakter"
  }
}
```

## Controller Method

### **updateBio()**

```php
public function updateBio()
{
    // Validasi HTTP method
    if (!$this->request->is('post')) {
        return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
    }

    // Validasi authentication
    $userId = (int) session('user_id');
    if (!$userId) {
        return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
    }

    // Validasi input
    $rules = [
        'bio' => 'permit_empty|max_length[500]'
    ];

    if (!$this->validate($rules)) {
        return $this->response->setStatusCode(422)->setJSON([
            'message' => 'Validasi gagal',
            'errors' => $this->validator->getErrors(),
        ]);
    }

    // Update database
    $bio = $this->request->getPost('bio');
    $model = new UserAuthModel();
    $model->update($userId, ['bio' => $bio]);

    // Refresh session
    session()->set('bio', $bio);

    return $this->response->setJSON(['message' => 'Bio berhasil diperbarui']);
}
```

## JavaScript Implementation

### **Form Submit**

```javascript
bioForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const bio = bioTextarea.value.trim();

  // Validasi client-side
  if (bio.length > 500) {
    window.showAlert("error", "Bio maksimal 500 karakter");
    return;
  }

  // Submit ke server
  const formData = new FormData();
  formData.append("bio", bio);

  (window.api || axios)
    .post("/Profile/update-bio", formData)
    .then((res) => {
      window.showAlert("success", "Bio berhasil diperbarui!");

      // Update bio di header profile
      const profileRole = document.querySelector(".profile-role");
      if (profileRole) {
        profileRole.textContent = bio || "Belum ada bio";
      }
    })
    .catch((err) => {
      window.showAlert("error", "Gagal memperbarui bio");
    });
});
```

### **Cancel Button**

```javascript
cancelBioBtn.addEventListener('click', function() {
  // Reset ke nilai asli
  bioTextarea.value = '<?= esc($user['bio'] ?? '') ?>';
  updateBioCounter();
});
```

## CSS Styling

### **Textarea**

```css
.form-group textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.form-group small {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.75rem;
}
```

## Database Schema

### **UserAuthModel**

```php
protected $allowedFields = [
    'namalengkap',
    'jurusan',
    'npm',
    'password',
    'status',
    'bio',        // ✅ Field bio sudah ada
    'foto_profil',
    'created_at',
    'updated_at'
];

protected $validationRules = [
    'bio' => 'permit_empty|max_length[500]'  // ✅ Validasi sudah ada
];
```

## Keuntungan Fitur

1. **User Experience:** Bio ditampilkan di header profile untuk visibility tinggi
2. **Real-time Feedback:** Karakter counter dengan warna indikator
3. **Validation:** Client-side dan server-side validation
4. **Consistency:** Menggunakan sistem alert yang sama
5. **Responsive:** Form responsive untuk mobile dan desktop
6. **Accessibility:** Proper labels dan ARIA attributes

## File yang Diupdate

1. **`app/Views/user/user_profile.php`** - UI dan JavaScript
2. **`app/Controllers/UserController/UCProfile.php`** - Method updateBio()
3. **`app/Config/Routes.php`** - Route untuk update-bio

Fitur edit bio sekarang sudah terintegrasi dengan baik di halaman user profile! 🎉
