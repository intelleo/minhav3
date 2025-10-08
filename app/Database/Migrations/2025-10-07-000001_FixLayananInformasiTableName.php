<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixLayananInformasiTableName extends Migration
{
  public function up()
  {
    // Cek apakah tabel layanan_informasi ada (tanpa prefix) dan rename jika perlu
    try {
      $this->db->query("RENAME TABLE layanan_informasi TO minha_layanan_informasi");
    } catch (\Exception $e) {
      // Tabel layanan_informasi tidak ada, lanjutkan
    }

    // Cek apakah tabel minha_layanan_informasi sudah ada, jika belum buat
    try {
      $this->db->query("SELECT 1 FROM minha_layanan_informasi LIMIT 1");
    } catch (\Exception $e) {
      // Jika belum ada, buat tabel baru
      $this->forge->addField([
        'id' => [
          'type'           => 'INT',
          'unsigned'       => true,
          'auto_increment' => true,
        ],
        'judul' => [
          'type'       => 'VARCHAR',
          'constraint' => 255,
        ],
        'deskripsi' => [
          'type' => 'TEXT',
        ],
        'kategori' => [
          'type'       => 'ENUM',
          'constraint' => ['Akademik', 'Administrasi', 'Umum'],
        ],
        'created_at' => [
          'type' => 'TIMESTAMP',
          'null' => true,
          'default' => null,
        ],
      ]);
      $this->forge->addKey('id', true);
      $this->forge->createTable('minha_layanan_informasi');
    }
  }

  public function down()
  {
    // Rename kembali ke tanpa prefix jika diperlukan
    try {
      $this->db->query("RENAME TABLE minha_layanan_informasi TO layanan_informasi");
    } catch (\Exception $e) {
      // Tabel tidak ada, tidak perlu melakukan apa-apa
    }
  }
}
