<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserRole extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_role');
    }

    public function down()
    {
        $this->forge->dropTable('user_role');
    }
}
