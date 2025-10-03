<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MadingLikes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'mading_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Composite Primary Key → cegah like ganda
        $this->forge->addKey(['mading_id', 'user_id'], true);

        // Foreign Key ke mading_online
        $this->forge->addForeignKey('mading_id', 'mading_online', 'id', 'CASCADE', 'CASCADE');

        // Foreign Key ke user_auth
        $this->forge->addForeignKey('user_id', 'user_auth', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('mading_likes');
    }

    public function down()
    {
        $this->forge->dropTable('mading_likes', true);
    }
}
