<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserBooksTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'book_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['want_to_read', 'reading', 'finished'],
                'default'    => 'want_to_read',
            ],
            'current_position' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', // Stores Page number or EPUB CFI
                'null'       => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', // Path to PDF/EPUB on server
                'null'       => true,
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'personal_notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        
        // Use Shield's default users table 'users'
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('user_books');
    }

    public function down()
    {
        $this->forge->dropTable('user_books');
    }
}