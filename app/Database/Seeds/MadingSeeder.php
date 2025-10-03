<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MadingSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Kosongkan tabel
        $db->table('mading_online')->emptyTable();
        $db->query('ALTER TABLE minha_mading_online AUTO_INCREMENT = 1');

        // Ambil admin_id dari auth_admin
        $admin = $db->table('auth_admin')
            ->select('id')
            ->where('username', 'minhaai')
            ->get()
            ->getRow();

        if (!$admin) {
            echo "❌ Admin 'minhaai' tidak ditemukan. Harap jalankan AuthAdminSeeder dulu.\n";
            return;
        }

        $adminId = $admin->id;

        // Data mading
        $madingData = [
            [
                'admin_id'    => $adminId,
                'judul'       => 'Workshop AI: Masa Depan Sudah di Depan Mata',
                'category'    => 'edukasi',
                'deskripsi'   => 'Workshop AI tingkat lanjut untuk mahasiswa yang ingin mendalami deep learning dan NLP.',
                'file'        => null,
                'tgl_mulai'   => '2025-09-01',
                'tgl_akhir'   => '2025-09-30',
                'status'      => 'aktif',
                'created_at'  => Time::now(),
                'updated_at'  => Time::now(),
            ],
            [
                'admin_id'    => $adminId,
                'judul'       => 'Pengumuman: Libur Nasional dan Cuti Bersama',
                'category'    => 'pengumuman',
                'deskripsi'   => 'Kampus akan libur mulai 17 September 2025 dalam rangka Hari Raya dan cuti bersama.',
                'file'        => 'uploads/mading/libur-2025.pdf',
                'tgl_mulai'   => '2025-09-17',
                'tgl_akhir'   => '2025-09-19',
                'status'      => 'aktif',
                'created_at'  => Time::now(),
                'updated_at'  => Time::now(),
            ],
            [
                'admin_id'    => $adminId,
                'judul'       => 'Event: Pameran Karya Mahasiswa 2025',
                'category'    => 'event',
                'deskripsi'   => 'Pameran karya mahasiswa dari berbagai jurusan akan digelar di aula utama kampus.',
                'file'        => null,
                'tgl_mulai'   => '2025-10-05',
                'tgl_akhir'   => '2025-10-07',
                'status'      => 'aktif',
                'created_at'  => Time::now(),
                'updated_at'  => Time::now(),
            ],
            [
                'admin_id'    => $adminId,
                'judul'       => 'Tips Belajar Efektif untuk UAS',
                'category'    => 'berita',
                'deskripsi'   => 'Beberapa tips dari dosen dan mahasiswa berprestasi untuk menghadapi UAS dengan tenang.',
                'file'        => null,
                'tgl_mulai'   => '2025-09-05',
                'tgl_akhir'   => '2025-09-25',
                'status'      => 'aktif',
                'created_at'  => Time::now(),
                'updated_at'  => Time::now(),
            ],
            [
                'admin_id'    => $adminId,
                'judul'       => 'Pelatihan Public Speaking Gratis',
                'category'    => 'edukasi',
                'deskripsi'   => 'Ikuti pelatihan public speaking yang diselenggarakan oleh BEM kampus. Terbuka untuk semua mahasiswa.',
                'file'        => 'uploads/mading/pelatihan-ps.pdf',
                'tgl_mulai'   => '2025-09-10',
                'tgl_akhir'   => '2025-09-14',
                'status'      => 'aktif',
                'created_at'  => Time::now(),
                'updated_at'  => Time::now(),
            ],
        ];

        // Insert data
        $db->table('mading_online')->insertBatch($madingData);

        echo "✅ Berhasil mengisi 5 data mading dengan admin 'minhaai'!\n";
    }
}
