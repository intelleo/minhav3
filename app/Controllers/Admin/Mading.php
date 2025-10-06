<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Mading extends BaseController
{
  public function index()
  {
    $data = [
      'title' => 'E-mading Management',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
    ];

    return view('admin/mading_management', $data);
  }

  public function listHtml()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $page = (int) ($this->request->getGet('page') ?? 1);
    $perPage = (int) ($this->request->getGet('perPage') ?? 6);
    $offset = max(0, ($page - 1) * $perPage);

    $madingModel = new \App\Models\MadingModel();

    // Ambil data mading dengan query yang sama seperti user
    $today = date('Y-m-d');
    $builder = $madingModel->db->table('mading_online')
      ->select('mading_online.*, auth_admin.username')
      ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
      ->where('mading_online.status', 'aktif')
      ->where('mading_online.tgl_akhir >=', $today)
      ->orderBy('mading_online.created_at', 'DESC');

    $all = $builder->get()->getResultArray();
    $slice = array_slice($all, $offset, $perPage);

    // Enrich data seperti di user
    $slice = $madingModel->enrichMadingDataPublic($slice);

    // Render item HTML menggunakan partial admin
    $html = '';
    foreach ($slice as $mading) {
      $html .= view('admin/partials/mading_card_item', ['mading' => $mading]);
    }

    return $this->response->setJSON([
      'success' => true,
      'html' => $html,
      'page' => $page,
    ]);
  }

  public function create()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $rules = [
      'judul'      => 'required|string|min_length[3]|max_length[150]',
      'category'   => 'required|in_list[edukasi,pengumuman,event,berita]',
      'deskripsi'  => 'permit_empty|string',
      'file'       => 'permit_empty|uploaded[file]|is_image[file]|max_size[file,2048]|mime_in[file,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
      'tgl_mulai'  => 'permit_empty|valid_date',
      'tgl_akhir'  => 'permit_empty|valid_date',
      'status'     => 'required|in_list[pending,aktif,nonaktif]',
    ];

    // Catatan: untuk file opsional, gunakan dua aturan alternatif: permit_empty OR uploaded
    // CodeIgniter tidak mendukung OR langsung, jadi kita validasi manual untuk file ketika ada

    $validation = \Config\Services::validation();
    $validation->setRules($rules);

    // Jika tidak ada file yang diupload, hapus aturan uploaded agar permit_empty berlaku
    $file = $this->request->getFile('file');
    if (!$file || !$file->isValid()) {
      $validation->setRule('file', 'file', 'permit_empty');
    }

    if (!$validation->withRequest($this->request)->run()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors'  => $validation->getErrors(),
      ]);
    }

    $judul     = trim((string) $this->request->getPost('judul'));
    $category  = (string) $this->request->getPost('category');
    $deskripsi = (string) $this->request->getPost('deskripsi');
    $tglMulai  = (string) $this->request->getPost('tgl_mulai');
    $tglAkhir  = (string) $this->request->getPost('tgl_akhir');
    $status    = (string) $this->request->getPost('status');

    // Validasi bisnis: tgl_akhir >= tgl_mulai jika keduanya ada
    if ($tglMulai && $tglAkhir && (strtotime($tglAkhir) < strtotime($tglMulai))) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
        'errors'  => ['tgl_akhir' => 'Tanggal akhir < tanggal mulai'],
      ]);
    }

    $relativePath = null;
    if ($file && $file->isValid() && !$file->hasMoved()) {
      $uploadDir = FCPATH . 'uploads/mading';
      if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
        // Tambahkan index.html untuk keamanan sederhana
        if (!file_exists($uploadDir . '/index.html')) {
          @file_put_contents($uploadDir . '/index.html', "");
        }
      }
      $newName = $file->getRandomName();
      try {
        $file->move($uploadDir, $newName, true);
        $relativePath = 'uploads/mading/' . $newName;
      } catch (\Throwable $e) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menyimpan file: ' . $e->getMessage(),
        ]);
      }
    }

    $adminId = (int) (session('admin_id') ?? 0);

    $madingModel = new \App\Models\MadingModel();

    $data = [
      'judul'     => $judul,
      'category'  => $category,
      'deskripsi' => $deskripsi ?: null,
      'file'      => $relativePath,
      'tgl_mulai' => $tglMulai ?: null,
      'tgl_akhir' => $tglAkhir ?: null,
      'status'    => $status,
      'admin_id'  => $adminId ?: null,
      'views'     => 0,
    ];

    try {
      $id = $madingModel->insert($data, true);
      $madingModel->invalidateCache($id);
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
      ]);
    }

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Mading berhasil ditambahkan',
      'id'      => $id,
    ]);
  }

  public function detail($id)
  {
    $madingModel = new \App\Models\MadingModel();
    $commentModel = new \App\Models\MadingCommentModel();

    // Ambil data mading dengan query langsung seperti di user
    $today = date('Y-m-d');
    $mading = $madingModel->select('mading_online.*, auth_admin.username')
      ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
      ->where('mading_online.id', $id)
      ->where('mading_online.status', 'aktif')
      ->where('mading_online.tgl_akhir >=', $today)
      ->first();

    if (!$mading) {
      return redirect()->to('Admin/Mading')->with('error', 'Mading tidak ditemukan.');
    }

    // Enrich data seperti di user
    $mading = $madingModel->enrichMadingDataPublic([$mading])[0];

    // Tambah view
    $madingModel->set('views', 'views + 1', false)
      ->where('id', $id)
      ->update();

    $data = [
      'mading' => $mading,
      'title' => 'Detail Mading - ' . $mading['judul'],
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
    ];

    // Ambil semua komentar untuk nested threading
    $allComments = $commentModel->getAllByMadingWithUser((int)$id);

    // Pisahkan komentar utama dan balasan
    $mainComments = [];
    $replies = [];

    foreach ($allComments as $comment) {
      if ($comment['parent_id'] === null) {
        $comment['replies'] = [];
        $mainComments[] = $comment;
      } else {
        $replies[] = $comment;
      }
    }

    // Urutkan komentar utama: terbaru di atas
    usort($mainComments, function ($a, $b) {
      return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });

    // Urutkan balasan: tertua dulu (kronologis)
    usort($replies, function ($a, $b) {
      return strtotime($a['created_at']) <=> strtotime($b['created_at']);
    });

    // Buat struktur nested dengan rekursi
    $data['commentTree'] = $this->buildNestedComments($mainComments, $replies);

    return view('admin/mading_detail', $data);
  }

  /**
   * Membangun struktur komentar nested secara rekursif
   */
  private function buildNestedComments(array $mainComments, array $replies): array
  {
    // Kelompokkan balasan berdasarkan parent_id
    $repliesByParent = [];
    foreach ($replies as $reply) {
      $parentId = $reply['parent_id'];
      if (!isset($repliesByParent[$parentId])) {
        $repliesByParent[$parentId] = [];
      }
      $repliesByParent[$parentId][] = $reply;
    }

    // Fungsi rekursif untuk membangun nested structure
    $buildReplies = function ($parentId) use (&$buildReplies, $repliesByParent) {
      if (!isset($repliesByParent[$parentId])) {
        return [];
      }

      $children = [];
      foreach ($repliesByParent[$parentId] as $reply) {
        $reply['replies'] = $buildReplies($reply['id']);
        $children[] = $reply;
      }

      return $children;
    };

    // Gabungkan balasan ke komentar utama
    foreach ($mainComments as &$mainComment) {
      $mainComment['replies'] = $buildReplies($mainComment['id']);
    }
    unset($mainComment);

    return $mainComments;
  }

  public function addComment()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }


    $rules = [
      'mading_id'     => 'required|numeric',
      'isi_komentar'  => 'required|min_length[1]|max_length[200]',
      'parent_id'     => 'permit_empty|numeric'
    ];

    if (!$this->validate($rules)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Komentar minimal 1 karakter dan maksimal 200.',
        'csrf'    => csrf_hash(),
      ])->setStatusCode(422);
    }

    $madingId = $this->request->getPost('mading_id');
    $parentId = $this->request->getPost('parent_id') ?: null;
    $isi_komentar = trim($this->request->getPost('isi_komentar'));

    // Validasi nested threading
    if ($parentId) {
      $commentModel = new \App\Models\MadingCommentModel();
      $parentComment = $commentModel->where('id', $parentId)->first();

      if (!$parentComment) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Komentar yang dibalas tidak ditemukan.',
          'csrf'    => csrf_hash(),
        ])->setStatusCode(404);
      }

      if ($parentComment['mading_id'] != $madingId) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Komentar tidak valid.',
          'csrf'    => csrf_hash(),
        ])->setStatusCode(422);
      }
    }

    // Validasi mading ada
    $madingModel = new \App\Models\MadingModel();
    $mading = $madingModel->find($madingId);

    if (!$mading) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Mading tidak ditemukan.',
        'csrf'    => csrf_hash(),
      ])->setStatusCode(404);
    }

    $data = [
      'mading_id'     => $madingId,
      'user_id'       => session('admin_id'), // Admin berkomentar sebagai admin
      'user_type'     => 'admin', // Penting: tandai sebagai komentar admin
      'parent_id'     => $parentId,
      'isi_komentar'  => $isi_komentar,
    ];

    $commentModel = new \App\Models\MadingCommentModel();

    if ($commentModel->insert($data)) {
      $totalComments = $commentModel->where('mading_id', $madingId)->countAllResults();
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Komentar berhasil ditambahkan.',
        'totalComments' => $totalComments,
        'csrf'    => csrf_hash(),
      ]);
    }

    return $this->response->setJSON([
      'success' => false,
      'message' => 'Gagal menyimpan komentar.',
      'csrf'    => csrf_hash(),
    ])->setStatusCode(500);
  }

  public function toggleLike()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }


    $madingId = $this->request->getPost('mading_id');
    $adminId = session('admin_id');

    if (!is_numeric($madingId)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'ID mading tidak valid.'
      ])->setStatusCode(400);
    }

    $likeModel = new \App\Models\MadingLikeModel();

    // Cek apakah admin sudah like
    $isLikedRecord = $likeModel->where('mading_id', $madingId)
      ->where('user_id', $adminId)
      ->first();

    if ($isLikedRecord) {
      // Unlike
      $db = \Config\Database::connect();
      $db->table('mading_likes')
        ->where('mading_id', $madingId)
        ->where('user_id', $adminId)
        ->delete();
      $liked = false;
    } else {
      // Like
      $likeModel->insert([
        'mading_id' => $madingId,
        'user_id'   => $adminId
      ]);
      $liked = true;
    }

    // Hitung total like dengan reset query builder
    $totalLikes = $likeModel->resetQuery()->where('mading_id', $madingId)->countAllResults();

    // Invalidate cache untuk memastikan data ter-update
    $madingModel = new \App\Models\MadingModel();
    $madingModel->invalidateCache($madingId);

    return $this->response->setJSON([
      'success'     => true,
      'liked'       => $liked,
      'total_likes' => $totalLikes,
      'csrf'        => csrf_hash()
    ]);
  }

  public function loadComments($madingId)
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $page = max(1, (int)$this->request->getGet('page'));
    $perPage = min(20, max(5, (int)$this->request->getGet('perPage') ?: 10));
    $offset = ($page - 1) * $perPage;

    $commentModel = new \App\Models\MadingCommentModel();
    $all = $commentModel->getAllByMadingWithUser((int)$madingId);

    // Pisahkan komentar utama dan balasan
    $mainComments = [];
    $replies = [];

    foreach ($all as $comment) {
      if ($comment['parent_id'] === null) {
        $comment['replies'] = [];
        $mainComments[] = $comment;
      } else {
        $replies[] = $comment;
      }
    }

    // Urutkan komentar utama: terbaru di atas
    usort($mainComments, function ($a, $b) {
      return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });

    // Urutkan balasan: tertua dulu
    usort($replies, function ($a, $b) {
      return strtotime($a['created_at']) <=> strtotime($b['created_at']);
    });

    // Buat struktur nested dengan rekursi
    $roots = $this->buildNestedComments($mainComments, $replies);

    $totalRoots = count($roots);
    $totalComments = count($all);
    $slice = array_slice($roots, $offset, $perPage);

    return $this->response->setJSON([
      'success' => true,
      'page' => $page,
      'perPage' => $perPage,
      'totalRoots' => $totalRoots,
      'totalComments' => $totalComments,
      'hasMore' => ($offset + $perPage) < $totalRoots,
      'items' => $slice,
      'csrf' => csrf_hash(),
    ]);
  }
}
