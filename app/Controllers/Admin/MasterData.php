<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserAuthModel;
use App\Models\MadingModel;
use App\Models\LayananModel;

class MasterData extends BaseController
{
  protected $userModel;
  protected $madingModel;
  protected $layananModel;

  public function __construct()
  {
    $this->userModel = new UserAuthModel();
    $this->madingModel = new MadingModel();
    $this->layananModel = new LayananModel();
  }

  public function index()
  {
    // Get statistics with error handling
    try {
      $stats = [
        // User statistics
        'total_users' => $this->userModel->countAllResults(),
        'active_users' => $this->userModel->where('status', 'aktif')->countAllResults(),
        'pending_users' => $this->userModel->where('status', 'pending')->countAllResults(),
        'inactive_users' => $this->userModel->where('status', 'nonaktif')->countAllResults(),

        // Mading statistics
        'total_mading' => $this->madingModel->countAllResults(),
        'active_mading' => $this->madingModel->where('status', 'aktif')->countAllResults(),
        'pending_mading' => $this->madingModel->where('status', 'pending')->countAllResults(),
        'inactive_mading' => $this->madingModel->where('status', 'nonaktif')->countAllResults(),

        // Chatbot statistics (layanan informasi)
        'total_chatbot' => $this->layananModel->countAllResults(),
        'akademik_chatbot' => $this->layananModel->where('kategori', 'Akademik')->countAllResults(),
        'administrasi_chatbot' => $this->layananModel->where('kategori', 'Administrasi')->countAllResults(),
        'umum_chatbot' => $this->layananModel->where('kategori', 'Umum')->countAllResults(),
      ];
    } catch (\Exception $e) {
      // If database error, use default values
      log_message('error', 'MasterData index error: ' . $e->getMessage());
      $stats = [
        // User statistics
        'total_users' => 0,
        'active_users' => 0,
        'pending_users' => 0,
        'inactive_users' => 0,

        // Mading statistics
        'total_mading' => 0,
        'active_mading' => 0,
        'pending_mading' => 0,
        'inactive_mading' => 0,

        // Chatbot statistics
        'total_chatbot' => 0,
        'akademik_chatbot' => 0,
        'administrasi_chatbot' => 0,
        'umum_chatbot' => 0,
      ];
    }

    $data = [
      'title' => 'Master Data',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'stats' => $stats,
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/master_data', $data);
    }

    return view('admin/master_data', $data);
  }

  public function users()
  {
    // Debug logging
    log_message('debug', 'MasterData::users called - Method: ' . $this->request->getMethod() . ', AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));

    // Get search and filter parameters
    $search = $this->request->getGet('search');
    $status = $this->request->getGet('status');
    $jurusan = $this->request->getGet('jurusan');
    $page = $this->request->getGet('page') ?? 1;
    $sortBy = $this->request->getGet('sortBy') ?: 'created_at';
    $sortDir = strtolower($this->request->getGet('sortDir') ?: 'DESC');
    $allowedSort = ['id', 'npm', 'jurusan', 'status', 'created_at'];
    // Map kolom ke tabel yang benar untuk menghindari ambiguitas
    $columnMap = [
      'id' => 'user_auth.id',
      'npm' => 'user_auth.npm',
      'jurusan' => 'user_auth.jurusan',
      'status' => 'user_auth.status',
      'created_at' => 'user_auth.created_at',
    ];
    if (!in_array($sortBy, $allowedSort, true)) {
      $sortBy = 'created_at';
    }
    if (!in_array($sortDir, ['asc', 'desc'], true)) {
      $sortDir = 'DESC';
    }
    $perPage = 10;

    log_message('debug', 'Pagination params - Page: ' . $page . ', Search: ' . $search . ', Status: ' . $status . ', Jurusan: ' . $jurusan);

    // Build query for counting
    $countBuilder = $this->userModel->builder();

    if (!empty($search)) {
      $countBuilder->groupStart()
        ->like('namalengkap', $search)
        ->orLike('npm', $search)
        ->groupEnd();
    }

    if (!empty($status)) {
      $countBuilder->where('status', $status);
    }

    if (!empty($jurusan)) {
      $countBuilder->where('jurusan', $jurusan);
    }

    // Get total count for pagination
    $totalUsers = $countBuilder->countAllResults(false);

    log_message('debug', 'Total users count: ' . $totalUsers . ', Total pages: ' . ceil($totalUsers / $perPage));

    // Build query for data (separate from count query)
    $dataBuilder = $this->userModel->builder();

    if (!empty($search)) {
      $dataBuilder->groupStart()
        ->like('namalengkap', $search)
        ->orLike('npm', $search)
        ->groupEnd();
    }

    if (!empty($status)) {
      $dataBuilder->where('status', $status);
    }

    if (!empty($jurusan)) {
      $dataBuilder->where('jurusan', $jurusan);
    }

    // Get users with pagination
    $orderCol = $columnMap[$sortBy] ?? 'user_auth.created_at';
    $users = $dataBuilder->orderBy($orderCol, $sortDir)
      ->limit($perPage, ($page - 1) * $perPage)
      ->get()
      ->getResultArray();

    // Get statistics
    $stats = [
      'total' => $this->userModel->countAllResults(),
      'active' => $this->userModel->where('status', 'aktif')->countAllResults(),
      'pending' => $this->userModel->where('status', 'pending')->countAllResults(),
      'nonaktif' => $this->userModel->where('status', 'nonaktif')->countAllResults(),
    ];

    // Get unique jurusan for filter
    $jurusanList = $this->userModel->builder()
      ->select('jurusan')
      ->groupBy('jurusan')
      ->get()
      ->getResultArray();

    // Get all possible jurusan options from database schema
    $jurusanOptions = $this->getJurusanOptions();

    $data = [
      'title' => 'Users Management',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'users' => $users,
      'stats' => $stats,
      'jurusanList' => array_column($jurusanList, 'jurusan'),
      'jurusanOptions' => $jurusanOptions,
      'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $totalUsers,
        'total_pages' => ceil($totalUsers / $perPage)
      ],
      'filters' => [
        'search' => $search,
        'status' => $status,
        'jurusan' => $jurusan
      ]
    ];

    // Check if AJAX request for table updates (always return JSON for AJAX pagination)
    if ($this->request->isAJAX()) {
      log_message('debug', 'Returning AJAX JSON response for pagination');
      // Prevent caching of dynamic JSON responses
      $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
      $this->response->setHeader('Pragma', 'no-cache');

      // Return only tbody content for AJAX table updates
      $tbodyHtml = view('admin/partials/users_tbody', [
        'users' => $users
      ]);
      $paginationHtml = view('admin/partials/users_pagination', [
        'pagination' => $data['pagination'],
        'filters' => $data['filters']
      ]);

      $response = [
        'success' => true,
        'html' => $tbodyHtml,
        'pagination' => $paginationHtml,
        'stats' => $stats
      ];

      log_message('debug', 'AJAX Response: ' . json_encode($response));

      return $this->response->setJSON($response);
    }

    return view('admin/master_data_users', $data);
  }

  public function chatbot()
  {
    // Get search and filter parameters
    $search = $this->request->getGet('search');
    $kategori = $this->request->getGet('kategori');
    $page = $this->request->getGet('page') ?? 1;
    $sortBy = $this->request->getGet('sortBy') ?: 'created_at';
    $sortDir = strtolower($this->request->getGet('sortDir') ?: 'DESC');
    $allowedSort = ['id', 'judul', 'deskripsi', 'kategori', 'created_at'];
    // Map kolom ke tabel yang benar untuk menghindari ambiguitas
    $columnMap = [
      'id' => 'layanan_informasi.id',
      'judul' => 'layanan_informasi.judul',
      'deskripsi' => 'layanan_informasi.deskripsi',
      'kategori' => 'layanan_informasi.kategori',
      'created_at' => 'layanan_informasi.created_at',
    ];
    if (!in_array($sortBy, $allowedSort, true)) {
      $sortBy = 'created_at';
    }
    if (!in_array($sortDir, ['asc', 'desc'], true)) {
      $sortDir = 'DESC';
    }
    $perPage = 10;

    // Initialize LayananModel
    $layananModel = new \App\Models\LayananModel();

    // Build query for counting (exclude empty kategori)
    $countBuilder = $layananModel->builder()
      ->where('kategori IS NOT NULL AND kategori != ""', null, false);

    if (!empty($search)) {
      $countBuilder->groupStart()
        ->like('judul', $search)
        ->orLike('deskripsi', $search)
        ->groupEnd();
    }

    if (!empty($kategori)) {
      $countBuilder->where('kategori', $kategori);
    }

    // Get total count for pagination
    $totalLayanan = $countBuilder->countAllResults(false);

    // Build query for data (separate from count query, exclude empty kategori)
    $dataBuilder = $layananModel->builder()
      ->where('kategori IS NOT NULL AND kategori != ""', null, false);

    if (!empty($search)) {
      $dataBuilder->groupStart()
        ->like('judul', $search)
        ->orLike('deskripsi', $search)
        ->groupEnd();
    }

    if (!empty($kategori)) {
      $dataBuilder->where('kategori', $kategori);
    }

    // Get layanan with pagination
    $orderCol = $columnMap[$sortBy] ?? 'layanan_informasi.created_at';
    $layanan = $dataBuilder->orderBy($orderCol, $sortDir)
      ->limit($perPage, ($page - 1) * $perPage)
      ->get()
      ->getResultArray();

    // Get statistics (exclude empty kategori)
    $stats = [
      'total' => $layananModel->where('kategori IS NOT NULL AND kategori != ""', null, false)->countAllResults(),
      'akademik' => $layananModel->where('kategori', 'Akademik')->countAllResults(),
      'administrasi' => $layananModel->where('kategori', 'Administrasi')->countAllResults(),
      'umum' => $layananModel->where('kategori', 'Umum')->countAllResults(),
    ];

    // Get unique kategori for filter (exclude empty/null)
    $kategoriList = $layananModel->builder()
      ->select('kategori')
      ->where('kategori IS NOT NULL AND kategori != ""', null, false)
      ->groupBy('kategori')
      ->orderBy('kategori', 'ASC')
      ->get()
      ->getResultArray();

    $data = [
      'title' => 'Chatbot Management',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'layanan' => $layanan,
      'stats' => $stats,
      'kategoriList' => array_column($kategoriList, 'kategori'),
      'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $totalLayanan,
        'total_pages' => ceil($totalLayanan / $perPage)
      ],
      'filters' => [
        'search' => $search,
        'kategori' => $kategori,
        'sortBy' => $sortBy,
        'sortDir' => $sortDir
      ]
    ];

    // Check if AJAX request for table updates
    if ($this->request->isAJAX()) {
      // Return only tbody content for AJAX table updates
      $tbodyHtml = view('admin/partials/chatbot_tbody', [
        'layanan' => $layanan
      ]);
      $paginationHtml = view('admin/partials/chatbot_pagination', [
        'pagination' => $data['pagination'],
        'filters' => $data['filters']
      ]);

      $response = [
        'success' => true,
        'html' => $tbodyHtml,
        'pagination' => $paginationHtml,
        'stats' => $stats
      ];

      return $this->response->setJSON($response);
    }

    return view('admin/master_data_chatbot', $data);
  }

  public function mading()
  {
    // Parameter filter & sort
    $search = $this->request->getGet('search');
    $status = $this->request->getGet('status'); // pending|aktif|nonaktif
    $category = $this->request->getGet('category'); // edukasi|pengumuman|event|berita
    if (!$category) {
      // Alias untuuk kompatibilitas jika client mengirim 'jurusan'
      $category = $this->request->getGet('jurusan');
    }
    $page = max(1, (int) ($this->request->getGet('page') ?? 1));
    $perPage = 10;
    $sortBy = $this->request->getGet('sortBy') ?: 'created_at';
    $sortDir = strtolower($this->request->getGet('sortDir') ?: 'DESC');

    $allowedSort = ['id', 'judul', 'category', 'status', 'tgl_mulai', 'tgl_akhir', 'created_at'];
    $columnMap = [
      'id' => 'mading_online.id',
      'judul' => 'mading_online.judul',
      'category' => 'mading_online.category',
      'status' => 'mading_online.status',
      'tgl_mulai' => 'mading_online.tgl_mulai',
      'tgl_akhir' => 'mading_online.tgl_akhir',
      'created_at' => 'mading_online.created_at',
    ];
    if (!in_array($sortBy, $allowedSort, true)) {
      $sortBy = 'created_at';
    }
    if (!in_array($sortDir, ['asc', 'desc'], true)) {
      $sortDir = 'DESC';
    }

    // Builder untuk count
    $countBuilder = $this->madingModel->builder();
    if (!empty($search)) {
      $countBuilder->groupStart()
        ->like('judul', $search)
        ->orLike('deskripsi', $search)
        ->groupEnd();
    }
    if (!empty($status)) {
      $countBuilder->where('status', $status);
    }
    if (!empty($category)) {
      $countBuilder->where('category', $category);
    }
    $total = $countBuilder->countAllResults(false);

    // Data builder terpisah
    $dataBuilder = $this->madingModel->builder();
    if (!empty($search)) {
      $dataBuilder->groupStart()
        ->like('judul', $search)
        ->orLike('deskripsi', $search)
        ->groupEnd();
    }
    if (!empty($status)) {
      $dataBuilder->where('status', $status);
    }
    if (!empty($category)) {
      $dataBuilder->where('category', $category);
    }

    $orderCol = $columnMap[$sortBy] ?? 'mading_online.created_at';
    $rows = $dataBuilder->orderBy($orderCol, $sortDir)
      ->limit($perPage, ($page - 1) * $perPage)
      ->get()
      ->getResultArray();

    // Stats ringkas
    $stats = [
      'total' => $this->madingModel->countAllResults(),
      'aktif' => $this->madingModel->where('status', 'aktif')->countAllResults(),
      'pending' => $this->madingModel->where('status', 'pending')->countAllResults(),
      'nonaktif' => $this->madingModel->where('status', 'nonaktif')->countAllResults(),
    ];

    $data = [
      'title' => 'Mading Management',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'mading' => $rows,
      'stats' => $stats,
      'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => (int) ceil($total / $perPage)
      ],
      'filters' => [
        'search' => $search,
        'status' => $status,
        'category' => $category,
        'sortBy' => $sortBy,
        'sortDir' => $sortDir,
      ],
      'categories' => ['edukasi', 'pengumuman', 'event', 'berita'],
    ];

    // AJAX response: return tbody + pagination for SPA update
    if ($this->request->isAJAX()) {
      $tbodyHtml = view('admin/partials/mading_tbody', [
        'mading' => $rows
      ]);
      $paginationHtml = view('admin/partials/mading_pagination', [
        'pagination' => $data['pagination'],
        'filters' => $data['filters']
      ]);

      $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
      $this->response->setHeader('Pragma', 'no-cache');

      return $this->response->setJSON([
        'success' => true,
        'html' => $tbodyHtml,
        'pagination' => $paginationHtml,
        'stats' => $stats,
      ]);
    }

    return view('admin/master_data_mading', $data);
  }

  public function addMading()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $judul = $this->request->getPost('judul');
    $category = $this->request->getPost('category');
    $deskripsi = $this->request->getPost('deskripsi');
    $tgl_mulai = $this->request->getPost('tgl_mulai');
    $tgl_akhir = $this->request->getPost('tgl_akhir');
    $status = $this->request->getPost('status');

    $data = [];
    if ($judul !== null) $data['judul'] = $judul;
    if ($category !== null) $data['category'] = $category;
    if ($deskripsi !== null) $data['deskripsi'] = $deskripsi;
    if ($tgl_mulai !== null) $data['tgl_mulai'] = $tgl_mulai;
    if ($tgl_akhir !== null) $data['tgl_akhir'] = $tgl_akhir;
    if ($status !== null) $data['status'] = $status;
    $data['admin_id'] = (int) (session('admin_id') ?? 0);
    $data['views'] = 0;
    $data['created_at'] = date('Y-m-d H:i:s');

    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'required|min_length[3]|max_length[150]',
      'category' => 'required|in_list[edukasi,pengumuman,event,berita]',
      'deskripsi' => 'permit_empty|string',
      'tgl_mulai' => 'permit_empty|valid_date',
      'tgl_akhir' => 'permit_empty|valid_date',
      'status' => 'required|in_list[pending,aktif,nonaktif]',
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validasi gagal: ' . implode(', ', $validation->getErrors()),
        'csrf' => csrf_hash()
      ]);
    }

    // File opsional
    $file = $this->request->getFile('file');
    if ($file && $file->isValid() && !$file->hasMoved()) {
      $uploadDir = FCPATH . 'uploads/mading';
      if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
        if (!file_exists($uploadDir . '/index.html')) {
          @file_put_contents($uploadDir . '/index.html', "");
        }
      }
      $newName = $file->getRandomName();
      $file->move($uploadDir, $newName, true);
      $data['file'] = 'uploads/mading/' . $newName;
    }

    try {
      $this->madingModel->insert($data);
      $this->madingModel->invalidateCache();
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Mading berhasil ditambahkan',
        'csrf' => csrf_hash()
      ]);
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menambahkan mading: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  public function updateMading()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $id = (int) $this->request->getPost('mading_id');
    if (!$id) {
      return $this->response->setJSON(['success' => false, 'message' => 'ID mading tidak valid', 'csrf' => csrf_hash()]);
    }

    $judul = $this->request->getPost('judul');
    $category = $this->request->getPost('category');
    $deskripsi = $this->request->getPost('deskripsi');
    $tgl_mulai = $this->request->getPost('tgl_mulai');
    $tgl_akhir = $this->request->getPost('tgl_akhir');
    $status = $this->request->getPost('status');

    $existing = $this->madingModel->find($id);
    if (!$existing) {
      return $this->response->setJSON(['success' => false, 'message' => 'Mading tidak ditemukan', 'csrf' => csrf_hash()]);
    }

    $data = [];
    if ($judul !== null) $data['judul'] = $judul;
    if ($category !== null) $data['category'] = $category;
    if ($deskripsi !== null) $data['deskripsi'] = $deskripsi;
    if ($tgl_mulai !== null) $data['tgl_mulai'] = $tgl_mulai;
    if ($tgl_akhir !== null) $data['tgl_akhir'] = $tgl_akhir;
    if ($status !== null) $data['status'] = $status;
    $data['updated_at'] = date('Y-m-d H:i:s');

    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'permit_empty|min_length[3]|max_length[150]',
      'category' => 'permit_empty|in_list[edukasi,pengumuman,event,berita]',
      'deskripsi' => 'permit_empty|string',
      'tgl_mulai' => 'permit_empty|valid_date',
      'tgl_akhir' => 'permit_empty|valid_date',
      'status' => 'permit_empty|in_list[pending,aktif,nonaktif]',
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validasi gagal: ' . implode(', ', $validation->getErrors()),
        'csrf' => csrf_hash()
      ]);
    }

    // File baru opsional
    $file = $this->request->getFile('file');
    if ($file && $file->isValid() && !$file->hasMoved()) {
      $uploadDir = FCPATH . 'uploads/mading';
      if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
        if (!file_exists($uploadDir . '/index.html')) {
          @file_put_contents($uploadDir . '/index.html', "");
        }
      }
      $newName = $file->getRandomName();
      $file->move($uploadDir, $newName, true);
      $data['file'] = 'uploads/mading/' . $newName;
    }

    try {
      $this->madingModel->update($id, $data);
      $this->madingModel->invalidateCache($id);
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Mading berhasil diperbarui',
        'csrf' => csrf_hash()
      ]);
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal memperbarui mading: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  public function deleteMading()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $id = (int) $this->request->getPost('mading_id');
    if (!$id) {
      return $this->response->setJSON(['success' => false, 'message' => 'ID mading tidak valid', 'csrf' => csrf_hash()]);
    }

    try {
      $this->madingModel->delete($id);
      $this->madingModel->invalidateCache($id);
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Mading berhasil dihapus',
        'csrf' => csrf_hash()
      ]);
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menghapus mading: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  public function getMading()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }
    $id = (int) $this->request->getGet('id');
    if (!$id) {
      return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid']);
    }
    $row = $this->madingModel->find($id);
    if (!$row) {
      return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    return $this->response->setJSON(['success' => true, 'data' => $row, 'csrf' => csrf_hash()]);
  }

  // AJAX Methods for Users Management
  public function updateUserStatus()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $userId = $this->request->getPost('user_id');
    $status = $this->request->getPost('status');

    if (!$userId || !in_array($status, ['aktif', 'nonaktif', 'pending'])) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters', 'csrf' => csrf_hash()]);
    }

    try {
      $this->userModel->update($userId, ['status' => $status]);
      return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully', 'csrf' => csrf_hash()]);
    } catch (\Exception $e) {
      return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status', 'csrf' => csrf_hash()]);
    }
  }

  public function updateUser()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $userId = $this->request->getPost('user_id');
    $namaLengkap = $this->request->getPost('nama_lengkap');
    $npm = $this->request->getPost('npm');
    $jurusan = $this->request->getPost('jurusan');
    $status = $this->request->getPost('status');
    $password = $this->request->getPost('password');

    if (!$userId) {
      return $this->response->setJSON(['success' => false, 'message' => 'User ID required', 'csrf' => csrf_hash()]);
    }

    $data = [];
    if ($namaLengkap !== null) $data['namalengkap'] = $namaLengkap;
    if ($npm !== null) $data['npm'] = $npm;
    if ($jurusan !== null) $data['jurusan'] = $jurusan;
    if ($status !== null) $data['status'] = $status;
    if ($password !== null && $password !== '') {
      // Biarkan Model callbacks (beforeUpdate) yang melakukan hash
      $data['password'] = $password;
    }
    $data['updated_at'] = date('Y-m-d H:i:s');

    $validation = \Config\Services::validation();
    $validation->setRules([
      'namalengkap' => 'permit_empty|min_length[3]|max_length[100]',
      'npm' => 'permit_empty|min_length[8]|max_length[20]',
      'jurusan' => 'permit_empty|in_list[Komputerisasi Akuntansi,Manajemen Informatika,Sistem Informasi,Teknik Informatika,Sistem Komputer,Hukum,Administrasi Publik,Kewirausahaan]',
      'status' => 'permit_empty|in_list[pending,aktif,nonaktif]',
      'password' => 'permit_empty|min_length[6]'
    ]);

    if (!$validation->run([
      'namalengkap' => $namaLengkap,
      'npm' => $npm,
      'jurusan' => $jurusan,
      'status' => $status,
      'password' => $password,
    ])) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors()),
        'csrf' => csrf_hash()
      ]);
    }

    try {
      if ($npm !== null && $npm !== '') {
        $exists = $this->userModel->where('npm', $npm)->where('id !=', $userId)->first();
        if ($exists) {
          return $this->response->setJSON([
            'success' => false,
            'message' => 'NPM sudah digunakan user lain',
            'csrf' => csrf_hash()
          ]);
        }
      }

      // Gunakan validasi manual di atas; lewati validation bawaan Model yang mewajibkan password
      $this->userModel->skipValidation(true);
      $result = $this->userModel->update($userId, $data);
      if ($result === false) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal memperbarui user: ' . implode(', ', (array) $this->userModel->errors()),
          'csrf' => csrf_hash()
        ]);
      }

      return $this->response->setJSON(['success' => true, 'message' => 'User berhasil diperbarui', 'csrf' => csrf_hash()]);
    } catch (\Exception $e) {
      return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui user', 'csrf' => csrf_hash()]);
    }
  }

  public function deleteUser()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $userId = $this->request->getPost('user_id');

    if (!$userId) {
      return $this->response->setJSON(['success' => false, 'message' => 'User ID required', 'csrf' => csrf_hash()]);
    }

    try {
      $this->userModel->delete($userId);
      return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully', 'csrf' => csrf_hash()]);
    } catch (\Exception $e) {
      return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user', 'csrf' => csrf_hash()]);
    }
  }

  /**
   * Add new user
   */
  public function addUser()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // Debug CSRF token
    $receivedToken = $this->request->getPost('csrf_test_name');
    $expectedToken = csrf_hash();
    $allPostData = $this->request->getPost();
    $rawInput = $this->request->getBody();
    $allHeaders = $this->request->getHeaders();

    log_message('debug', 'CSRF Debug - Received: ' . $receivedToken . ', Expected: ' . $expectedToken);
    log_message('debug', 'All POST data: ' . json_encode($allPostData));
    log_message('debug', 'Raw input: ' . $rawInput);
    log_message('debug', 'Request method: ' . $this->request->getMethod());
    log_message('debug', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
    log_message('debug', 'All headers: ' . json_encode($allHeaders));

    // Try to get CSRF token from different sources
    $csrfFromPost = $this->request->getPost('csrf_test_name');
    $csrfFromHeader = $this->request->getHeaderLine('X-CSRF-TOKEN');
    $csrfFromCookie = $this->request->getCookie('csrf_cookie_name');

    log_message('debug', 'CSRF from POST: ' . $csrfFromPost);
    log_message('debug', 'CSRF from Header: ' . $csrfFromHeader);
    log_message('debug', 'CSRF from Cookie: ' . $csrfFromCookie);

    // Validate CSRF token
    if (!$receivedToken || !hash_equals($expectedToken, $receivedToken)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid CSRF token - Received: ' . $receivedToken . ', Expected: ' . $expectedToken,
        'debug' => [
          'received_token' => $receivedToken,
          'expected_token' => $expectedToken,
          'all_post_data' => $allPostData,
          'raw_input' => $rawInput,
          'request_method' => $this->request->getMethod(),
          'content_type' => $this->request->getHeaderLine('Content-Type'),
          'csrf_from_post' => $csrfFromPost,
          'csrf_from_header' => $csrfFromHeader,
          'csrf_from_cookie' => $csrfFromCookie,
          'all_headers' => $allHeaders
        ]
      ]);
    }

    // Get form data
    $data = [
      'namalengkap' => $this->request->getPost('nama_lengkap'),
      'npm' => $this->request->getPost('npm'),
      'jurusan' => $this->request->getPost('jurusan'),
      'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
      'status' => $this->request->getPost('status'),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'namalengkap' => 'required|min_length[3]|max_length[100]',
      'npm' => 'required|min_length[8]|max_length[20]|is_unique[user_auth.npm]',
      'jurusan' => 'required|in_list[Komputerisasi Akuntansi,Manajemen Informatika,Sistem Informasi,Teknik Informatika,Sistem Komputer,Hukum,Administrasi Publik,Kewirausahaan]',
      'password' => 'required|min_length[6]',
      'status' => 'required|in_list[pending,aktif,nonaktif]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
      ]);
    }

    try {
      // Insert user data
      $result = $this->userModel->insert($data);

      if ($result) {
        return $this->response->setJSON([
          'success' => true,
          'message' => 'User berhasil ditambahkan!',
          'user_id' => $result,
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menambahkan user',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Add user error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan user: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Test method to bypass CSRF validation and actually save data
   */
  public function addUserTest()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // Debug all data without CSRF validation
    $allPostData = $this->request->getPost();
    log_message('debug', 'TEST - All POST data: ' . json_encode($allPostData));

    // Get form data
    $data = [
      'namalengkap' => $this->request->getPost('nama_lengkap'),
      'npm' => $this->request->getPost('npm'),
      'jurusan' => $this->request->getPost('jurusan'),
      'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
      'status' => $this->request->getPost('status'),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'namalengkap' => 'required|min_length[3]|max_length[100]',
      'npm' => 'required|min_length[8]|max_length[20]|is_unique[user_auth.npm]',
      'jurusan' => 'required|in_list[Komputerisasi Akuntansi,Manajemen Informatika,Sistem Informasi,Teknik Informatika,Sistem Komputer,Hukum,Administrasi Publik,Kewirausahaan]',
      'password' => 'required|min_length[6]',
      'status' => 'required|in_list[pending,aktif,nonaktif]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors()),
        'csrf' => csrf_hash()
      ]);
    }

    try {
      // Insert user data
      $result = $this->userModel->insert($data);

      if ($result) {
        return $this->response->setJSON([
          'success' => true,
          'message' => 'User berhasil ditambahkan!',
          'user_id' => $result,
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menambahkan user',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Add user test error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan user: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Add new layanan (chatbot Q&A)
   */
  public function addLayanan()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // CSRF validation
    $receivedToken = $this->request->getPost('csrf_test_name');
    $expectedToken = csrf_hash();

    if (!$receivedToken || !hash_equals($expectedToken, $receivedToken)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid CSRF token'
      ]);
    }

    // Get form data
    $data = [
      'judul' => $this->request->getPost('judul'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'kategori' => $this->request->getPost('kategori'),
      'created_at' => date('Y-m-d H:i:s')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'required|min_length[3]|max_length[255]',
      'deskripsi' => 'required|min_length[10]',
      'kategori' => 'required|in_list[Akademik,Administrasi,Umum]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Insert layanan data
      $result = $layananModel->insert($data);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil ditambahkan!',
          'layanan_id' => $result,
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menambahkan layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Add layanan error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Update layanan (chatbot Q&A)
   */
  public function updateLayanan()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // CSRF validation
    $receivedToken = $this->request->getPost('csrf_test_name');
    $expectedToken = csrf_hash();

    if (!$receivedToken || !hash_equals($expectedToken, $receivedToken)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid CSRF token'
      ]);
    }

    $layananId = $this->request->getPost('layanan_id');

    if (!$layananId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Layanan ID required'
      ]);
    }

    // Get form data
    $data = [
      'judul' => $this->request->getPost('judul'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'kategori' => $this->request->getPost('kategori')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'required|min_length[3]|max_length[255]',
      'deskripsi' => 'required|min_length[10]',
      'kategori' => 'required|in_list[Akademik,Administrasi,Umum]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Update layanan data
      $result = $layananModel->update($layananId, $data);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil diperbarui!',
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal memperbarui layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Update layanan error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memperbarui layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Delete layanan (chatbot Q&A)
   */
  public function deleteLayanan()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // CSRF validation
    $receivedToken = $this->request->getPost('csrf_test_name');
    $expectedToken = csrf_hash();

    if (!$receivedToken || !hash_equals($expectedToken, $receivedToken)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid CSRF token'
      ]);
    }

    $layananId = $this->request->getPost('layanan_id');

    if (!$layananId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Layanan ID required'
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Delete layanan data
      $result = $layananModel->delete($layananId);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil dihapus!',
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menghapus layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Delete layanan error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Get layanan by ID for editing
   */
  public function getLayanan()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    $layananId = $this->request->getGet('id');

    if (!$layananId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Layanan ID required'
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Get layanan data
      $layanan = $layananModel->find($layananId);

      if ($layanan) {
        return $this->response->setJSON([
          'success' => true,
          'data' => $layanan
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Layanan tidak ditemukan'
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Get layanan error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat mengambil data layanan: ' . $e->getMessage()
      ]);
    }
  }


  /**
   * Test method to bypass CSRF validation for delete layanan
   */
  public function deleteLayananTest()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // Debug all data without CSRF validation
    $allPostData = $this->request->getPost();
    log_message('debug', 'TEST DELETE LAYANAN - All POST data: ' . json_encode($allPostData));

    $layananId = $this->request->getPost('layanan_id');

    if (!$layananId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Layanan ID required'
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Delete layanan data
      $result = $layananModel->delete($layananId);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil dihapus!',
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menghapus layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Delete layanan test error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Test method to bypass CSRF validation for update layanan
   */
  public function updateLayananTest()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // Debug all data without CSRF validation
    $allPostData = $this->request->getPost();
    log_message('debug', 'TEST UPDATE LAYANAN - All POST data: ' . json_encode($allPostData));

    $layananId = $this->request->getPost('layanan_id');

    if (!$layananId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Layanan ID required'
      ]);
    }

    // Get form data
    $data = [
      'judul' => $this->request->getPost('judul'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'kategori' => $this->request->getPost('kategori')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'required|min_length[3]|max_length[255]',
      'deskripsi' => 'required|min_length[10]',
      'kategori' => 'required|in_list[Akademik,Administrasi,Umum]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Update layanan data
      $result = $layananModel->update($layananId, $data);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil diperbarui!',
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal memperbarui layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Update layanan test error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memperbarui layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Test method to bypass CSRF validation for layanan (same as addUserTest)
   */
  public function addLayananTest()
  {
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    // Debug all data without CSRF validation
    $allPostData = $this->request->getPost();
    log_message('debug', 'TEST LAYANAN - All POST data: ' . json_encode($allPostData));

    // Get form data
    $data = [
      'judul' => $this->request->getPost('judul'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'kategori' => $this->request->getPost('kategori'),
      'created_at' => date('Y-m-d H:i:s')
    ];

    // Validate required fields
    $validation = \Config\Services::validation();
    $validation->setRules([
      'judul' => 'required|min_length[3]|max_length[255]',
      'deskripsi' => 'required|min_length[10]',
      'kategori' => 'required|in_list[Akademik,Administrasi,Umum]'
    ]);

    if (!$validation->run($data)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
      ]);
    }

    try {
      // Initialize LayananModel
      $layananModel = new \App\Models\LayananModel();

      // Insert layanan data
      $result = $layananModel->insert($data);

      if ($result) {
        // Invalidate cache
        $layananModel->invalidateCache();

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Layanan berhasil ditambahkan!',
          'layanan_id' => $result,
          'csrf' => csrf_hash()
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menambahkan layanan',
          'csrf' => csrf_hash()
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Add layanan test error: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan layanan: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }

  /**
   * Get jurusan options from database schema
   */
  private function getJurusanOptions()
  {
    return [
      'Komputerisasi Akuntansi' => 'Komputerisasi Akuntansi',
      'Manajemen Informatika' => 'Manajemen Informatika',
      'Sistem Informasi' => 'Sistem Informasi',
      'Teknik Informatika' => 'Teknik Informatika',
      'Sistem Komputer' => 'Sistem Komputer',
      'Hukum' => 'Hukum',
      'Administrasi Publik' => 'Administrasi Publik',
      'Kewirausahaan' => 'Kewirausahaan'
    ];
  }

  /**
   * Update mading status
   */
  public function updateMadingStatus()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['success' => false, 'message' => 'Invalid request', 'csrf' => csrf_hash()]);
    }

    $madingId = $this->request->getPost('mading_id');
    $status = $this->request->getPost('status');

    if (!$madingId || !$status) {
      return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap', 'csrf' => csrf_hash()]);
    }

    $madingModel = new \App\Models\MadingModel();

    try {
      $madingModel->set('status', $status)
        ->where('id', $madingId)
        ->update();

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Status mading berhasil diubah',
        'csrf' => csrf_hash()
      ]);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal mengubah status: ' . $e->getMessage(),
        'csrf' => csrf_hash()
      ]);
    }
  }
}
