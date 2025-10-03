<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAuthModel extends Model
{
    protected $table            = 'user_auth';           // ✅ sesuaikan dengan migration
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // ✅ Field yang boleh diisi
    protected $allowedFields    = [
        'namalengkap',
        'jurusan',
        'npm',
        'password',
        'status',
        'bio',
        'foto_profil',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // ✅ Aktifkan timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $dateFormat    = 'datetime';

    // ✅ Tambahkan validasi dasar
    protected $validationRules = [
        'npm'         => 'required|numeric|min_length[10]|max_length[12]|is_unique[user_auth.npm]', // asumsi NPM = email, atau sesuaikan
        'password' => 'required|min_length[6]',
        'namalengkap' => 'required|min_length[3]|max_length[100]',
        'bio'         => 'permit_empty|max_length[500]',
        'foto_profil' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'npm' => [
            'required' => 'NPM harus diisi.',
            'valid_email' => 'Format NPM tidak valid.',
            'min_length' => 'NPM minimal 10 angka menggunakan npm masing-masing.',
            'is_unique' => 'NPM sudah terdaftar.'
        ],
        'password' => [
            'required' => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    // 🔐 Hash password sebelum simpan
    protected function hashPassword(array $data): array
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }
}
