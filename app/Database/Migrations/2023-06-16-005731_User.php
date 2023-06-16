<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
                'unique'     => true,
            ],
            'image' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint' => 256,
            ],
            'role_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'is_active' => [
                'type'           => 'BOOLEAN',
                'default'        => false
            ],
            'date_created' => [
                'type'           => 'VARCHAR',
                'constraint' => 128,
            ],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'user_role', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user');
    }

    public function down()
    {
        $this->forge->dropTable('user');
    }
}
