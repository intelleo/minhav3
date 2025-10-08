<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserAuthModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('auth/login');
    }

    public function register(): string
    {
        return view('auth/register');
    }

    // ✅ Method untuk proses login
    public function doLogin()
    {
        $model = new UserAuthModel();

        // Validasi input
        $rules = [
            'npm'      => 'required|min_length[10]|numeric',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'NPM atau password salah.');
        }

        $npm = $this->request->getPost('npm');
        $password = $this->request->getPost('password');

        // Cari user
        $user = $model->where('npm', $npm)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'NPM tidak ditemukan.');
        }

        if ($user['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Akun kamu belum aktif, Silakan hubungi admin.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah.');
        }

        // ✅ Simpan session login
        session()->set([
            'logged_in'   => true,
            'user_id'     => $user['id'],
            'npm'         => $user['npm'],
            'namalengkap' => $user['namalengkap'],
            'jurusan'     => $user['jurusan'],
            'bio'     => $user['bio'],
            'status'     => $user['status'],
            'foto_profil'     => $user['foto_profil'],
        ]);

        // 🔥 Ambil redirect_url dari flashdata (lebih aman)
        $redirect = session()->getFlashdata('redirect_url') ?? '/Dashboard';

        // Tidak perlu remove, karena otomatis hilang setelah get
        return redirect()->to($redirect)->with('success', 'Login berhasil!');
    }

    public function logout()
    {
        // Hapus hanya session user, jangan hapus session admin
        session()->remove(['logged_in', 'user_id', 'npm', 'namalengkap', 'jurusan', 'bio', 'status', 'foto_profil']);
        return redirect()->to('/login')->with('success', 'Logout berhasil.');
    }

    // register user baru

    public function doRegister()
    {
        $model = new UserAuthModel();

        // Validasi input
        $rules = [
            'namalengkap' => 'required|min_length[3]|max_length[100]',
            'jurusan'     => 'required|in_list[Komputerisasi Akuntansi,Manajemen Informatika,Sistem Informasi,Teknik Informatika,Sistem Komputer,Hukum,Administrasi Publik,Kewirausahaan]',
            'npm'         => 'required|numeric|min_length[10]|max_length[12]',
            'password'    => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon periksa kembali data Anda.');
        }

        $namalengkap = $this->request->getPost('namalengkap');
        $jurusan     = $this->request->getPost('jurusan');
        $npm         = $this->request->getPost('npm');
        $password    = $this->request->getPost('password');

        // Cek apakah NPM sudah terdaftar
        if ($model->where('npm', $npm)->first()) {
            return redirect()->back()->withInput()->with('error', 'NPM sudah terdaftar.');
        }

        // Data untuk disimpan
        $data = [
            'namalengkap' => $namalengkap,
            'jurusan'     => $jurusan,
            'npm'         => $npm,
            'password'    => $password, // akan di-hash oleh model
            'status'      => 'pending', // bisa diaktifkan oleh admin nanti
            // ✅ foto_profil dan bio diisi nanti di pengaturan
        ];

        // Simpan ke database
        if ($model->insert($data)) {
            // 🔐 Opsi: langsung login setelah register
            $user = $model->where('npm', $npm)->first();

            return redirect()->to('/login')->with('success', 'Registrasi berhasil!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
    }
}
