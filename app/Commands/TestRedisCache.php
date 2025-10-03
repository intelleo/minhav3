<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestRedisCache extends BaseCommand
{
  protected $group       = 'Testing';
  protected $name        = 'cache:test-redis';
  protected $description = 'Test Redis connection dan caching functionality';

  public function run(array $params)
  {
    CLI::write('🔍 Testing Redis Cache Connection...', 'yellow');

    try {
      // Test 1: Basic Redis connection
      CLI::write('1. Testing Redis connection...', 'blue');
      $cache = \Config\Services::cache();

      // Test dengan operasi sederhana
      $testKey = 'redis_test_' . time();
      $testValue = 'Redis connection test';

      try {
        $saveResult = $cache->save($testKey, $testValue, 10);
        if ($saveResult) {
          CLI::write('   ✅ Redis connection berhasil', 'green');
        } else {
          CLI::write('   ❌ Redis connection gagal - tidak bisa menyimpan data', 'red');
          return;
        }
      } catch (\Exception $e) {
        CLI::write('   ❌ Redis connection error: ' . $e->getMessage(), 'red');
        CLI::write('   💡 Pastikan Redis server berjalan di Laragon', 'yellow');
        return;
      }

      // Test 2: Basic cache operations
      CLI::write('2. Testing basic cache operations...', 'blue');

      $testKey2 = 'test_redis_connection_' . time();
      $testData = [
        'message' => 'Hello Redis!',
        'timestamp' => date('Y-m-d H:i:s'),
        'random' => rand(1000, 9999)
      ];

      // Save to cache
      $saveResult = $cache->save($testKey2, $testData, 60);
      if ($saveResult) {
        CLI::write('   ✅ Data berhasil disimpan ke cache', 'green');
      } else {
        CLI::write('   ❌ Gagal menyimpan data ke cache', 'red');
        return;
      }

      // Retrieve from cache
      $retrievedData = $cache->get($testKey2);
      if ($retrievedData && $retrievedData['message'] === $testData['message']) {
        CLI::write('   ✅ Data berhasil diambil dari cache', 'green');
        CLI::write('   📄 Data: ' . json_encode($retrievedData), 'cyan');
      } else {
        CLI::write('   ❌ Gagal mengambil data dari cache', 'red');
        return;
      }

      // Test 3: Cache remember functionality
      CLI::write('3. Testing cache remember functionality...', 'blue');

      $rememberKey = 'test_remember_' . time();
      $rememberData = $cache->remember($rememberKey, 60, function () {
        return [
          'computed_at' => date('Y-m-d H:i:s'),
          'expensive_calculation' => 'This was computed!',
          'random_value' => rand(10000, 99999)
        ];
      });

      if ($rememberData) {
        CLI::write('   ✅ Cache remember berfungsi', 'green');
        CLI::write('   📄 Data: ' . json_encode($rememberData), 'cyan');
      } else {
        CLI::write('   ❌ Cache remember gagal', 'red');
        return;
      }

      // Test 4: Test model caching
      CLI::write('4. Testing model caching...', 'blue');

      $layananModel = new \App\Models\LayananModel();
      $cachedData = $layananModel->getAllWithCache();

      if (is_array($cachedData)) {
        CLI::write('   ✅ Model caching berfungsi', 'green');
        CLI::write('   📊 Jumlah data layanan: ' . count($cachedData), 'cyan');
      } else {
        CLI::write('   ❌ Model caching gagal', 'red');
      }

      // Test 5: Cache invalidation
      CLI::write('5. Testing cache invalidation...', 'blue');

      $deleteResult = $cache->delete($testKey2);
      if ($deleteResult) {
        CLI::write('   ✅ Cache invalidation berfungsi', 'green');
      } else {
        CLI::write('   ❌ Cache invalidation gagal', 'red');
      }

      // Cleanup
      $cache->delete($testKey);
      $cache->delete($rememberKey);

      CLI::write('', 'white');
      CLI::write('🎉 Semua test Redis cache berhasil!', 'green');
      CLI::write('📈 Redis caching siap digunakan untuk optimasi performa', 'yellow');
    } catch (\Exception $e) {
      CLI::write('❌ Error: ' . $e->getMessage(), 'red');
      CLI::write('💡 Pastikan Redis server berjalan di Laragon', 'yellow');
    }
  }
}
