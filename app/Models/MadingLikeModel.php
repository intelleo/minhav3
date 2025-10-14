<?php

namespace App\Models;

use CodeIgniter\Model;

class MadingLikeModel extends Model
{
    protected $table = 'mading_likes';
    protected $allowedFields = ['mading_id', 'user_id', 'user_type'];
    // tabel mading_likes tidak memiliki kolom updated_at pada skema saat ini
    // matikan otomatis timestamps untuk menghindari error saat insert
    protected $useTimestamps = false;

    // Hitung total like per mading
    public function getTotalLikes($madingId)
    {
        return $this->where('mading_id', $madingId)->countAllResults();
    }

    // Cek apakah user sudah like
    public function isLiked($madingId, $userId, $userType = 'user')
    {
        return $this->where([
            'mading_id' => $madingId,
            'user_id' => $userId,
            'user_type' => $userType
        ])->countAllResults() > 0;
    }
}
