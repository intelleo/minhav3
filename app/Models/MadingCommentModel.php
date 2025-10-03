<?php

namespace App\Models;

use CodeIgniter\Model;

class MadingCommentModel extends Model
{
    protected $table = 'mading_comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['mading_id', 'user_id', 'parent_id', 'isi_komentar', 'deleted_at', 'updated_at', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    public function getMainComments($madingId)
    {
        return $this->select('mading_comments.*, user_auth.namalengkap, user_auth.foto_profil')
            ->join('user_auth', 'user_auth.id = mading_comments.user_id')
            ->where('mading_comments.mading_id', $madingId)
            ->where('mading_comments.parent_id IS NULL')
            // Urutkan terbaru di atas
            ->orderBy('mading_comments.created_at', 'DESC')
            ->findAll();
    }

    public function getReplies($parentId)
    {
        return $this->select('mading_comments.*, user_auth.namalengkap, user_auth.foto_profil')
            ->join('user_auth', 'user_auth.id = mading_comments.user_id')
            ->where('mading_comments.parent_id', $parentId)
            // Urutkan terbaru di atas
            ->orderBy('mading_comments.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Ambil semua komentar untuk satu mading dalam satu query.
     */
    public function getAllByMadingWithUser(int $madingId): array
    {
        // Ambil semua komentar untuk satu mading, terbaru di atas
        return $this->select('mading_comments.*, user_auth.namalengkap, user_auth.foto_profil')
            ->join('user_auth', 'user_auth.id = mading_comments.user_id')
            ->where('mading_comments.mading_id', $madingId)
            ->orderBy('mading_comments.created_at', 'DESC')
            ->findAll();
    }
}
