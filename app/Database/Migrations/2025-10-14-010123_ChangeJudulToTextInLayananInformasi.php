<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeJudulToTextInLayananInformasi extends Migration
{
    public function up()
    {
        // Hapus index pada kolom judul terlebih dahulu
        try {
            $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_judul");
        } catch (\Exception $e) {
            // Index tidak ada, lanjutkan
        }

        // Hapus FULLTEXT index yang menggunakan kolom judul
        try {
            $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_search");
        } catch (\Exception $e) {
            // Index tidak ada, lanjutkan
        }

        // Mengubah tipe data kolom judul dari VARCHAR(255) menjadi TEXT
        $fields = [
            'judul' => [
                'type' => 'TEXT',
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('layanan_informasi', $fields);

        // Buat ulang FULLTEXT index dengan kolom judul yang sudah berubah ke TEXT
        try {
            $this->db->query("ALTER TABLE minha_layanan_informasi ADD FULLTEXT idx_search (judul, deskripsi)");
        } catch (\Exception $e) {
            // Index sudah ada atau gagal dibuat, lanjutkan
        }
    }

    public function down()
    {
        // Hapus FULLTEXT index terlebih dahulu
        try {
            $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_search");
        } catch (\Exception $e) {
            // Index tidak ada, lanjutkan
        }

        // Mengembalikan tipe data kolom judul ke VARCHAR(255)
        $fields = [
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('layanan_informasi', $fields);

        // Buat ulang index yang diperlukan
        try {
            $this->db->query("ALTER TABLE minha_layanan_informasi ADD INDEX idx_judul (judul)");
            $this->db->query("ALTER TABLE minha_layanan_informasi ADD FULLTEXT idx_search (judul, deskripsi)");
        } catch (\Exception $e) {
            // Index sudah ada atau gagal dibuat, lanjutkan
        }
    }
}
