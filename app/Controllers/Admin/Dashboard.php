<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserAuthModel;
use App\Models\MadingModel;
use App\Models\MadingCommentModel;

class Dashboard extends BaseController
{
  public function index()
  {
    // Initialize models
    $userModel = new UserAuthModel();
    $madingModel = new MadingModel();
    $commentModel = new MadingCommentModel();

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

    // Get content statistics with error handling
    try {
      $contentStats = [
        'total_mading' => $madingModel->countAllResults(),
        'total_comments' => $commentModel->countAllResults(),
        'chatbot_qa' => 0, // TODO: Implement when chatbot model is ready
      ];

      // Get mading statistics by status
      $madingStats = [
        'mading_aktif' => (clone $madingModel)->where('status', 'aktif')->countAllResults(),
        'mading_pending' => (clone $madingModel)->where('status', 'pending')->countAllResults(),
        'mading_nonaktif' => (clone $madingModel)->where('status', 'nonaktif')->countAllResults(),
      ];

      $contentStats = array_merge($contentStats, $madingStats);

      // Debug: Log the mading stats
      log_message('info', 'Mading Stats: ' . json_encode($madingStats));
    } catch (\Exception $e) {
      log_message('error', 'Dashboard content stats error: ' . $e->getMessage());
      $contentStats = [
        'total_mading' => 0,
        'total_comments' => 0,
        'chatbot_qa' => 0,
        'mading_aktif' => 0,
        'mading_pending' => 0,
        'mading_nonaktif' => 0,
      ];
    }

    $data = [
      'title' => 'Admin Dashboard',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'stats' => array_merge($userStats, $contentStats),
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/dashboard', $data);
    }

    return view('admin/dashboard', $data);
  }
}
