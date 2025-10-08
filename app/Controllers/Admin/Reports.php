<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MadingModel;
use App\Models\MadingCommentModel;
use App\Models\MadingLikeModel;
use App\Models\UserAuthModel;

class Reports extends BaseController
{
  protected $madingModel;
  protected $commentModel;
  protected $likeModel;
  protected $userModel;

  public function __construct()
  {
    $this->madingModel = new MadingModel();
    $this->commentModel = new MadingCommentModel();
    $this->likeModel = new MadingLikeModel();
    $this->userModel = new UserAuthModel();
  }

  public function index()
  {
    // Initialize variables outside try-catch to prevent undefined variable errors
    $comments = [];
    $madingReports = [];
    $repliesByParent = [];
    $stats = [
      'total_comments' => 0,
      'total_mading' => 0,
      'total_views' => 0,
      'total_likes' => 0,
    ];
    $totalComments = 0;
    $totalMading = 0;

    try {
      // Pagination params
      $pc = max(1, (int) ($this->request->getGet('pc') ?? 1)); // page comments
      $pm = max(1, (int) ($this->request->getGet('pm') ?? 1)); // page mading
      $perComments = max(1, (int) ($this->request->getGet('ppc') ?? 10));
      $perMading   = max(1, (int) ($this->request->getGet('ppm') ?? 10));

      // Ambil semua komentar utama (hanya user) dengan data user dan mading
      $commentsBuilder = $this->commentModel->select('mading_comments.*, user_auth.namalengkap, user_auth.foto_profil, mading_online.judul as mading_judul')
        ->join('user_auth', 'user_auth.id = mading_comments.user_id', 'left')
        ->join('mading_online', 'mading_online.id = mading_comments.mading_id', 'left')
        ->where('mading_comments.user_type', 'user');

      // Total untuk pagination
      $totalComments = (clone $commentsBuilder)->countAllResults(false);

      // Data halaman ini
      $comments = $commentsBuilder
        ->orderBy('mading_comments.created_at', 'DESC')
        ->limit($perComments, ($pc - 1) * $perComments)
        ->get()->getResultArray();

      // Ambil semua balasan untuk komentar di atas (user/admin)
      $repliesByParent = [];
      if (!empty($comments)) {
        $parentIds = array_column($comments, 'id');
        if (!empty($parentIds)) {
          $db = \Config\Database::connect();
          $replies = $this->commentModel->whereIn('parent_id', $parentIds)
            ->orderBy('created_at', 'ASC')
            ->findAll();
          foreach ($replies as &$rp) {
            $userType = $rp['user_type'] ?? 'user';
            if ($userType === 'admin') {
              $admin = $db->table('auth_admin')->select('username')->where('id', $rp['user_id'])->get()->getRowArray();
              $rp['namalengkap'] = $admin['username'] ?? 'Admin';
              $rp['foto_profil'] = null;
            } else {
              $user = $db->table('user_auth')->select('namalengkap, foto_profil')->where('id', $rp['user_id'])->get()->getRowArray();
              $rp['namalengkap'] = $user['namalengkap'] ?? 'User';
              $rp['foto_profil'] = $user['foto_profil'] ?? '';
            }
            $pid = (int)($rp['parent_id'] ?? 0);
            if (!isset($repliesByParent[$pid])) $repliesByParent[$pid] = [];
            $repliesByParent[$pid][] = $rp;
          }
        }
      }

      // Ambil laporan mading dengan view dan like (pagination)
      $madingBuilder = $this->madingModel->select('mading_online.*, auth_admin.username as admin_username')
        ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left');

      $totalMading = (clone $madingBuilder)->countAllResults(false);

      $madingReports = $madingBuilder
        ->orderBy('mading_online.created_at', 'DESC')
        ->limit($perMading, ($pm - 1) * $perMading)
        ->get()->getResultArray();

      // Enrich data mading dengan total likes dan comments
      $madingReports = $this->madingModel->enrichMadingDataPublic($madingReports);

      // Hitung statistik
      $stats = [
        'total_comments' => $this->commentModel->where('user_type', 'user')->countAllResults(),
        'total_mading' => $this->madingModel->countAllResults(),
        'total_views' => $this->madingModel->selectSum('views')->first()['views'] ?? 0,
        'total_likes' => $this->likeModel->countAllResults(),
      ];
    } catch (\Exception $e) {
      log_message('error', 'Reports index error: ' . $e->getMessage());
      // Variables already initialized above, no need to reassign
    }

    $data = [
      'title' => 'Reports & Analytics',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'comments' => $comments,
      'madingReports' => $madingReports,
      'stats' => $stats,
      'repliesByParent' => $repliesByParent,
      'pagination' => [
        'comments' => [
          'current_page' => $pc ?? 1,
          'per_page' => $perComments ?? 10,
          'total' => $totalComments,
          'total_pages' => (int) ceil(max(1, $totalComments) / max(1, $perComments ?? 10)),
          'query_key_page' => 'pc',
          'query_key_perpage' => 'ppc',
        ],
        'mading' => [
          'current_page' => $pm ?? 1,
          'per_page' => $perMading ?? 10,
          'total' => $totalMading,
          'total_pages' => (int) ceil(max(1, $totalMading) / max(1, $perMading ?? 10)),
          'query_key_page' => 'pm',
          'query_key_perpage' => 'ppm',
        ],
      ],
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/reports', $data);
    }

    return view('admin/reports', $data);
  }

  public function replyComment()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $parentId = $this->request->getPost('parent_id');
    $madingId = $this->request->getPost('mading_id');
    $reply = $this->request->getPost('reply');

    if (empty($parentId) || empty($madingId) || empty($reply)) {
      return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
    }

    try {
      $adminId = (int) session('admin_id');
      $adminUsername = (string) session('admin_username');

      $now = date('Y-m-d H:i:s');
      $insertData = [
        'mading_id' => (int) $madingId,
        'user_id' => $adminId,
        'user_type' => 'admin',
        'parent_id' => (int) $parentId,
        'isi_komentar' => (string) $reply,
        'created_at' => $now,
        'updated_at' => $now,
      ];

      $this->commentModel->insert($insertData);
      $newId = (int) ($this->commentModel->getInsertID() ?? 0);

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Balasan berhasil dikirim',
        'csrf' => csrf_hash(),
        'reply' => [
          'id' => $newId,
          'mading_id' => (int) $madingId,
          'parent_id' => (int) $parentId,
          'isi_komentar' => (string) $reply,
          'created_at' => $now,
          'namalengkap' => $adminUsername,
          'user_type' => 'admin',
        ],
      ]);
    } catch (\Exception $e) {
      log_message('error', 'Reply comment error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal mengirim balasan',
        'csrf' => csrf_hash()
      ]);
    }
  }
}
