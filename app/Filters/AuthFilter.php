<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    if (!session()->has('logged_in')) {
      // Simpan: user ingin ke mana? (kecuali untuk AJAX/API endpoints)
      $currentUrl = current_url();

      // Daftar endpoint yang tidak boleh menjadi redirect target
      $excludedEndpoints = [
        'Notifications/count',
        'Notifications/seen',
        'Notifications/dismiss',
        'Mading/komentar',
        'Mading/like',
        'Mading/context-menu-action',
        'Mading/update-comment',
        'Mading/comments',
        'Profile/update-photo',
        'Profile/delete-photo',
        'Profile/update-bio',
        'Profile/update-password'
      ];

      // Cek apakah URL saat ini adalah endpoint yang dikecualikan
      $shouldExclude = false;
      foreach ($excludedEndpoints as $endpoint) {
        if (strpos($currentUrl, $endpoint) !== false) {
          $shouldExclude = true;
          break;
        }
      }

      // Jika bukan endpoint yang dikecualikan, simpan sebagai redirect target
      if (!$shouldExclude) {
        session()->setFlashdata('redirect_url', $currentUrl);
      }

      return redirect()->to('/login')->with('error', 'Silakan login dulu.');
    }

    // ✅ JIKA SUDAH LOGIN → catat URL ini sebagai "terakhir dikunjungi" (kecuali AJAX endpoints)
    $currentUrl = current_url();

    // Daftar endpoint yang tidak boleh disimpan sebagai last_visited
    $excludedEndpoints = [
      'Notifications/count',
      'Notifications/seen',
      'Notifications/dismiss',
      'Mading/komentar',
      'Mading/like',
      'Mading/context-menu-action',
      'Mading/update-comment',
      'Mading/comments',
      'Profile/update-photo',
      'Profile/delete-photo',
      'Profile/update-bio',
      'Profile/update-password'
    ];

    // Cek apakah URL saat ini adalah endpoint yang dikecualikan
    $shouldExclude = false;
    foreach ($excludedEndpoints as $endpoint) {
      if (strpos($currentUrl, $endpoint) !== false) {
        $shouldExclude = true;
        break;
      }
    }

    // Hanya simpan sebagai last_visited jika bukan endpoint yang dikecualikan
    if (!$shouldExclude) {
      session()->set('last_visited', $currentUrl);
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Tidak perlu
  }
}
