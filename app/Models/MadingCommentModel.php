<?php

namespace App\Models;

use CodeIgniter\Model;

class MadingCommentModel extends Model
{
    protected $table = 'mading_comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['mading_id', 'user_id', 'user_type', 'parent_id', 'isi_komentar', 'deleted_at', 'updated_at', 'created_at'];
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
     * Handle komentar dari user dan admin.
     */
    public function getAllByMadingWithUser(int $madingId): array
    {
        $db = \Config\Database::connect();

        // Ambil semua komentar untuk mading ini
        $comments = $this->where('mading_id', $madingId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $result = [];

        foreach ($comments as $comment) {
            $userId = $comment['user_id'];
            $userType = $comment['user_type'] ?? 'user'; // Default untuk data lama

            // Gunakan kolom user_type untuk membedakan
            if ($userType === 'admin') {
                // Ambil data admin
                $userData = $db->table('auth_admin')
                    ->select('username as namalengkap, NULL as foto_profil')
                    ->where('id', $userId)
                    ->get()
                    ->getRowArray();

                if ($userData) {
                    $comment['namalengkap'] = $userData['namalengkap'];
                    $comment['foto_profil'] = null;
                } else {
                    $comment['namalengkap'] = 'Unknown Admin';
                    $comment['foto_profil'] = null;
                }
            } else {
                // Ambil data user
                $userData = $db->table('user_auth')
                    ->select('namalengkap, foto_profil')
                    ->where('id', $userId)
                    ->get()
                    ->getRowArray();

                if ($userData) {
                    $comment['namalengkap'] = $userData['namalengkap'];
                    $comment['foto_profil'] = $userData['foto_profil'];
                } else {
                    $comment['namalengkap'] = 'Unknown User';
                    $comment['foto_profil'] = null;
                }
            }

            // Pastikan user_type terset
            $comment['user_type'] = $userType;

            $result[] = $comment;
        }

        return $result;
    }
}
