<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'title',
        'author',
        'isbn',
        'description',
        'cover_url',
        'total_pages',
        'genre',
        'publisher',
        'published_date',
        'language',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[1]|max_length[255]',
        'isbn'  => 'permit_empty|min_length[10]|max_length[20]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // ── Callbacks ─────────────────────────────────────────
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ─────────────────────────────────────────────────────
    //  Find existing book by ISBN or create a new one
    //  Returns the book ID in both cases
    // ─────────────────────────────────────────────────────
    public function findOrCreate(array $data): int
    {
        // Try to find by ISBN first (most reliable)
        if (! empty($data['isbn'])) {
            $book = $this->where('isbn', $data['isbn'])->first();
            if ($book) {
                return (int) $book['id'];
            }
        }

        // Fallback: find by title + author
        if (! empty($data['title']) && ! empty($data['author'])) {
            $book = $this->where('title', $data['title'])
                         ->where('author', $data['author'])
                         ->first();
            if ($book) {
                return (int) $book['id'];
            }
        }

        // Book doesn't exist — insert it
        $this->insert($data);
        return (int) $this->getInsertID();
    }

    // ─────────────────────────────────────────────────────
    //  Build book data array from Google Books API response
    // ─────────────────────────────────────────────────────
    public static function fromGoogleBooks(array $volumeInfo): array
    {
        return [
            'title'          => $volumeInfo['title'] ?? 'Unknown Title',
            'author'         => isset($volumeInfo['authors'])
                                    ? implode(', ', $volumeInfo['authors'])
                                    : null,
            'isbn'           => self::extractIsbn($volumeInfo['industryIdentifiers'] ?? []),
            'description'    => $volumeInfo['description'] ?? null,
            'cover_url'      => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'total_pages'    => $volumeInfo['pageCount'] ?? null,
            'genre'          => isset($volumeInfo['categories'])
                                    ? $volumeInfo['categories'][0]
                                    : null,
            'publisher'      => $volumeInfo['publisher'] ?? null,
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'language'       => $volumeInfo['language'] ?? 'en',
        ];
    }

    // ─────────────────────────────────────────────────────
    //  Extract ISBN-13 (preferred) or ISBN-10 from API data
    // ─────────────────────────────────────────────────────
    private static function extractIsbn(array $identifiers): ?string
    {
        $isbn13 = null;
        $isbn10 = null;

        foreach ($identifiers as $id) {
            if ($id['type'] === 'ISBN_13') $isbn13 = $id['identifier'];
            if ($id['type'] === 'ISBN_10') $isbn10 = $id['identifier'];
        }

        return $isbn13 ?? $isbn10;
    }
}
