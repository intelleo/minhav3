<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddViewsToMadingOnline extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mading_online', [
            'views' => [
                'type'       => 'INT',
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('mading_online', 'views');
    }
}
    