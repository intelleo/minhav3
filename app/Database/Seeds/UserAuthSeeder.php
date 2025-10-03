<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserAuthSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'namalengkap' => 'Salman Asmandi',
                'npm'    => '2021020109',
                'password'    => password_hash('salman27', PASSWORD_BCRYPT),
                'status'      => 'aktif',
            ],
            [
                'namalengkap' => 'Andi Aulia Putri',
                'npm'    => '2021020023',
                'password'    => password_hash('Aulia27', PASSWORD_BCRYPT),
                'status'      => 'aktif',
            ],
            [
                'namalengkap' => 'Ahmad Sawal',
                'npm'    => '2021020110',
                'password'    => password_hash('sawal27', PASSWORD_BCRYPT),
                'status'      => 'aktif',
            ],
        ];

        // Insert batch ke tabel user_auth
        $this->db->table('user_auth')->insertBatch($data);
    }
}
