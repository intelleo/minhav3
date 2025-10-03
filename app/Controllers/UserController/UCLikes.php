<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use App\Models\MadingModel;

class UCLikes extends BaseController
{
  public function index()
  {
    $userId = (int) session('user_id');
    if (!$userId) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $today = date('Y-m-d');

    // Ambil daftar mading yang disukai user, hanya yang masih aktif dan belum kadaluarsa
    $db = \Config\Database::connect();
    $builder = $db->table('mading_likes ml')
      ->select('mo.*, mo.id as mading_id, aa.username')
      ->join('mading_online mo', 'mo.id = ml.mading_id')
      ->join('auth_admin aa', 'aa.id = mo.admin_id', 'left')
      ->where('ml.user_id', $userId)
      ->where('mo.status', 'aktif')
      ->where('mo.tgl_akhir >=', $today)
      ->orderBy('mo.created_at', 'DESC');

    $liked = $builder->get()->getResultArray();

    // Enrich data dengan total likes dan total comments
    if (!empty($liked)) {
      $madingModel = new MadingModel();
      $liked = $madingModel->enrichMadingDataPublic($liked);
    }

    $data = [
      'title' => 'Like Saya',
      'likedMading' => $liked,
    ];

    return view('user/user_likes', $data);
  }
}
