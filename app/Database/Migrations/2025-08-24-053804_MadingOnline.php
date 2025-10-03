<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MadingOnline extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'            => 'INT',
                'unsigned'        => true,
                'auto_increment'  => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['edukasi', 'pengumuman', 'event', 'berita'],
                'default'    => 'edukasi',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                // 'unique'   => true, // aktifkan jika ingin file wajib unik
            ],
            'tgl_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_akhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'aktif', 'nonaktif'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'admin_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, // bisa null sementara
            ],
        ]);
        // Tambahkan foreign key ke auth_admin
        $this->forge->addKey('id', true);
        $this->forge->createTable('mading_online');
        $this->forge->addForeignKey('admin_id', 'auth_admin', 'id', 'SET_NULL', 'CASCADE');
        $this->forge->processIndexes('mading_online');
    }

    public function down()
    {
        // Hapus foreign key dulu
        // $this->forge->dropForeignKey('mading_online', 'mading_online_admin_id_foreign');

        // Baru hapus tabel
        $this->forge->dropTable('mading_online', true);
    }
}
