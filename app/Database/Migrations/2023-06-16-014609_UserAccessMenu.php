<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserAccessMenu extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'menu_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('menu_id', 'user_menu', 'id');
        $this->forge->addForeignKey('role_id', 'user_role', 'id');
        $this->forge->createTable('user_access_menu');
    }

    public function down()
    {
        $this->forge->dropTable('user_access_menu');
    }
}
