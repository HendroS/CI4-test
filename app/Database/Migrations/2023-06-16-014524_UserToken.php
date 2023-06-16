<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserToken extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ],
            'token' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ],
            'date_created' => [
                'type'           => 'INT',
            ],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_token');
    }

    public function down()
    {
        $this->forge->dropTable('user_token');
    }
}
