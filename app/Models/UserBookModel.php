<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBookModel extends Model
{
    protected $table            = 'userbooks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'book_id',
        'status',
        'current_position',
        'file_path',
        'rating',
        'personal_notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    /**
     * Get a user's full library with book details joined
     */
    public function getUserLibrary(int $userId, ?string $status = null)
    {
        $builder = $this->select('user_books.*, books.title, books.author, books.cover_url')
            ->join('books', 'books.id = user_books.book_id')
            ->where('user_id', $userId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }
    // Validation
    protected $validationRules      = [];
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
}
