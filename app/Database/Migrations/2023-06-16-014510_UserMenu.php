<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserMenu extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu' => [
                'type'           => 'VARCHAR',
                'constraint' => 64,
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_menu');
    }

    public function down()
    {
        $this->forge->dropTable('user_menu');
    }
}
