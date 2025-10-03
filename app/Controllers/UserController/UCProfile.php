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
                'rules' => 'uploaded[profilePhoto]|is_image[profilePhoto]|max_size[profilePhoto,2048]|mime_in[profilePhoto,image/jpg,image/jpeg,image/png,image/webp]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors(),
            ]);
        }

        $file = $this->request->getFile('profilePhoto');
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'File tidak valid']);
        }

        $newName = $file->getRandomName();

        // Pastikan folder uploads/profile ada
        $uploadPath = FCPATH . 'uploads/profile';
        if (!is_dir($uploadPath)) {
            if (!@mkdir($uploadPath, 0775, true)) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal membuat folder upload']);
            }
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

        // Simpan path relatif dalam database untuk portabilitas lingkungan
        $relativePath = 'uploads/profile/' . $newName;

        // Update database
        $model = new UserAuthModel();
        $model->update($userId, ['foto_profil' => $relativePath]);

        // Refresh session foto (tetap relatif)
        session()->set('foto_profil', $relativePath);

        // Kembalikan URL absolut untuk frontend saat ini
        return $this->response->setJSON([
            'message' => 'Foto profil berhasil diperbarui',
            'foto_profil' => base_url($relativePath),
        ]);
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
}
