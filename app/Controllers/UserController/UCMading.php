<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MadingModel;
use App\Models\MadingCommentModel;
use App\Models\UserAuthModel;

class UCMading extends BaseController

{
    private $madingModel;
    public function __construct()
    {
        $this->madingModel = new \App\Models\MadingModel();
    }

    /**
     * Membangun struktur komentar nested secara rekursif
     * @param array $mainComments Komentar utama
     * @param array $replies Semua balasan
     * @return array Komentar dengan struktur nested
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

    /**
     * Soft delete satu komentar dan semua keturunannya (balasan rekursif)
     * @return array{0: int[], 1: ?string} [daftar ID terhapus, pesan error]
     */
    private function softDeleteCommentThread(int $rootCommentId): array
    {
        $commentModel = new MadingCommentModel();
        $db = \Config\Database::connect();
        $deletedIds = [];

        $db->transStart();
        try {
            // Kumpulkan semua id komentar (root + children rekursif)
            $toVisit = [$rootCommentId];
            while (!empty($toVisit)) {
                $currentId = array_pop($toVisit);
                $comment = $commentModel->find($currentId);
                if (!$comment) {
                    continue;
                }
                // Tambahkan anak-anaknya
                $children = $commentModel->where('parent_id', $currentId)->findAll();
                foreach ($children as $child) {
                    $toVisit[] = (int)$child['id'];
                }
                // Soft delete current
                $commentModel->update($currentId, ['deleted_at' => date('Y-m-d H:i:s')]);
                $deletedIds[] = $currentId;
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                return [[], 'Gagal menghapus komentar'];
            }
            return [$deletedIds, null];
        } catch (\Throwable $e) {
            $db->transRollback();
            return [[], 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    public function indexMading()
    {
        $data['title'] = 'Mading Online';
        $data['madingList'] = $this->madingModel->getAllWithAdmin();
        return view('user/user_mading', $data);
    }

    // listMading() dihapus karena frontend kini menggunakan endpoint list-html

    /**
     * Versi HTML partial: kembalikan potongan kartu mading memakai helper PHP (uimading_helper)
     * GET /Mading/list-html?page=1&perPage=6
     */
    public function listMadingHtml()
    {
        // Jika ini permintaan HTML (navigasi langsung), arahkan ke halaman Mading
        $acceptHeader = (string) $this->request->getHeaderLine('Accept');
        $isHtmlNavigation = stripos($acceptHeader, 'text/html') !== false && !$this->request->isAJAX();
        if ($isHtmlNavigation) {
            return redirect()->to('/Mading');
        }
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(24, max(3, (int)$this->request->getGet('perPage') ?: 6));
        $offset = ($page - 1) * $perPage;

        $today = date('Y-m-d');
        $builder = $this->madingModel->db->table('mading_online')
            ->select('mading_online.*, auth_admin.username')
            ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
            ->where('mading_online.status', 'aktif')
            ->where('mading_online.tgl_akhir >=', $today)
            ->orderBy('mading_online.created_at', 'DESC');

        $total = $builder->countAllResults(false);
        $rows = $builder->get($perPage, $offset)->getResultArray();
        $rows = (new \App\Models\MadingModel())->enrichMadingDataPublic($rows);

        // Render setiap item memakai view helper yang sama dengan detail (badge, border, dll)
        $html = '';
        foreach ($rows as $mading) {
            $html .= view('user/partials/mading_card_item', ['mading' => $mading]);
        }

        return $this->response->setJSON([
            'success' => true,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'hasMore' => ($offset + $perPage) < $total,
            'html' => $html,
            'csrf' => csrf_hash(),
        ]);
    }

    public function detail($id)
    {
        $madingModel = new MadingModel();
        $commentModel = new MadingCommentModel();

        $mading = $madingModel->getWithAdmin($id);

        if (!$mading) {
            return redirect()->to('Mading')->with('error', 'Mading tidak ditemukan.');
        }
        // Tambah view
        $madingModel->set('views', 'views + 1', false) // false = jangan escape
            ->where('id', $id)
            ->update();


        // Cek apakah mading masih aktif & belum kadaluarsa
        $today = date('Y-m-d');
        if ($mading['status'] !== 'aktif' || $mading['tgl_akhir'] < $today) {
            return redirect()->to('Mading')->with('info', 'Mading ini sudah tidak aktif.');
        }

        $data['mading'] = $mading;
        $data['title'] = 'mading online';

        // Ambil semua komentar untuk nested threading
        $allComments = $commentModel->getAllByMadingWithUser((int)$id);

        // Pisahkan komentar utama dan balasan
        $mainComments = [];
        $replies = [];

        foreach ($allComments as $comment) {
            if ($comment['parent_id'] === null) {
                // Komentar utama
                $comment['replies'] = [];
                $mainComments[] = $comment;
            } else {
                // Balasan - bisa mengacu ke komentar atau balasan lain
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

        return view('user/user_mading_detail', $data);
    }

    /**
     * Memuat komentar root secara paginasi untuk lazy load/infinite scroll
     * GET /Mading/comments/{madingId}?page=1&perPage=10
     */
    public function loadComments($madingId)
    {
        // Jika ini permintaan HTML (navigasi langsung), arahkan ke halaman detail mading
        $acceptHeader = (string) $this->request->getHeaderLine('Accept');
        $isHtmlNavigation = stripos($acceptHeader, 'text/html') !== false && !$this->request->isAJAX();
        if ($isHtmlNavigation) {
            return redirect()->to('/Mading/detail/' . $madingId);
        }

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(20, max(5, (int)$this->request->getGet('perPage') ?: 10));
        $offset = ($page - 1) * $perPage;

        $commentModel = new MadingCommentModel();
        // Ambil seluruh komentar untuk nested threading
        $all = $commentModel->getAllByMadingWithUser((int)$madingId);

        // Pisahkan komentar utama dan balasan (nested threading)
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

    public function addComment()
    {
        if (!session('logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Harap login untuk berkomentar.',
                    'csrf'    => csrf_hash(),
                ])->setStatusCode(403);
            }
            return redirect()->to('/login')->with('error', 'Harap login untuk berkomentar.');
        }

        $rules = [
            'mading_id'     => 'required|numeric',
            'isi_komentar'  => 'required|min_length[1]|max_length[200]',
            'parent_id'     => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Komentar minimal 1 karakter dan maksimal 200.',
                    'csrf'    => csrf_hash(),
                ])->setStatusCode(422);
            }
            return redirect()->back()->withInput()->with('error', 'Komentar minimal 3 karakter.');
        }

        $madingId = $this->request->getPost('mading_id');
        $parentId = $this->request->getPost('parent_id') ?: null;

        // ✅ BARIS INI YANG HILANG!
        $isi_komentar = trim($this->request->getPost('isi_komentar'));

        // Validasi nested threading: balasan bisa mengacu ke komentar atau balasan lain
        if ($parentId) {
            $commentModel = new MadingCommentModel();
            $parentComment = $commentModel->find($parentId);

            if (!$parentComment) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Komentar yang dibalas tidak ditemukan.',
                        'csrf'    => csrf_hash(),
                    ])->setStatusCode(404);
                }
                return redirect()->back()->with('error', 'Komentar yang dibalas tidak ditemukan.');
            }

            // Pastikan komentar yang dibalas adalah dari mading yang sama
            if ($parentComment['mading_id'] != $madingId) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Komentar tidak valid.',
                        'csrf'    => csrf_hash(),
                    ])->setStatusCode(422);
                }
                return redirect()->back()->with('error', 'Komentar tidak valid.');
            }

            // Nested threading: biarkan parent_id tetap mengacu ke komentar yang dibalas
            // Tidak ada perubahan parent_id, biarkan user membalas komentar atau balasan apapun
        }

        // Validasi mading ada & aktif
        $madingModel = new \App\Models\MadingModel();
        $mading = $madingModel->find($madingId);

        if (!$mading) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Mading tidak ditemukan.',
                    'csrf'    => csrf_hash(),
                ])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Mading tidak ditemukan.');
        }

        $today = date('Y-m-d');
        if ($mading['status'] !== 'aktif' || $mading['tgl_akhir'] < $today) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Mading ini sudah tidak aktif.',
                    'csrf'    => csrf_hash(),
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('error', 'Mading ini sudah tidak aktif.');
        }

        $data = [
            'mading_id'     => $madingId,
            'user_id'       => session('user_id'),
            'parent_id'     => $parentId,
            'isi_komentar'  => $isi_komentar, // ✅ Sekarang variabel sudah ada
        ];

        $commentModel = new \App\Models\MadingCommentModel();

        if ($commentModel->insert($data)) {
            $totalComments = $commentModel->where('mading_id', $madingId)->countAllResults();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Komentar berhasil ditambahkan.',
                    'totalComments' => $totalComments,
                    'csrf'    => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan komentar.',
                'csrf'    => csrf_hash(),
            ])->setStatusCode(500);
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan komentar.');
    }

    // likes
    public function toggleLike()
    {
        if (!session()->has('logged_in')) {
            log_message('error', 'Session logged_in tidak ada');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi login hilang. Silakan login ulang.'
            ])->setStatusCode(403);
        }
        $madingId = $this->request->getPost('mading_id');
        $userId = session('user_id'); // Aman, karena sudah login

        // Validasi input
        if (!is_numeric($madingId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID mading tidak valid.'
            ])->setStatusCode(400);
        }

        $likeModel = new \App\Models\MadingLikeModel();

        // Cek apakah user sudah like
        $isLikedRecord = $likeModel->where('mading_id', $madingId)
            ->where('user_id', $userId)
            ->first();

        if ($isLikedRecord) {
            // Unlike: gunakan query builder langsung karena tabel tidak memiliki kolom 'id' sebagai PK
            $db = \Config\Database::connect();
            $db->table('mading_likes')
                ->where('mading_id', $madingId)
                ->where('user_id', $userId)
                ->delete();
            $liked = false;
        } else {
            // Like: tambah like
            $likeModel->insert([
                'mading_id' => $madingId,
                'user_id'   => $userId
            ]);
            $liked = true;
        }

        // Hitung total like
        $totalLikes = $likeModel->where('mading_id', $madingId)->countAllResults();

        // Kirim kembali CSRF hash baru agar front-end bisa memperbarui token untuk request berikutnya
        $newCsrf = csrf_hash();

        return $this->response->setJSON([
            'success'     => true,
            'liked'       => $liked,
            'total_likes' => $totalLikes,
            'csrf'        => $newCsrf
        ]);
    }

    /**
     * Handle context menu actions untuk komentar
     * POST /Mading/context-menu-action
     */
    public function contextMenuAction()
    {
        $action = $this->request->getPost('action');
        $commentId = $this->request->getPost('comment_id');
        $userId = session('user_id');

        if (!$action || !$commentId || !$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid',
                'csrf'    => csrf_hash(),
            ]);
        }

        $commentModel = new MadingCommentModel();
        $comment = $commentModel->find($commentId);

        if (!$comment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar tidak ditemukan',
                'csrf'    => csrf_hash(),
            ]);
        }

        // Check permission
        $canEdit = ($comment['user_id'] == $userId);
        $canDelete = ($comment['user_id'] == $userId);

        switch ($action) {
            case 'edit':
                if (!$canEdit) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tidak memiliki izin untuk mengedit komentar ini',
                        'csrf'    => csrf_hash(),
                    ]);
                }
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Edit komentar',
                    'action' => 'edit',
                    'commentId' => $commentId,
                    'content' => $comment['isi_komentar'],
                    'csrf'    => csrf_hash(),
                ]);

            case 'delete':
                if (!$canDelete) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tidak memiliki izin untuk menghapus komentar ini',
                        'csrf'    => csrf_hash(),
                    ]);
                }

                // Soft delete komentar beserta semua balasan (rekursif)
                [$deletedIds, $error] = $this->softDeleteCommentThread((int)$commentId);
                if ($error !== null) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $error,
                        'csrf'    => csrf_hash(),
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Komentar dan balasan terkait berhasil dihapus',
                    'action' => 'delete',
                    'commentId' => $commentId,
                    'deletedIds' => $deletedIds,
                    'csrf'    => csrf_hash(),
                ]);

            case 'report':
                // TODO: Implementasi sistem laporan
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Komentar berhasil dilaporkan',
                    'action' => 'report',
                    'commentId' => $commentId,
                    'csrf'    => csrf_hash(),
                ]);

            case 'copy':
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Komentar berhasil disalin',
                    'action' => 'copy',
                    'commentId' => $commentId,
                    'content' => $comment['isi_komentar']
                ]);

            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Aksi tidak valid'
                ]);
        }
    }

    /**
     * Update komentar
     * POST /Mading/update-comment
     */
    public function updateComment()
    {
        $commentId = $this->request->getPost('comment_id');
        $content = $this->request->getPost('content');
        $userId = session('user_id');

        if (!$commentId || !$content || !$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid',
                'csrf'    => csrf_hash(),
            ]);
        }

        $commentModel = new MadingCommentModel();
        $comment = $commentModel->find($commentId);

        if (!$comment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar tidak ditemukan',
                'csrf'    => csrf_hash(),
            ]);
        }

        // Check permission
        if ($comment['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak memiliki izin untuk mengedit komentar ini',
                'csrf'    => csrf_hash(),
            ]);
        }

        // Validate content
        if (strlen(trim($content)) < 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar terlalu pendek (minimal 3 karakter)',
                'csrf'    => csrf_hash(),
            ]);
        }

        if (strlen($content) > 500) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar terlalu panjang (maksimal 500 karakter)',
                'csrf'    => csrf_hash(),
            ]);
        }

        // Update komentar
        $commentModel->update($commentId, [
            'isi_komentar' => trim($content),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui',
            'content' => trim($content),
            'csrf'    => csrf_hash(),
        ]);
    }
}
