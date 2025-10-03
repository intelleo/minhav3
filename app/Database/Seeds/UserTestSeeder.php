<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserTestSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        // Generate 25 test users untuk testing pagination (10 per page = 3 pages)
        $jurusanList = ['teknik informatika', 'sistem informasi', 'sistem komputer', 'manajemen informatika'];
        $statusList = ['aktif', 'pending', 'nonaktif'];

        for ($i = 1; $i <= 25; $i++) {
            $data[] = [
                'namalengkap' => 'User Test ' . $i,
                'npm' => '202' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'jurusan' => $jurusanList[array_rand($jurusanList)],
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'status' => $statusList[array_rand($statusList)],
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        // Insert data
        $this->db->table('user_auth')->insertBatch($data);

        echo "Inserted 25 test users for pagination testing\n";
    }
}
