<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLayananKategoriToUmumBaakBuak extends Migration
{
    public function up()
    {
        // Update data yang ada terlebih dahulu
        // Akademik -> BAAK
        $this->db->query("UPDATE minha_layanan_informasi SET kategori = 'BAAK' WHERE kategori = 'Akademik'");

        // Administrasi -> BUAK  
        $this->db->query("UPDATE minha_layanan_informasi SET kategori = 'BUAK' WHERE kategori = 'Administrasi'");

        // Umum tetap Umum (tidak perlu diubah)

        // Ubah tipe data ENUM dari ['Akademik', 'Administrasi', 'Umum'] menjadi ['Umum', 'BAAK', 'BUAK']
        $this->db->query("ALTER TABLE minha_layanan_informasi MODIFY COLUMN kategori ENUM('Umum', 'BAAK', 'BUAK')");
    }

    public function down()
    {
        // Kembalikan data ke kategori lama
        // BAAK -> Akademik
        $this->db->query("UPDATE minha_layanan_informasi SET kategori = 'Akademik' WHERE kategori = 'BAAK'");

        // BUAK -> Administrasi
        $this->db->query("UPDATE minha_layanan_informasi SET kategori = 'Administrasi' WHERE kategori = 'BUAK'");

        // Ubah kembali tipe data ENUM ke ['Akademik', 'Administrasi', 'Umum']
        $this->db->query("ALTER TABLE minha_layanan_informasi MODIFY COLUMN kategori ENUM('Akademik', 'Administrasi', 'Umum')");
    }
}
