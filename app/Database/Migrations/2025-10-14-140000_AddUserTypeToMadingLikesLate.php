<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserTypeToMadingLikesLate extends Migration
{
  public function up()
  {
    $db = \Config\Database::connect();
    $prefix = $db->getPrefix();

    $prefixedTable = $prefix . 'mading_likes';
    $unprefixedTable = 'mading_likes';

    $tableName = $db->query("SHOW TABLES LIKE '{$prefixedTable}'")->getNumRows() > 0
      ? $prefixedTable
      : ($db->query("SHOW TABLES LIKE '{$unprefixedTable}'")->getNumRows() > 0 ? $unprefixedTable : null);

    if ($tableName === null) {
      echo "ℹ️ Tabel mading_likes belum ada, melewati migrasi add user_type (late).\n";
      return;
    }

    // Cek apakah kolom sudah ada
    $col = $db->query("SHOW COLUMNS FROM `{$tableName}` LIKE 'user_type'");
    if ($col->getNumRows() === 0) {
      // Tambah kolom melalui forge (logical name tanpa prefix)
      $this->forge->addColumn('mading_likes', [
        'user_type' => [
          'type' => 'ENUM',
          'constraint' => ['user', 'admin'],
          'default' => 'user',
          'after' => 'user_id',
        ],
      ]);

      // Update existing: set admin berdasarkan auth_admin
      $adminTable = $prefix . 'auth_admin';
      try {
        $db->query("UPDATE `{$tableName}` ml INNER JOIN `{$adminTable}` aa ON aa.id = ml.user_id SET ml.user_type = 'admin'");
      } catch (\Throwable $e) {
        // Jika table auth_admin belum ada, skip
        echo "ℹ️ Melewati update existing admin likes: " . $e->getMessage() . "\n";
      }

      echo "✅ Kolom user_type ditambahkan ke {$tableName} (late).\n";
    } else {
      echo "ℹ️ Kolom user_type sudah ada pada {$tableName}, skip.\n";
    }
  }

  public function down()
  {
    $db = \Config\Database::connect();
    $prefix = $db->getPrefix();

    $prefixedTable = $prefix . 'mading_likes';
    $unprefixedTable = 'mading_likes';

    $tableName = $db->query("SHOW TABLES LIKE '{$prefixedTable}'")->getNumRows() > 0
      ? $prefixedTable
      : ($db->query("SHOW TABLES LIKE '{$unprefixedTable}'")->getNumRows() > 0 ? $unprefixedTable : null);

    if ($tableName === null) {
      echo "ℹ️ Tabel mading_likes tidak ditemukan saat down(), skip.\n";
      return;
    }

    $this->forge->dropColumn('mading_likes', 'user_type');
  }
}
