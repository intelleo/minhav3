<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MadingComments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mading_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'parent_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'isi_komentar' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('mading_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('parent_id');

        // Relasi ke mading_online
        $this->forge->addForeignKey('mading_id', 'mading_online', 'id', 'CASCADE', 'CASCADE');

        // Relasi ke user_auth
        $this->forge->addForeignKey('user_id', 'user_auth', 'id', 'CASCADE', 'CASCADE');

        // Relasi reply ke komentar lain
        $this->forge->addForeignKey('parent_id', 'mading_comments', 'id', 'SET_NULL', 'CASCADE');

        $this->forge->createTable('mading_comments');
    }

    public function down()
    {
        $this->forge->dropTable('mading_comments', true);
    }
}
