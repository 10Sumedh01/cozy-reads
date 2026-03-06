<?php

// app/Controllers/DashboardController.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserBookModel;
use App\Models\BookModel;
use App\Models\ReadingGoalModel;

class DashboardController extends BaseController
{
    protected UserBookModel    $userBookModel;
    protected BookModel        $bookModel;
    protected ReadingGoalModel $goalModel;

    public function __construct()
    {
        $this->userBookModel = new UserBookModel();
        $this->bookModel     = new BookModel();
        $this->goalModel     = new ReadingGoalModel();
    }

    // ─────────────────────────────────────────────────────
    //  Main dashboard page
    // ─────────────────────────────────────────────────────
    public function index(): string
    {
        $userId = auth()->id();

        // ── 1. Current read ───────────────────────────────
        $currentRead = $this->userBookModel->getCurrentRead($userId);

        // ── 2. Reading stats ──────────────────────────────
        $stats = $this->userBookModel->getStats($userId);

        // ── 3. Recently added books (all shelves, max 8) ──
        $recentBooks = array_slice(
            $this->userBookModel->getUserLibrary($userId),
            0, 8
        );

        // ── 4. Reading goals ──────────────────────────────
        $annualGoal  = $this->goalModel->getGoal($userId, 'annual_books',  (int) date('Y'));
        $monthlyGoal = $this->goalModel->getGoal($userId, 'monthly_pages', (int) date('Y'), (int) date('m'));

        // ── 5. Goal progress ──────────────────────────────
        $annualProgress  = $this->calculateAnnualProgress($userId, $annualGoal);
        $monthlyProgress = $this->calculateMonthlyProgress($userId, $monthlyGoal);

        // ── 6. Is new user (no books added yet)? ──────────
        $isNewUser = $this->userBookModel
            ->where('user_id', $userId)
            ->countAllResults() === 0;

        return view('dashboard/index', [
            'currentRead'     => $currentRead,
            'stats'           => $stats,
            'recentBooks'     => $recentBooks,
            'annualGoal'      => $annualGoal,
            'monthlyGoal'     => $monthlyGoal,
            'annualProgress'  => $annualProgress,
            'monthlyProgress' => $monthlyProgress,
            'isNewUser'       => $isNewUser,
            'user'            => auth()->user(),
        ]);
    }

    // ─────────────────────────────────────────────────────
    //  Annual books goal progress
    // ─────────────────────────────────────────────────────
    private function calculateAnnualProgress(int $userId, ?array $goal): array
    {
        $current = $this->userBookModel
            ->where('user_id', $userId)
            ->where('status', 'finished')
            ->where('YEAR(finished_at)', date('Y'))
            ->countAllResults();

        $target  = $goal['target'] ?? 20;
        $percent = $target > 0
            ? (int) min(100, round(($current / $target) * 100))
            : 0;

        return compact('current', 'target', 'percent');
    }

    // ─────────────────────────────────────────────────────
    //  Monthly pages goal progress
    // ─────────────────────────────────────────────────────
    private function calculateMonthlyProgress(int $userId, ?array $goal): array
    {
        $db = \Config\Database::connect();

        $row = $db->table('user_books')
            ->selectSum('current_page')
            ->where('user_id', $userId)
            ->where('MONTH(updated_at)', date('m'))
            ->where('YEAR(updated_at)',  date('Y'))
            ->get()
            ->getRow();

        $current = (int) ($row->current_page ?? 0);
        $target  = $goal['target'] ?? 1000;
        $percent = $target > 0
            ? (int) min(100, round(($current / $target) * 100))
            : 0;

        return compact('current', 'target', 'percent');
    }
}
