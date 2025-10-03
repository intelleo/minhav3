<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\Handlers\BaseHandler;

class MadingModel extends Model
{
    protected $table = 'mading_online';
    protected $primaryKey = 'id';
    protected $allowedFields = ['judul', 'category', 'deskripsi', 'file', 'tgl_mulai', 'tgl_akhir', 'status', 'admin_id', 'views'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get cache instance with proper type hint
     * 
     * @return BaseHandler
     */
    private function getCache(): BaseHandler
    {
        return \Config\Services::cache();
    }

    // Otomatis nonaktifkan mading kadaluarsa
    public function archiveExpired()
    {
        $today = date('Y-m-d');

        $this->where('tgl_akhir <', $today)
            ->where('status', 'aktif')
            ->set(['status' => 'nonaktif'])
            ->update();
    }

    /**
     * Tambahkan data tambahan: views, total_likes, total_comments
     * 
     * @param array $madingList
     * @return array
     */
    private function enrichMadingData(array $madingList): array
    {
        $likeModel = new \App\Models\MadingLikeModel();
        $commentModel = new \App\Models\MadingCommentModel();

        foreach ($madingList as &$mading) {
            // Pastikan views tidak null
            $mading['views'] = $mading['views'] ?? 0;

            // Hitung likes
            $mading['total_likes'] = $likeModel->getTotalLikes($mading['id']);

            // Hitung komentar
            $mading['total_comments'] = $commentModel->where('mading_id', $mading['id'])->countAllResults();
        }

        return $madingList;
    }

    // Versi publik untuk dipanggil dari controller API
    public function enrichMadingDataPublic(array $madingList): array
    {
        return $this->enrichMadingData($madingList);
    }
    // Ambil semua mading + data admin dengan caching
    public function getAllWithAdmin()
    {
        $today = date('Y-m-d');
        $cacheKey = "mading_all_with_admin_{$today}";

        return $this->getCache()->remember($cacheKey, 300, function () use ($today) {
            // Nonaktifkan mading kadaluarsa
            $this->archiveExpired();

            // Ambil semua mading aktif & belum kadaluarsa
            $builder = $this->db->table('mading_online')
                ->select('mading_online.*, auth_admin.username')
                ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
                ->where('mading_online.status', 'aktif')
                ->where('mading_online.tgl_akhir >=', $today)
                ->orderBy('mading_online.created_at', 'DESC');

            $result = $builder->get()->getResultArray();

            // Tambahkan data tambahan
            return $this->enrichMadingData($result);
        });
    }

    // Ambil satu mading + admin dengan caching
    public function getWithAdmin($id)
    {
        $today = date('Y-m-d');
        $cacheKey = "mading_single_{$id}_{$today}";

        return $this->getCache()->remember($cacheKey, 300, function () use ($id, $today) {
            // Nonaktifkan mading kadaluarsa
            $this->archiveExpired();

            // Ambil satu mading
            $mading = $this->select('mading_online.*, auth_admin.username')
                ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
                ->where('mading_online.id', $id)
                ->where('mading_online.status', 'aktif')
                ->where('mading_online.tgl_akhir >=', $today)
                ->first();

            if (!$mading) {
                return null;
            }

            // Gunakan fungsi enrich untuk konsistensi
            $enriched = $this->enrichMadingData([$mading]);

            return $enriched[0]; // kembalikan sebagai array tunggal
        });
    }

    // Ambil mading terbaru untuk halaman depan dashboard overview mading dengan caching
    public function getLatest($limit = 3)
    {
        $today = date('Y-m-d');
        $cacheKey = "mading_latest_{$limit}_{$today}";

        return $this->getCache()->remember($cacheKey, 300, function () use ($limit, $today) {
            // Pastikan yang kadaluarsa dinonaktifkan
            $this->archiveExpired();

            return $this->select('mading_online.*, auth_admin.username')
                ->join('auth_admin', 'auth_admin.id = mading_online.admin_id', 'left')
                ->where('mading_online.status', 'aktif')
                ->where('mading_online.tgl_akhir >=', $today)
                ->orderBy('mading_online.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
        });
    }

    /**
     * Invalidate cache saat ada perubahan data mading
     */
    public function invalidateCache($madingId = null)
    {
        $today = date('Y-m-d');

        // Hapus cache untuk semua mading
        $this->getCache()->delete("mading_all_with_admin_{$today}");
        $this->getCache()->delete("mading_latest_3_{$today}");
        $this->getCache()->delete("mading_latest_5_{$today}");

        // Hapus cache untuk mading spesifik jika ada
        if ($madingId) {
            $this->getCache()->delete("mading_single_{$madingId}_{$today}");
        }
    }
}
