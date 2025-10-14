<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixMadingLikesForeignKey extends Migration
{
  public function up()
  {
    $db = \Config\Database::connect();
    $prefix = $db->getPrefix();
    // Determine actual table name (with or without prefix)
    $prefixedTable = $prefix . 'mading_likes';
    $unprefixedTable = 'mading_likes';

    $tableName = $db->query("SHOW TABLES LIKE '{$prefixedTable}'")->getNumRows() > 0
      ? $prefixedTable
      : ($db->query("SHOW TABLES LIKE '{$unprefixedTable}'")->getNumRows() > 0 ? $unprefixedTable : null);

    if ($tableName === null) {
      echo "ℹ️ Tabel mading_likes tidak ditemukan. Melewati migrasi FK.\n";
      return;
    }

    // Drop existing foreign key constraint for user_id
    // because admin users are not in user_auth table
    try {
      $db->query("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$tableName}_user_id_foreign`");
      echo "✅ Foreign key constraint user_id berhasil dihapus\n";
    } catch (\Exception $e) {
      echo "ℹ️ Foreign key constraint user_id tidak ditemukan atau sudah dihapus\n";
    }

    // Keep only mading_id foreign key constraint
    // user_id constraint removed because:
    // - user_id can be from user_auth (for regular users)
    // - user_id can be from auth_admin (for admin users)
    // - We use user_type to distinguish between them
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
      echo "ℹ️ Tabel mading_likes tidak ditemukan saat down(). Melewati.\n";
      return;
    }

    // Re-add user_id foreign key constraint (only for user_auth)
    $db->query("ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$tableName}_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `{$prefix}user_auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
  }
}
