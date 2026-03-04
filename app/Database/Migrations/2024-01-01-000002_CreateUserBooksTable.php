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

            // ── Foreign keys ──────────────────────────────
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

            // ── Reading status ────────────────────────────
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['want_to_read', 'reading', 'finished'],
                'default'    => 'want_to_read',
            ],

            // ── Book type — determines how page count works ──
            // physical  → total_pages from Google Books API via ISBN
            // epub      → total_pages calculated from uploaded EPUB file
            'book_type' => [
                'type'       => 'ENUM',
                'constraint' => ['physical', 'epub'],
                'default'    => 'physical',
            ],

            // ── Progress tracking ─────────────────────────
            // For physical books: stores current page number e.g. "150"
            // For EPUB: stores CFI string e.g. "epubcfi(/6/4[chap01]!/4/2/2/2)"
            'current_position' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'comment'    => 'Page number for physical, CFI for epub',
            ],

            // Current page as integer for easy % calculation
            'current_page' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => true,
            ],

            // ── EPUB file ─────────────────────────────────
            // Relative path from WRITEPATH e.g. "uploads/epubs/user_1/book.epub"
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'comment'    => 'Relative path from WRITEPATH for epub files',
            ],

            // ── Rating 1-5 ────────────────────────────────
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 4,      // 1–5 stars
                'unsigned'   => true,
                'null'       => true,
            ],

            // ── Personal notes ────────────────────────────
            'personal_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // ── Reading dates ─────────────────────────────
            'started_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'finished_at' => [
                'type' => 'DATE',
                'null' => true,
            ],

            // ── Timestamps ───────────────────────────────
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

        // Prevent a user from adding the same book twice
        $this->forge->addUniqueKey(['user_id', 'book_id']);

        // Foreign keys — Shield uses 'users' table
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');

        // NOTE: table name is 'user_books' (with underscore)
        $this->forge->createTable('user_books', true);
    }

    public function down()
    {
        // Drop foreign keys first to avoid constraint errors
        $this->forge->dropTable('user_books', true);
    }
}
