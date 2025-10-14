<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLayananInformasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'TEXT',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['BAAK', 'BAUK', 'Umum'],
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('layanan_informasi');
    }

    public function down()
    {
        $this->forge->dropTable('layanan_informasi', true);
    }
}
