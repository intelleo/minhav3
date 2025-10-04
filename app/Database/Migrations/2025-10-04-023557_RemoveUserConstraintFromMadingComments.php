<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUserConstraintFromMadingComments extends Migration
{
    public function up()
    {
        // Cek constraint yang ada terlebih dahulu
        $db = \Config\Database::connect();
        $constraints = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Minha_mading_comments' AND COLUMN_NAME = 'user_id' AND CONSTRAINT_NAME != 'PRIMARY'")->getResultArray();

        foreach ($constraints as $constraint) {
            $constraintName = $constraint['CONSTRAINT_NAME'];
            try {
                $this->forge->dropForeignKey('mading_comments', $constraintName);
                echo "Dropped constraint: $constraintName\n";
            } catch (\Exception $e) {
                echo "Could not drop constraint $constraintName: " . $e->getMessage() . "\n";
            }
        }
    }

    public function down()
    {
        // Kembalikan foreign key constraint jika diperlukan
        $this->forge->addForeignKey('user_id', 'user_auth', 'id', 'CASCADE', 'CASCADE', 'mading_comments_user_id_foreign');
    }
}
