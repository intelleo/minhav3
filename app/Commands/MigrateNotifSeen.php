<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateNotifSeen extends BaseCommand
{
  protected $group       = 'Database';
  protected $name        = 'notif:migrate-seen';
  protected $description = 'Migrate existing notif_seen_ids from sessions to database';

  public function run(array $params)
  {
    $db = \Config\Database::connect();

    CLI::write('Memulai migrasi notif_seen_ids dari session ke database...', 'yellow');

    // Ambil semua session files
    $sessionPath = WRITEPATH . 'session/';
    $sessionFiles = glob($sessionPath . 'ci_session*');

    $migratedCount = 0;
    $errorCount = 0;

    foreach ($sessionFiles as $sessionFile) {
      try {
        $sessionData = file_get_contents($sessionFile);

        // Parse session data untuk mencari notif_seen_ids
        if (preg_match('/notif_seen_ids\|a:(\d+):\{([^}]+)\}/', $sessionData, $matches)) {
          $count = (int) $matches[1];
          $data = $matches[2];

          // Extract user_id dari session
          if (preg_match('/user_id\|s:\d+:"(\d+)"/', $sessionData, $userMatches)) {
            $userId = (int) $userMatches[1];

            // Parse array data
            if (preg_match_all('/i:\d+;i:(\d+);/', $data, $idMatches)) {
              $commentIds = array_map('intval', $idMatches[1]);

              // Insert ke database
              foreach ($commentIds as $commentId) {
                $db->table('notif_seen')->ignore(true)->insert([
                  'user_id' => $userId,
                  'comment_id' => $commentId,
                  'created_at' => date('Y-m-d H:i:s'),
                ]);
              }

              $migratedCount += count($commentIds);
              CLI::write("User ID {$userId}: " . count($commentIds) . " notifikasi migrated", 'green');
            }
          }
        }
      } catch (\Exception $e) {
        $errorCount++;
        CLI::write("Error processing {$sessionFile}: " . $e->getMessage(), 'red');
      }
    }

    CLI::write("Migrasi selesai!", 'yellow');
    CLI::write("Total migrated: {$migratedCount} records", 'green');
    CLI::write("Errors: {$errorCount} files", $errorCount > 0 ? 'red' : 'green');
  }
}
