<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserTypeToMadingLikes extends Migration
{
  public function up()
  {
    // Check if column already exists first
    $db = \Config\Database::connect();
    $prefix = $db->getPrefix();
    // Determine actual table name (with or without prefix)
    $prefixedTable = $prefix . 'mading_likes';
    $unprefixedTable = 'mading_likes';

    $tableExists = $db->query("SHOW TABLES LIKE '{$prefixedTable}'")->getNumRows() > 0
      ? $prefixedTable
      : ($db->query("SHOW TABLES LIKE '{$unprefixedTable}'")->getNumRows() > 0 ? $unprefixedTable : null);

    if ($tableExists === null) {
      echo "ℹ️ Tabel mading_likes tidak ditemukan (prefixed maupun unprefixed). Melewati migrasi.\n";
      return;
    }

    $tableName = $tableExists;

    $query = $db->query("SHOW COLUMNS FROM `{$tableName}` LIKE 'user_type'");

    if ($query->getNumRows() == 0) {
      // Column doesn't exist, add it
      // Use forge for logical table name (CI handles prefix)
      $this->forge->addColumn('mading_likes', [
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
                UPDATE `{$tableName}` ml 
                INNER JOIN `{$adminTable}` aa ON aa.id = ml.user_id 
                SET ml.user_type = 'admin'
            ");

      echo "✅ Kolom user_type berhasil ditambahkan ke mading_likes dan data existing diupdate\n";
    } else {
      echo "ℹ️ Kolom user_type sudah ada di mading_likes, skip migration\n";
    }
  }

  public function down()
  {
    $db = \Config\Database::connect();
    $prefix = $db->getPrefix();
    $tableName = $prefix . 'mading_likes';

    // Restore original composite primary key
    $db->query("ALTER TABLE `{$tableName}` DROP PRIMARY KEY");
    $db->query("ALTER TABLE `{$tableName}` ADD PRIMARY KEY (`mading_id`, `user_id`)");

    // Drop user_type column
    $this->forge->dropColumn('mading_likes', 'user_type');
  }
}
