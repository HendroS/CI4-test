<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserSubMenu extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'title' => [
                'type'           => 'VARCHAR',
                'constraint'       => 128,
            ],
            'url' => [
                'type'           => 'VARCHAR',
                'constraint'       => 128,
            ],
            'icon' => [
                'type'           => 'VARCHAR',
                'constraint'       => 128,
            ],
            'is_active' => [
                'type'           => 'BOOLEAN',
                'default'       => true,
            ],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('menu_id', 'user_menu', 'id');
        $this->forge->createTable('user_sub_menu');
    }

    public function down()
    {
        $this->forge->dropTable('user_sub_menu');
    }
}
