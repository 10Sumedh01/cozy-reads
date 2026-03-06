<?php

// app/Models/ReadingGoalModel.php

namespace App\Models;

use CodeIgniter\Model;

class ReadingGoalModel extends Model
{
    protected $table            = 'reading_goals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'type',
        'target',
        'year',
        'month',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'type'    => 'required|in_list[annual_books,monthly_pages]',
        'target'  => 'required|integer|greater_than[0]',
        'year'    => 'required|integer',
        'month'   => 'permit_empty|integer|greater_than[0]|less_than[13]',
    ];

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
    //  Get a specific goal for a user
    //  month is null for annual_books goals
    // ─────────────────────────────────────────────────────
    public function getGoal(int $userId, string $type, int $year, ?int $month = null): ?array
    {
        return $this
            ->where('user_id', $userId)
            ->where('type', $type)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    // ─────────────────────────────────────────────────────
    //  Create or update a goal (upsert)
    // ─────────────────────────────────────────────────────
    public function saveGoal(int $userId, string $type, int $target, int $year, ?int $month = null): bool
    {
        $existing = $this->getGoal($userId, $type, $year, $month);

        if ($existing) {
            return $this->update($existing['id'], ['target' => $target]);
        }

        return $this->insert([
            'user_id' => $userId,
            'type'    => $type,
            'target'  => $target,
            'year'    => $year,
            'month'   => $month,
        ]) !== false;
    }
}
