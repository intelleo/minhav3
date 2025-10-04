<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestMadingCommentTable extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:mading-comments';
    protected $description = 'Test mading comments table structure and data';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $prefix = $db->getPrefix();

        // Check table structure
        CLI::write('Checking table structure...', 'yellow');
        $columns = $db->getFieldData($prefix . 'mading_comments');

        CLI::write('Columns:', 'green');
        foreach ($columns as $column) {
            CLI::write("- {$column->name}: {$column->type}");
        }

        // Check existing data
        CLI::write('\nSample data:', 'yellow');
        $comments = $db->table($prefix . 'mading_comments')
            ->select('id, user_id, user_type, isi_komentar')
            ->limit(5)
            ->get()
            ->getResult();

        foreach ($comments as $comment) {
            CLI::write("ID: {$comment->id}, User ID: {$comment->user_id}, Type: {$comment->user_type}, Comment: " . substr($comment->isi_komentar, 0, 50) . '...');
        }

        CLI::write('\nTest inserting admin comment...', 'yellow');

        try {
            $commentModel = new \App\Models\MadingCommentModel();
            $data = [
                'mading_id' => 1,
                'user_id' => 1,
                'user_type' => 'admin',
                'parent_id' => null,
                'isi_komentar' => 'Test admin comment - ' . date('Y-m-d H:i:s'),
            ];

            $id = $commentModel->insert($data, true);
            CLI::write("✅ Admin comment inserted successfully with ID: {$id}", 'green');
        } catch (\Throwable $e) {
            CLI::write("❌ Error inserting admin comment: " . $e->getMessage(), 'red');
        }
    }
}
