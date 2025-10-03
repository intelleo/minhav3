<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    if (session()->has('logged_in')) {
      // Dapatkan path yang diakses
      $currentPath = $request->getUri()->getPath();

      // Jika akses /register, arahkan ke /login (jangan ke last_visited)
      if ($currentPath === '/register') {
        return redirect()->to('/login')->with('info', 'Anda sudah login. Tidak bisa daftar ulang.');
      }

      // Untuk /login atau lainnya, gunakan last_visited
      $lastVisited = session('last_visited');
      $redirect = $lastVisited ?? '/Dashboard';

      return redirect()->to($redirect)->with('info', 'Anda sudah login.');
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Tidak perlu
  }
}
