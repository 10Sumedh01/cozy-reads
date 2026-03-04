<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooksTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'author' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'isbn' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Cover — either a URL from Google Books API or a local uploaded file path
            'cover_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],

            // Page count — fetched from Google Books API (physical) or
            // calculated from the EPUB file itself
            'total_pages' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'From Google Books API for physical, calculated for EPUB',
            ],

            // Genre / category from Google Books API
            'genre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            // Publisher info
            'publisher' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'published_date' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,   // "2001-08-01" or just "2001"
                'null'       => true,
            ],

            // Language
            'language' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'en',
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
        $this->forge->addUniqueKey('isbn');
        $this->forge->createTable('books', true);
    }

    public function down()
    {
        $this->forge->dropTable('books', true);
    }
}
