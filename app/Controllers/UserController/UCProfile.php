<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use App\Models\UserAuthModel;
use CodeIgniter\HTTP\ResponseInterface;

class UCProfile extends BaseController
{
    public function index()
    {
        $userId = (int) session('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = (new UserAuthModel())
            ->select('id, namalengkap, jurusan, npm, status, bio, foto_profil, created_at')
            ->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Data user tidak ditemukan.');
        }

        $data = [
            'title' => 'Profil Pengguna',
            'user' => $user,
        ];

        return view('user/user_profile', $data);
    }

    public function updatePhoto()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
            }

            $userId = (int) session('user_id');
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
            }

            $validation = service('validation');
            $validation->setRules([
                'profilePhoto' => [
                    'label' => 'Foto Profil',
                    'rules' => 'uploaded[profilePhoto]|is_image[profilePhoto]|mime_in[profilePhoto,image/jpg,image/jpeg,image/png,image/webp]'
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                log_message('error', 'Upload validation failed: ' . json_encode($validation->getErrors()));
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $file = $this->request->getFile('profilePhoto');
            if (!$file || !$file->isValid()) {
                $error = $file ? $file->getErrorString() : 'File tidak ditemukan';
                log_message('error', 'File upload tidak valid: ' . $error);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'File tidak valid: ' . $error]);
            }

            // Debug info
            log_message('info', 'File upload info - Name: ' . $file->getName() . ', Size: ' . $file->getSize() . ', Type: ' . $file->getMimeType());
            log_message('info', 'Server info - PHP Version: ' . PHP_VERSION . ', Memory: ' . ini_get('memory_limit'));

            $newName = $file->getRandomName();

            // Pastikan folder uploads/profile ada
            $uploadPath = FCPATH . 'uploads/profile';
            if (!is_dir($uploadPath)) {
                if (!@mkdir($uploadPath, 0775, true)) {
                    log_message('error', 'Gagal membuat folder upload: ' . $uploadPath);
                    return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal membuat folder upload']);
                }
            }

            // Cek permission folder
            if (!is_writable($uploadPath)) {
                log_message('error', 'Folder upload tidak writable: ' . $uploadPath);
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Folder upload tidak dapat ditulis']);
            }

            // Hapus foto lama jika ada
            $user = (new UserAuthModel())->find($userId);
            if ($user && !empty($user['foto_profil'])) {
                $oldPath = str_replace(base_url(), FCPATH, $user['foto_profil']);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Simpan file baru
            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan file']);
            }

            // Optimasi dan kompresi gambar (optional - skip jika ada masalah)
            $fullPath = $uploadPath . '/' . $newName;

            // Skip optimasi untuk sementara untuk menghindari error 500
            // TODO: Enable optimasi setelah server stabil
            log_message('info', 'Skip optimasi gambar untuk menghindari error server');

            // Simpan path relatif dalam database untuk portabilitas lingkungan
            $relativePath = 'uploads/profile/' . $newName;

            // Update database
            $model = new UserAuthModel();
            $updateResult = $model->update($userId, ['foto_profil' => $relativePath]);

            if (!$updateResult) {
                log_message('error', 'Gagal update database - User ID: ' . $userId . ', Path: ' . $relativePath);
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan data ke database']);
            }

            log_message('info', 'Database updated successfully - User ID: ' . $userId . ', Path: ' . $relativePath);

            // Refresh session foto (tetap relatif)
            session()->set('foto_profil', $relativePath);

            // Redirect kembali agar halaman reload dan UI segar
            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Upload foto profil error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'debug' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    public function updatePassword()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
        }

        $userId = (int) session('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $rules = [
            'currentPassword' => 'required',
            'newPassword' => 'required|min_length[6]',
            'confirmPassword' => 'required|matches[newPassword]'
        ];
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $current = (string) $this->request->getPost('currentPassword');
        $new = (string) $this->request->getPost('newPassword');

        $model = new UserAuthModel();
        $user = $model->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }

        if (!password_verify($current, $user['password'])) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Password saat ini salah']);
        }

        // Simpan password baru (model sudah hash di beforeUpdate)
        $model->update($userId, ['password' => $new]);

        return $this->response->setJSON(['message' => 'Password berhasil diperbarui']);
    }

    public function updateBio()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
        }

        $userId = (int) session('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $rules = [
            'bio' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $bio = $this->request->getPost('bio');

        $model = new UserAuthModel();
        $model->update($userId, ['bio' => $bio]);

        // Refresh session bio
        session()->set('bio', $bio);

        return $this->response->setJSON(['message' => 'Bio berhasil diperbarui']);
    }

    public function deletePhoto()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
        }

        $userId = (int) session('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $model = new UserAuthModel();
        $user = $model->find($userId);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }

        // Hapus file foto dari server (dukung path relatif/absolut)
        if (!empty($user['foto_profil'])) {
            $stored = (string) $user['foto_profil'];
            if (preg_match('#^https?://#i', $stored) === 1) {
                // Absolut: konversi ke path lokal jika domain sama
                $filePath = str_replace(base_url(), FCPATH, $stored);
            } else {
                // Relatif
                $filePath = FCPATH . ltrim($stored, '/');
            }

            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Update database untuk menghapus path foto
        $model->update($userId, ['foto_profil' => null]);

        // Refresh session
        session()->remove('foto_profil');

        return $this->response->setJSON(['message' => 'Foto profil berhasil dihapus']);
    }

    /**
     * Method test untuk upload foto profil (tanpa optimasi)
     */
    public function updatePhotoTest()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
            }

            $userId = (int) session('user_id');
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
            }

            $validation = service('validation');
            $validation->setRules([
                'profilePhoto' => [
                    'label' => 'Foto Profil',
                    'rules' => 'uploaded[profilePhoto]|is_image[profilePhoto]|mime_in[profilePhoto,image/jpg,image/jpeg,image/png,image/webp]'
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                log_message('error', 'Upload validation failed: ' . json_encode($validation->getErrors()));
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $file = $this->request->getFile('profilePhoto');
            if (!$file || !$file->isValid()) {
                $error = $file ? $file->getErrorString() : 'File tidak ditemukan';
                log_message('error', 'File upload tidak valid: ' . $error);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'File tidak valid: ' . $error]);
            }

            // Log file info untuk debugging
            log_message('info', 'File upload info - Name: ' . $file->getName() . ', Size: ' . $file->getSize() . ', Type: ' . $file->getMimeType());

            // Cek ukuran file secara manual untuk debugging
            $fileSize = $file->getSize();
            $maxSize = 10240 * 1024; // 10MB dalam bytes
            log_message('info', 'File size check - Actual: ' . $fileSize . ' bytes, Max allowed: ' . $maxSize . ' bytes');

            if ($fileSize > $maxSize) {
                log_message('error', 'File too large: ' . $fileSize . ' bytes exceeds ' . $maxSize . ' bytes');
                return $this->response->setStatusCode(400)->setJSON(['message' => 'File terlalu besar. Maksimal 10MB.']);
            }

            $newName = $file->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile';

            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0775, true);
            }

            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan file']);
            }

            $relativePath = 'uploads/profile/' . $newName;
            $model = new UserAuthModel();
            $model->update($userId, ['foto_profil' => $relativePath]);
            session()->set('foto_profil', $relativePath);

            return $this->response->setJSON([
                'message' => 'Foto profil berhasil diperbarui (test mode)',
                'foto_profil' => base_url($relativePath),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Upload photo error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Optimasi dan kompresi gambar untuk mengurangi ukuran file
     */
    private function optimizeImage($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                log_message('error', 'File gambar tidak ditemukan: ' . $imagePath);
                return false;
            }

            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                log_message('error', 'Tidak dapat membaca informasi gambar: ' . $imagePath);
                return false;
            }

            $mimeType = $imageInfo['mime'];
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Resize jika gambar terlalu besar (max 800x800 untuk profil)
            $maxSize = 800;
            if ($width > $maxSize || $height > $maxSize) {
                $this->resizeImage($imagePath, $maxSize, $maxSize, $mimeType);
            }

            // Kompresi berdasarkan tipe file
            switch ($mimeType) {
                case 'image/jpeg':
                    $this->compressJpeg($imagePath);
                    break;
                case 'image/png':
                    $this->compressPng($imagePath);
                    break;
                case 'image/webp':
                    $this->compressWebp($imagePath);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error optimasi gambar: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resize gambar dengan mempertahankan aspect ratio
     */
    private function resizeImage($imagePath, $maxWidth, $maxHeight, $mimeType)
    {
        $imageInfo = getimagesize($imagePath);
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Hitung dimensi baru dengan mempertahankan aspect ratio
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Load gambar berdasarkan tipe
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($imagePath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($imagePath);
                break;
            default:
                return false;
        }

        if (!$source) {
            return false;
        }

        // Buat gambar baru dengan dimensi yang diresize
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency untuk PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize gambar
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Simpan gambar yang sudah diresize
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($resized, $imagePath, 85); // Quality 85%
                break;
            case 'image/png':
                imagepng($resized, $imagePath, 8); // Compression level 8
                break;
            case 'image/webp':
                imagewebp($resized, $imagePath, 85); // Quality 85%
                break;
        }

        // Clean up memory
        imagedestroy($source);
        imagedestroy($resized);

        return true;
    }

    /**
     * Kompresi JPEG dengan quality yang optimal
     */
    private function compressJpeg($imagePath)
    {
        $image = imagecreatefromjpeg($imagePath);
        if ($image) {
            imagejpeg($image, $imagePath, 85); // Quality 85% untuk balance antara kualitas dan ukuran
            imagedestroy($image);
        }
    }

    /**
     * Kompresi PNG dengan compression level optimal
     */
    private function compressPng($imagePath)
    {
        $image = imagecreatefrompng($imagePath);
        if ($image) {
            imagepng($image, $imagePath, 8); // Compression level 8 (0-9, 9 = max compression)
            imagedestroy($image);
        }
    }

    /**
     * Kompresi WebP dengan quality optimal
     */
    private function compressWebp($imagePath)
    {
        $image = imagecreatefromwebp($imagePath);
        if ($image) {
            imagewebp($image, $imagePath, 85); // Quality 85%
            imagedestroy($image);
        }
    }
}
