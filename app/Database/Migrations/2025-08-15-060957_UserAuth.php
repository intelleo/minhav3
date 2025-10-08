<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserAuth extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'namalengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jurusan' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'Komputerisasi Akuntansi',
                    'Manajemen Informatika',
                    'Sistem Informasi',
                    'Teknik Informatika',
                    'Sistem Komputer',
                    'Hukum',
                    'Administrasi Publik',
                    'Kewirausahaan'
                ],
                'default'    => 'Teknik Informatika',
            ],
            'npm' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'aktif', 'nonaktif'],
                'default'    => 'pending',
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_profil' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status'); //index untuk kolom status
        $this->forge->addKey('jurusan'); //index untuk kolom jurusan
        $this->forge->createTable('user_auth'); // plural agar konsisten
    }

    public function down()
    {
        $this->forge->dropTable('user_auth', true);
    }
}
