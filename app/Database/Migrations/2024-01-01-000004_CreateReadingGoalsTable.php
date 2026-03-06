<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReadingGoalsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // FK to Shield's users table
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // annual_books  = how many books to finish in a year
            // monthly_pages = how many pages to read in a month
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['annual_books', 'monthly_pages'],
            ],

            // The goal number e.g. 20 books or 1000 pages
            'target' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // Which year this goal applies to e.g. 2025
            'year' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'unsigned'   => true,
            ],

            // Only used when type = monthly_pages (1-12)
            // NULL when type = annual_books
            'month' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Prevent duplicate goals:
        // user can only have ONE annual_books goal per year
        // user can only have ONE monthly_pages goal per year+month
        $this->forge->addUniqueKey(['user_id', 'type', 'year', 'month']);

        // Cascade delete — goals deleted when user is deleted
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('reading_goals', true);
    }

    public function down()
    {
        $this->forge->dropTable('reading_goals', true);
    }
}
