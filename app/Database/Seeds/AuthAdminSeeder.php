<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AuthAdminSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Hapus dulu (opsional)
        $db->table('auth_admin')->where('username', 'minhaai')->delete();

        // Hash password: "minha27"
        $password = password_hash('minha27', PASSWORD_DEFAULT);

        $data = [
            'username'   => 'minhaai',
            'password'   => $password,
            'created_at' => Time::now(),
            'updated_at' => Time::now(),
        ];

        $db->table('auth_admin')->insert($data);

        echo "✅ Admin 'minhaai' berhasil dibuat!\n";
    }
}
