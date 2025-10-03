<?php

namespace App\Controllers;

use App\Models\AdminAuthModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdminAuthController extends BaseController
{
  protected AdminAuthModel $admins;

  public function __construct()
  {
    $this->admins = new AdminAuthModel();
  }

  public function login()
  {
    if (session()->get('admin_logged_in')) {
      return redirect()->to('/Admin/Dashboard');
    }
    $data = [
      'title' => 'Admin Login',
    ];
    return view('auth/admin_login', $data);
  }

  public function doLogin()
  {
    // Validasi input seperti pola login user
    $rules = [
      'username' => 'required|min_length[3]|max_length[50]',
      'password' => 'required|min_length[6]'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()->withInput()->with('error', 'Username atau password tidak valid.');
    }

    $username = trim((string) $this->request->getPost('username'));
    $password = (string) $this->request->getPost('password');

    $admin = $this->admins->findByUsername($username);
    if (!$admin || !password_verify($password, $admin['password'])) {
      return redirect()->back()->with('error', 'Kredensial tidak valid.')->withInput();
    }

    session()->set([
      'admin_logged_in' => true,
      'admin_id'        => $admin['id'],
      'admin_username'  => $admin['username'],
    ]);

    // Non-SPA redirect ke dashboard admin
    return redirect()->to('/Admin/Dashboard ')->with('success', 'Login admin berhasil!');
  }

  public function logout(): RedirectResponse
  {
    session()->remove(['admin_logged_in', 'admin_id', 'admin_username']);
    return redirect()->to('/admin/login')->with('success', 'Anda telah logout.');
  }
}
