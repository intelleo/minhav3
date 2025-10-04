<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserTypeColumnToMadingComments extends Migration
{
    public function up()
    {
        // Check if column already exists first
        $db = \Config\Database::connect();
        $prefix = $db->getPrefix();
        $tableName = $prefix . 'mading_comments';

        $query = $db->query("SHOW COLUMNS FROM `{$tableName}` LIKE 'user_type'");

        if ($query->getNumRows() == 0) {
            // Column doesn't exist, add it
            $this->forge->addColumn('mading_comments', [
                'user_type' => [
                    'type' => 'ENUM',
                    'constraint' => ['user', 'admin'],
                    'default' => 'user',
                    'after' => 'user_id',
                ],
            ]);

            // Update existing records based on auth_admin lookup
            $adminTable = $prefix . 'auth_admin';
            $db->query("
                UPDATE `{$tableName}` mc 
                INNER JOIN `{$adminTable}` aa ON aa.id = mc.user_id 
                SET mc.user_type = 'admin'
            ");

            echo "✅ Kolom user_type berhasil ditambahkan dan data existing diupdate\n";
        } else {
            echo "ℹ️ Kolom user_type sudah ada, skip migration\n";
        }
    }

    public function down()
    {
        $this->forge->dropColumn('mading_comments', 'user_type');
    }
}
