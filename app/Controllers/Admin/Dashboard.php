<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserAuthModel;
use App\Models\MadingModel;
use App\Models\MadingCommentModel;
use App\Models\LayananModel;

class Dashboard extends BaseController
{
  public function index()
  {
    // Initialize models
    $userModel = new UserAuthModel();
    $madingModel = new MadingModel();
    $commentModel = new MadingCommentModel();
    $layananModel = new LayananModel();

    // Get user statistics with error handling
    try {
      $userStats = [
        'total_users' => $userModel->countAllResults(),
        'active_users' => $userModel->where('status', 'aktif')->countAllResults(),
        'pending_users' => $userModel->where('status', 'pending')->countAllResults(),
        'inactive_users' => $userModel->where('status', 'nonaktif')->countAllResults(),
      ];
    } catch (\Exception $e) {
      log_message('error', 'Dashboard user stats error: ' . $e->getMessage());
      $userStats = [
        'total_users' => 0,
        'active_users' => 0,
        'pending_users' => 0,
        'inactive_users' => 0,
      ];
    }

    // Get content statistics dengan error handling
    try {
      $contentStats = [
        'total_mading' => $madingModel->countAllResults(),
        'active_mading' => $madingModel->where('status', 'aktif')->countAllResults(),
        'pending_mading' => $madingModel->where('status', 'pending')->countAllResults(),
        'inactive_mading' => $madingModel->where('status', 'nonaktif')->countAllResults(),
        'total_comments' => $commentModel->countAllResults(),
        'total_chatbot' => $layananModel->countAllResults(),
        // Kategori disesuaikan: BAAK dan BAUK
        'baak_chatbot' => $layananModel->where('kategori', 'BAAK')->countAllResults(),
        'bauk_chatbot' => $layananModel->where('kategori', 'BAUK')->countAllResults(),
        'umum_chatbot' => $layananModel->where('kategori', 'Umum')->countAllResults(),
      ];
    } catch (\Exception $e) {
      log_message('error', 'Dashboard content stats error: ' . $e->getMessage());
      $contentStats = [
        'total_mading' => 0,
        'active_mading' => 0,
        'pending_mading' => 0,
        'inactive_mading' => 0,
        'total_comments' => 0,
        'total_chatbot' => 0,
        'baak_chatbot' => 0,
        'bauk_chatbot' => 0,
        'umum_chatbot' => 0,
      ];
    }

    // Recent Activity
    try {
      $recent = [];
      // Users: terbaru
      $recentUsers = $userModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
      foreach ($recentUsers as $u) {
        $recent[] = [
          'type' => 'user',
          'created_at' => $u['created_at'] ?? date('Y-m-d H:i:s'),
          'title' => 'User baru terdaftar',
          'detail' => ($u['namalengkap'] ?? '') !== '' ? $u['namalengkap'] : (($u['npm'] ?? '') !== '' ? $u['npm'] : 'Pengguna'),
          'icon' => 'ri-user-add-line',
          'color' => 'blue'
        ];
      }

      // Mading: terbaru
      $recentMading = $madingModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
      foreach ($recentMading as $m) {
        $recent[] = [
          'type' => 'mading',
          'created_at' => $m['created_at'] ?? date('Y-m-d H:i:s'),
          'title' => 'Mading baru dipublikasikan',
          'detail' => $m['judul'] ?? 'Mading',
          'icon' => 'ri-news-line',
          'color' => 'green'
        ];
      }

      // Komentar: terbaru (user/admin)
      $recentComments = $commentModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
      foreach ($recentComments as $c) {
        $recent[] = [
          'type' => 'comment',
          'created_at' => $c['created_at'] ?? date('Y-m-d H:i:s'),
          'title' => 'Komentar baru',
          'detail' => mb_strimwidth((string)($c['isi_komentar'] ?? ''), 0, 80, '...'),
          'icon' => 'ri-message-line',
          'color' => 'yellow'
        ];
      }

      // Urutkan semua berdasarkan waktu desc dan ambil 10 teratas
      usort($recent, function ($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
      });
      $recent = array_slice($recent, 0, 10);
    } catch (\Exception $e) {
      log_message('error', 'Dashboard recent activity error: ' . $e->getMessage());
      $recent = [];
    }

    $data = [
      'title' => 'Admin Dashboard',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'stats' => array_merge($userStats, $contentStats),
      'recent' => $recent,
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/dashboard', $data);
    }

    return view('admin/dashboard', $data);
  }
}
