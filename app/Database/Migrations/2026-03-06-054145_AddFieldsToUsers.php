<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
                'after'      => 'username',
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female', 'other', 'prefer_not_to_say'],
                'null'       => true,
                'after'      => 'mobile',
            ],
            'profile_pic' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => 'default.png',
                'after'      => 'gender',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['mobile', 'gender', 'profile_pic']);
    }
}