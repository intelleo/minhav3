<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    // Jalankan semua seeder utama
    $this->call('AuthAdminSeeder');
    // $this->call('UserAuthSeeder');
    $this->call('MadingSeeder');
    $this->call('LayananSeeder');
  }
}
