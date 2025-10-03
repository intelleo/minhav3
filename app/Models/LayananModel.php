<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\Handlers\BaseHandler;

class LayananModel extends Model
{
    protected $table      = 'layanan_informasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['judul', 'deskripsi', 'kategori', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get cache instance with proper type hint
     * 
     * @return BaseHandler
     */
    private function getCache(): BaseHandler
    {
        return \Config\Services::cache();
    }

    /**
     * Ambil semua layanan dengan caching
     */
    public function getAllWithCache()
    {
        $cacheKey = 'layanan_all_' . date('Y-m-d');

        return $this->getCache()->remember($cacheKey, 600, function () {
            return $this->orderBy('created_at', 'DESC')->findAll();
        });
    }

    /**
     * Ambil layanan berdasarkan kategori dengan caching
     */
    public function getByKategori($kategori)
    {
        $cacheKey = "layanan_kategori_{$kategori}_" . date('Y-m-d');

        return $this->getCache()->remember($cacheKey, 600, function () use ($kategori) {
            return $this->where('kategori', $kategori)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        });
    }

    /**
     * Ambil layanan terbaru dengan caching
     */
    public function getLatest($limit = 5)
    {
        $cacheKey = "layanan_latest_{$limit}_" . date('Y-m-d');

        return $this->getCache()->remember($cacheKey, 300, function () use ($limit) {
            return $this->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->findAll();
        });
    }

    /**
     * Cari layanan berdasarkan judul/deskripsi dengan caching
     */
    public function searchLayanan($keyword)
    {
        $cacheKey = "layanan_search_" . md5($keyword) . "_" . date('Y-m-d');

        return $this->getCache()->remember($cacheKey, 300, function () use ($keyword) {
            return $this->groupStart()
                ->like('judul', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();
        });
    }

    /**
     * Invalidate cache saat ada perubahan data layanan
     */
    public function invalidateCache()
    {
        $today = date('Y-m-d');

        // Hapus semua cache layanan
        $this->getCache()->delete("layanan_all_{$today}");
        $this->getCache()->delete("layanan_latest_5_{$today}");
        $this->getCache()->delete("layanan_latest_10_{$today}");

        // Hapus cache kategori (kita tidak tahu semua kategori, jadi hapus yang umum)
        $kategoriUmum = ['informasi', 'pengumuman', 'acara', 'berita'];
        foreach ($kategoriUmum as $kategori) {
            $this->getCache()->delete("layanan_kategori_{$kategori}_{$today}");
        }
    }
}
