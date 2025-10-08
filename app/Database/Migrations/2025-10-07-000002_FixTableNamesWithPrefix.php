<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixTableNamesWithPrefix extends Migration
{
  public function up()
  {
    // Daftar tabel yang perlu di-rename dari tanpa prefix ke dengan prefix
    $tablesToRename = [
      'mading_online' => 'minha_mading_online',
      'mading_comments' => 'minha_mading_comments',
      'mading_likes' => 'minha_mading_likes',
      'user_auth' => 'minha_user_auth',
      'auth_admin' => 'minha_auth_admin',
      'notif_dismissed' => 'minha_notif_dismissed',
      'notif_seen' => 'minha_notif_seen',
    ];

    foreach ($tablesToRename as $oldName => $newName) {
      try {
        // Cek apakah tabel lama ada dan tabel baru belum ada
        $this->db->query("SELECT 1 FROM {$oldName} LIMIT 1");
        $this->db->query("SELECT 1 FROM {$newName} LIMIT 1");
        // Jika sampai sini berarti kedua tabel ada, skip
      } catch (\Exception $e) {
        try {
          // Cek apakah tabel lama ada
          $this->db->query("SELECT 1 FROM {$oldName} LIMIT 1");
          // Jika sampai sini berarti tabel lama ada, rename
          $this->db->query("RENAME TABLE {$oldName} TO {$newName}");
        } catch (\Exception $e2) {
          // Tabel lama tidak ada, lanjutkan
        }
      }
    }
  }

  public function down()
  {
    // Daftar tabel yang perlu di-rename kembali dari dengan prefix ke tanpa prefix
    $tablesToRename = [
      'minha_mading_online' => 'mading_online',
      'minha_mading_comments' => 'mading_comments',
      'minha_mading_likes' => 'mading_likes',
      'minha_user_auth' => 'user_auth',
      'minha_auth_admin' => 'auth_admin',
      'minha_notif_dismissed' => 'notif_dismissed',
      'minha_notif_seen' => 'notif_seen',
    ];

    foreach ($tablesToRename as $oldName => $newName) {
      try {
        $this->db->query("RENAME TABLE {$oldName} TO {$newName}");
      } catch (\Exception $e) {
        // Tabel tidak ada, lanjutkan
      }
    }
  }
}
