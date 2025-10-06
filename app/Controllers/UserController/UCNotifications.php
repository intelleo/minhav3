<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;

class UCNotifications extends BaseController
{
  public function index()
  {
    $userId = (int) session('user_id');
    if (!$userId) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $db = \Config\Database::connect();
    // Notifikasi: balasan terhadap komentar user
    $dismissedTable = $db->prefixTable('notif_dismissed');
    $seenTable = $db->prefixTable('notif_seen');
    // Query untuk notifikasi dengan handle admin dan user
    $items = [];

    // Ambil semua komentar yang membalas user ini (hanya komentar user, bukan admin)
    $comments = $db->table('mading_comments child')
      ->select('child.*, parent.user_id as parent_user_id, parent.user_type as parent_user_type, mo.id as mading_id, mo.judul')
      ->join('mading_comments parent', 'parent.id = child.parent_id')
      ->join('mading_online mo', 'mo.id = child.mading_id')
      ->where('parent.user_id', $userId)
      ->where('(parent.user_type IS NULL OR parent.user_type != "admin")', null, false) // Hanya komentar user, bukan admin
      ->where("NOT EXISTS (SELECT 1 FROM {$dismissedTable} nd WHERE nd.user_id = {$userId} AND nd.comment_id = child.id)", null, false)
      ->orderBy('child.created_at', 'DESC')
      ->get()
      ->getResultArray();

    // Untuk setiap komentar, cari nama pengirim (admin atau user)
    foreach ($comments as $comment) {
      $replierId = $comment['user_id'];
      $userType = $comment['user_type'] ?? 'user'; // Default untuk data lama

      // Gunakan user_type untuk menentukan jenis pengirim
      if ($userType === 'admin') {
        // Ambil data admin
        $adminData = $db->table('auth_admin')
          ->select('username as namalengkap, NULL as foto_profil')
          ->where('id', $replierId)
          ->get()
          ->getRowArray();

        if ($adminData) {
          $comment['replier_name'] = '[ADMIN] ' . $adminData['namalengkap'];
          $comment['replier_photo'] = null;
        } else {
          $comment['replier_name'] = '[ADMIN] Unknown';
          $comment['replier_photo'] = null;
        }
      } else {
        // Ambil data user
        $userData = $db->table('user_auth')
          ->select('namalengkap, foto_profil')
          ->where('id', $replierId)
          ->get()
          ->getRowArray();

        if ($userData) {
          $comment['replier_name'] = $userData['namalengkap'];
          $comment['replier_photo'] = $userData['foto_profil'];
        } else {
          $comment['replier_name'] = 'Unknown User';
          $comment['replier_photo'] = null;
        }
      }

      $items[] = $comment;
    }

    // Ambil daftar yang sudah dilihat dari database
    $seenBuilder = $db->table('notif_seen')
      ->select('comment_id')
      ->where('user_id', $userId);
    $seenResults = $seenBuilder->get()->getResultArray();
    $seen = array_column($seenResults, 'comment_id');

    $data = [
      'title' => 'Notifikasi',
      'notifications' => $items,
      'seenIds' => $seen,
    ];

    return view('user/user_notifications', $data);
  }

  public function count()
  {
    if (!$this->request->is('get')) {
      return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
    }
    $userId = (int) session('user_id');
    if (!$userId) {
      return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
    }
    $db = \Config\Database::connect();
    $dismissedTable = $db->prefixTable('notif_dismissed');
    $seenTable = $db->prefixTable('notif_seen');
    $builder = $db->table('mading_comments child')
      ->join('mading_comments parent', 'parent.id = child.parent_id')
      ->where('parent.user_id', $userId)
      ->where('(parent.user_type IS NULL OR parent.user_type != "admin")', null, false) // Hanya komentar user, bukan admin
      ->where("NOT EXISTS (SELECT 1 FROM {$dismissedTable} nd WHERE nd.user_id = {$userId} AND nd.comment_id = child.id)", null, false)
      ->where("NOT EXISTS (SELECT 1 FROM {$seenTable} ns WHERE ns.user_id = {$userId} AND ns.comment_id = child.id)", null, false);
    $cnt = $builder->countAllResults();

    return $this->response->setJSON(['count' => (int) $cnt]);
  }

  public function seen($id = null)
  {
    if (!$this->request->is('post')) {
      return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
    }
    $userId = (int) session('user_id');
    if (!$userId) {
      return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
    }
    $notifId = (int) ($id ?? 0);
    if ($notifId <= 0) {
      return $this->response->setStatusCode(400)->setJSON(['message' => 'ID tidak valid']);
    }

    // Simpan ke database instead of session
    $db = \Config\Database::connect();
    $db->table('notif_seen')->ignore(true)->insert([
      'user_id' => $userId,
      'comment_id' => $notifId,
      'created_at' => date('Y-m-d H:i:s'),
    ]);

    return $this->response->setJSON(['success' => true]);
  }

  public function dismiss($id = null)
  {
    if (!$this->request->is('post')) {
      return $this->response->setStatusCode(405)->setJSON(['message' => 'Metode tidak diizinkan']);
    }
    $userId = (int) session('user_id');
    if (!$userId) {
      return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
    }
    $commentId = (int) ($id ?? 0);
    if ($commentId <= 0) {
      return $this->response->setStatusCode(400)->setJSON(['message' => 'ID tidak valid']);
    }
    $db = \Config\Database::connect();
    $db->table('notif_dismissed')->ignore(true)->insert([
      'user_id' => $userId,
      'comment_id' => $commentId,
      'created_at' => date('Y-m-d H:i:s'),
    ]);
    return $this->response->setJSON(['success' => true]);
  }
}
