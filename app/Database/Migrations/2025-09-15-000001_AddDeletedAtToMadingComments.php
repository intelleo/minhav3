<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToMadingComments extends Migration
{
  public function up()
  {
    // Tambah kolom deleted_at jika belum ada
    if (! $this->db->fieldExists('deleted_at', 'mading_comments')) {
      $fields = [
        'deleted_at' => [
          'type' => 'DATETIME',
          'null' => true,
          'default' => null,
          'after' => 'updated_at',
        ],
      ];
      $this->forge->addColumn('mading_comments', $fields);
    }
  }

  public function down()
  {
    if ($this->db->fieldExists('deleted_at', 'mading_comments')) {
      $this->forge->dropColumn('mading_comments', 'deleted_at');
    }
  }
}
