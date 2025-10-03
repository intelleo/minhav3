<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'judul'     => 'Panduan KRS Online',
                'deskripsi' => 'Panduan lengkap melakukan pengisian Kartu Rencana Studi (KRS) melalui portal akademik.',
                'kategori'  => 'Akademik',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'judul'     => 'Prosedur KHS',
                'deskripsi' => 'Langkah-langkah melihat dan mencetak Kartu Hasil Studi (KHS) mahasiswa.',
                'kategori'  => 'Akademik',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'judul'     => 'Jadwal Registrasi Ulang',
                'deskripsi' => 'Tanggal dan ketentuan registrasi ulang mahasiswa untuk semester baru.',
                'kategori'  => 'Administrasi',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'judul'     => 'Pengumuman Lomba Desain Poster',
                'deskripsi' => 'Lomba desain poster untuk seluruh mahasiswa dengan hadiah menarik.',
                'kategori'  => 'Mading',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'judul'     => 'Panduan Beasiswa',
                'deskripsi' => 'Informasi jenis-jenis beasiswa dan cara mendaftarnya.',
                'kategori'  => 'Umum',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('layanan_informasi')->insertBatch($data);
    }
}
