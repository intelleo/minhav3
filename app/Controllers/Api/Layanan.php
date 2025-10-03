<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LayananModel;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\Handlers\BaseHandler;

class Layanan extends BaseController
{
    protected $layananModel;

    public function __construct()
    {
        $this->layananModel = new LayananModel();
    }

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
     * Ambil semua data atau filter berdasarkan kategori dengan caching
     * Contoh:
     * - /api/layanan          → semua data
     * - /api/layanan/mading   → hanya kategori Mading
     */
    public function index($kategori = null)
    {
        $cacheKey = $kategori ? "api_layanan_{$kategori}" : "api_layanan_all";
        $cacheKey .= "_" . date('Y-m-d');

        $response = $this->getCache()->remember($cacheKey, 300, function () use ($kategori) {
            if ($kategori) {
                $kategori = ucfirst(strtolower($kategori)); // normalisasi format
                $data = $this->layananModel->getByKategori($kategori);
            } else {
                $data = $this->layananModel->getAllWithCache();
            }

            return [
                'status' => 'success',
                'total'  => count($data),
                'data'   => $data,
                'cached_at' => date('Y-m-d H:i:s')
            ];
        });

        return $this->response->setJSON($response);
    }

    /**
     * API untuk pencarian layanan dengan caching
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        if (empty($keyword)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter pencarian (q) diperlukan'
            ])->setStatusCode(400);
        }

        $cacheKey = "api_layanan_search_" . md5($keyword) . "_" . date('Y-m-d');

        $response = $this->getCache()->remember($cacheKey, 300, function () use ($keyword) {
            $data = $this->layananModel->searchLayanan($keyword);

            return [
                'status' => 'success',
                'total'  => count($data),
                'data'   => $data,
                'keyword' => $keyword,
                'cached_at' => date('Y-m-d H:i:s')
            ];
        });

        return $this->response->setJSON($response);
    }

    /**
     * API untuk layanan terbaru dengan caching
     */
    public function latest($limit = 5)
    {
        $limit = min($limit, 20); // batasi maksimal 20
        $cacheKey = "api_layanan_latest_{$limit}_" . date('Y-m-d');

        $response = $this->getCache()->remember($cacheKey, 300, function () use ($limit) {
            $data = $this->layananModel->getLatest($limit);

            return [
                'status' => 'success',
                'total'  => count($data),
                'data'   => $data,
                'cached_at' => date('Y-m-d H:i:s')
            ];
        });

        return $this->response->setJSON($response);
    }
}
