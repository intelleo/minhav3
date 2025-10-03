# 📁 Struktur Folder Uploads

## Lokasi

- `public/uploads/` - Folder utama untuk file yang diakses browser
- `public/uploads/profile/` - Folder khusus untuk foto profil user

## Keamanan

- File `.htaccess` mencegah eksekusi PHP dan akses file sensitif
- File `index.html` mencegah directory browsing
- Hanya mengizinkan file gambar (jpg, jpeg, png, gif, webp)

## Fitur

- ✅ Upload foto profil dengan validasi
- ✅ Hapus foto profil lama otomatis
- ✅ Hapus foto profil manual
- ✅ Cache browser untuk optimasi
- ✅ Security headers

## API Endpoints

- `POST /Profile/update-photo` - Upload foto profil baru
- `POST /Profile/delete-photo` - Hapus foto profil
- `POST /Profile/update-password` - Update password

## Struktur File

```
public/uploads/
├── .htaccess              # Security rules
├── index.html             # Prevent directory browsing
└── profile/               # Foto profil user
    ├── .htaccess          # Security rules khusus profile
    ├── index.html         # Prevent directory browsing
    └── [random_name].jpg  # File foto profil
```

## Validasi Upload

- Format: jpg, jpeg, png, gif, webp
- Ukuran maksimal: 2MB
- Nama file: Random untuk keamanan
- Path database: `base_url('uploads/profile/filename')`
