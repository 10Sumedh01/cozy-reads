<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBookModel extends Model
{
    protected $table            = 'user_books';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'book_id',
        'status',
        'book_type',
        'current_position',
        'current_page',
        'file_path',
        'rating',
        'personal_notes',
        'started_at',
        'finished_at',
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
        'user_id'   => 'required|integer',
        'book_id'   => 'required|integer',
        'status'    => 'required|in_list[want_to_read,reading,finished]',
        'book_type' => 'required|in_list[physical,epub]',
        'rating'    => 'permit_empty|integer|greater_than[0]|less_than[6]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
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
    //  Get user's full library with book details joined
    //  Optional status filter: 'want_to_read' | 'reading' | 'finished'
    // ─────────────────────────────────────────────────────
    public function getUserLibrary(int $userId, ?string $status = null): array
    {
        $builder = $this
            ->select('user_books.*, books.title, books.author, books.cover_url,
                      books.total_pages, books.genre, books.isbn, books.language')
            ->join('books', 'books.id = user_books.book_id')
            ->where('user_books.user_id', $userId)
            ->orderBy('user_books.updated_at', 'DESC');

        if ($status) {
            $builder->where('user_books.status', $status);
        }

        return $builder->findAll();
    }

    // ─────────────────────────────────────────────────────
    //  Get currently reading book for dashboard hero card
    // ─────────────────────────────────────────────────────
    public function getCurrentRead(int $userId): ?array
    {
        return $this
            ->select('user_books.*, books.title, books.author, books.cover_url,
                      books.total_pages, books.isbn')
            ->join('books', 'books.id = user_books.book_id')
            ->where('user_books.user_id', $userId)
            ->where('user_books.status', 'reading')
            ->orderBy('user_books.updated_at', 'DESC')
            ->first();
    }

    // ─────────────────────────────────────────────────────
    //  Update reading progress (current page + auto-status)
    // ─────────────────────────────────────────────────────
    public function updateProgress(int $userBookId, int $currentPage, ?string $cfiPosition = null): bool
    {
        $userBook = $this->find($userBookId);
        if (! $userBook) return false;

        $data = [
            'current_page' => $currentPage,
        ];

        // Store CFI string for EPUB, or page number string for physical
        if ($cfiPosition) {
            $data['current_position'] = $cfiPosition;
        } else {
            $data['current_position'] = (string) $currentPage;
        }

        // Auto set started_at on first progress update
        if (empty($userBook['started_at']) && $currentPage > 0) {
            $data['started_at'] = date('Y-m-d');
            $data['status']     = 'reading';
        }

        // Auto set finished when on last page
        // Requires joining books.total_pages — handled in controller
        return $this->update($userBookId, $data);
    }

    // ─────────────────────────────────────────────────────
    //  Reading stats for stats page
    // ─────────────────────────────────────────────────────
    public function getStats(int $userId): array
    {
        $db = \Config\Database::connect();

        return [
            // Total books finished
            'finished_count' => $this
                ->where('user_id', $userId)
                ->where('status', 'finished')
                ->countAllResults(),

            // Total pages read (sum of total_pages for finished books)
            'total_pages' => (int) $db->table('user_books ub')
                ->select('SUM(b.total_pages) as total')
                ->join('books b', 'b.id = ub.book_id')
                ->where('ub.user_id', $userId)
                ->where('ub.status', 'finished')
                ->get()->getRow()->total,

            // Favourite genre
            'favourite_genre' => $db->table('user_books ub')
                ->select('b.genre, COUNT(*) as cnt')
                ->join('books b', 'b.id = ub.book_id')
                ->where('ub.user_id', $userId)
                ->where('ub.status', 'finished')
                ->whereNotNull('b.genre')
                ->groupBy('b.genre')
                ->orderBy('cnt', 'DESC')
                ->limit(1)
                ->get()->getRowArray()['genre'] ?? 'N/A',

            // Books finished this year
            'finished_this_year' => $this
                ->where('user_id', $userId)
                ->where('status', 'finished')
                ->where('YEAR(finished_at)', date('Y'))
                ->countAllResults(),
        ];
    }

    // ─────────────────────────────────────────────────────
    //  Check if user already has this book
    // ─────────────────────────────────────────────────────
    public function userHasBook(int $userId, int $bookId): bool
    {
        return $this
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first() !== null;
    }
}
