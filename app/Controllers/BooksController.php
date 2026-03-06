<?php

// app/Controllers/BooksController.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Models\UserBookModel;
use App\Libraries\EpubUploader;

class BooksController extends BaseController
{
    protected BookModel     $bookModel;
    protected UserBookModel $userBookModel;

    public function __construct()
    {
        $this->bookModel     = new BookModel();
        $this->userBookModel = new UserBookModel();
    }

    // ─────────────────────────────────────────────────────
    //  GET books/add
    //  Show the add book form
    // ─────────────────────────────────────────────────────
    public function addView(): string
    {
        return view('books/add', [
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    // ─────────────────────────────────────────────────────
    //  POST books/add
    //  Handle form submission — save book + user_book record
    // ─────────────────────────────────────────────────────
    public function addAction()
    {
        $userId = auth()->id();

        // ── Validation ────────────────────────────────────
        $rules = [
            'title'     => 'required|min_length[1]|max_length[255]',
            'author'    => 'permit_empty|max_length[255]',
            'isbn'      => 'permit_empty|min_length[10]|max_length[20]',
            'status'    => 'required|in_list[want_to_read,reading,finished]',
            'book_type' => 'required|in_list[physical,epub]',
        ];

        $messages = [
            'title' => [
                'required'   => 'Please enter a book title.',
                'max_length' => 'Title is too long.',
            ],
            'status' => [
                'required' => 'Please select a shelf.',
                'in_list'  => 'Invalid shelf selected.',
            ],
            'book_type' => [
                'required' => 'Please select book type.',
                'in_list'  => 'Invalid book type.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $bookType = $this->request->getPost('book_type');

        // ── Handle EPUB upload if book_type is epub ───────
        $filePath  = null;
        $epubPages = null;

        if ($bookType === 'epub') {
            $epubFile = $this->request->getFile('epub_file');

            if (! $epubFile || ! $epubFile->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['epub_file' => 'Please upload a valid .epub file.']);
            }

            $uploader = new EpubUploader();
            $result   = $uploader->upload($epubFile, $userId);

            if (! $result['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['epub_file' => $result['error']]);
            }

            $filePath  = $result['file_path'];
            $epubPages = $result['page_count'];
        }

        // ── Build book data ───────────────────────────────
        // total_pages: use epub calculated pages, or manual input, or Google Books data
        $totalPages = $epubPages
            ?? (int) $this->request->getPost('total_pages')
            ?: null;

        $bookData = [
            'title'          => $this->request->getPost('title'),
            'author'         => $this->request->getPost('author'),
            'isbn'           => $this->request->getPost('isbn') ?: null,
            'description'    => $this->request->getPost('description') ?: null,
            'cover_url'      => $this->request->getPost('cover_url') ?: null,
            'total_pages'    => $totalPages,
            'genre'          => $this->request->getPost('genre') ?: null,
            'publisher'      => $this->request->getPost('publisher') ?: null,
            'published_date' => $this->request->getPost('published_date') ?: null,
            'language'       => $this->request->getPost('language') ?: 'en',
        ];

        // ── Save book (or find existing by ISBN) ──────────
        // findOrCreate prevents duplicate books in the books table
        $bookId = $this->bookModel->findOrCreate($bookData);

        // ── Check if user already has this book ───────────
        if ($this->userBookModel->userHasBook($userId, $bookId)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['title' => 'This book is already in your library.']);
        }

        // ── Build user_book data ──────────────────────────
        $status = $this->request->getPost('status');

        $userBookData = [
            'user_id'   => $userId,
            'book_id'   => $bookId,
            'status'    => $status,
            'book_type' => $bookType,
            'file_path' => $filePath,
        ];

        // Auto set started_at if status is 'reading'
        if ($status === 'reading') {
            $userBookData['started_at'] = date('Y-m-d');
        }

        // Auto set started_at + finished_at if status is 'finished'
        if ($status === 'finished') {
            $userBookData['started_at']  = date('Y-m-d');
            $userBookData['finished_at'] = date('Y-m-d');
        }

        $this->userBookModel->insert($userBookData);

        return redirect()->to('/dashboard')
            ->with('message', '"' . $bookData['title'] . '" added to your library! 📚');
    }

    // ─────────────────────────────────────────────────────
    //  GET books/search?q=harry+potter
    //  AJAX endpoint — searches Google Books API
    //  Returns JSON array of book results
    // ─────────────────────────────────────────────────────
    public function search()
    {
        $query = trim($this->request->getGet('q') ?? '');

        if (strlen($query) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Search query too short.',
                'results' => [],
            ]);
        }

        // Build Google Books API URL
        $isIsbn   = preg_match('/^[\d\-]{10,17}$/', $query);
        $apiQuery = $isIsbn ? 'isbn:' . $query : urlencode($query);
        $apiUrl   = 'https://www.googleapis.com/books/v1/volumes?q=' . $apiQuery . '&maxResults=5&langRestrict=en';
        $apiKey = env('GOOGLE_BOOKS_API_KEY');
        if ($apiKey) {
            $apiUrl .= '&key=' . $apiKey;
        }

        // Fetch using file_get_contents — simpler than curl, works on XAMPP
        $json = @file_get_contents($apiUrl);

        if ($json === false) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Could not reach Google Books API.',
                'results' => [],
            ]);
        }

        $data  = json_decode($json, true);
        $data = json_decode($json, true);

        // Check for API errors (rate limit, invalid key etc)
        if (isset($data['error'])) {
            $code = $data['error']['code'] ?? 0;
            $msg  = match ($code) {
                429     => 'Search limit reached. Please try again later.',
                403     => 'Google Books API key issue. Contact support.',
                default => 'Google Books API error. Please try again.',
            };
            return $this->response->setJSON([
                'success' => false,
                'error'   => $msg,
                'results' => [],
            ]);
        }

        $items = $data['items'] ?? [];
        $items = $data['items'] ?? [];

        // No results found
        if (empty($items)) {
            return $this->response->setJSON([
                'success' => true,
                'results' => [],
            ]);
        }

        // Format results
        $results = [];
        foreach ($items as $item) {
            $info = $item['volumeInfo'] ?? [];

            $isbn = null;
            foreach ($info['industryIdentifiers'] ?? [] as $id) {
                if ($id['type'] === 'ISBN_13') {
                    $isbn = $id['identifier'];
                    break;
                }
                if ($id['type'] === 'ISBN_10') {
                    $isbn = $id['identifier'];
                }
            }

            $results[] = [
                'title'          => $info['title']                        ?? 'Unknown Title',
                'author'         => implode(', ', $info['authors'] ?? []) ?: 'Unknown Author',
                'isbn'           => $isbn,
                'cover_url'      => $info['imageLinks']['thumbnail']      ?? null,
                'total_pages'    => $info['pageCount']                    ?? null,
                'description'    => $info['description']                  ?? null,
                'genre'          => $info['categories'][0]                ?? null,
                'publisher'      => $info['publisher']                    ?? null,
                'published_date' => $info['publishedDate']                ?? null,
                'language'       => $info['language']                     ?? 'en',
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'results' => $results,
        ]);
    }

    // ─────────────────────────────────────────────────────
    //  GET books/(:num)
    //  Single book detail page
    // ─────────────────────────────────────────────────────
    public function show(int $userBookId): string
    {
        $userId   = auth()->id();
        $userBook = $this->userBookModel
            ->select('user_books.*, books.title, books.author, books.cover_url,
                      books.total_pages, books.isbn, books.description,
                      books.genre, books.publisher, books.published_date')
            ->join('books', 'books.id = user_books.book_id')
            ->where('user_books.id', $userBookId)
            ->where('user_books.user_id', $userId) // security — own books only
            ->first();

        if (! $userBook) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('books/show', [
            'book'   => $userBook,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    // ─────────────────────────────────────────────────────
    //  POST books/progress/(:num)
    //  Update current page — called from dashboard form
    // ─────────────────────────────────────────────────────
    public function updateProgress(int $userBookId)
    {
        $userId   = auth()->id();
        $userBook = $this->userBookModel
            ->where('id', $userBookId)
            ->where('user_id', $userId) // security check
            ->first();

        if (! $userBook) {
            return redirect()->to('/dashboard')
                ->with('errors', ['general' => 'Book not found.']);
        }

        $currentPage = (int) $this->request->getPost('current_page');
        $totalPages  = (int) ($userBook['total_pages'] ?? 0);

        // Clamp value between 0 and total pages
        if ($totalPages > 0) {
            $currentPage = max(0, min($currentPage, $totalPages));
        }

        $updateData = [
            'current_page'     => $currentPage,
            'current_position' => (string) $currentPage,
        ];

        // Auto set status to 'reading' on first progress update
        if ($userBook['status'] === 'want_to_read' && $currentPage > 0) {
            $updateData['status']     = 'reading';
            $updateData['started_at'] = date('Y-m-d');
        }

        // Auto mark as finished when on last page
        if ($totalPages > 0 && $currentPage >= $totalPages) {
            $updateData['status']      = 'finished';
            $updateData['finished_at'] = date('Y-m-d');
        }

        $this->userBookModel->update($userBookId, $updateData);

        // Redirect back to where the form was submitted from
        $redirectTo = $this->request->getPost('redirect') ?? '/dashboard';

        return redirect()->to($redirectTo)
            ->with('message', 'Progress updated! 📖');
    }

    // ─────────────────────────────────────────────────────
    //  POST books/status/(:num)
    //  Change shelf status (want_to_read / reading / finished)
    // ─────────────────────────────────────────────────────
    public function updateStatus(int $userBookId)
    {
        $userId = auth()->id();
        $status = $this->request->getPost('status');

        $validStatuses = ['want_to_read', 'reading', 'finished'];
        if (! in_array($status, $validStatuses)) {
            return redirect()->back()->with('errors', ['status' => 'Invalid status.']);
        }

        $userBook = $this->userBookModel
            ->where('id', $userBookId)
            ->where('user_id', $userId)
            ->first();

        if (! $userBook) {
            return redirect()->to('/dashboard');
        }

        $updateData = ['status' => $status];

        if ($status === 'reading' && empty($userBook['started_at'])) {
            $updateData['started_at'] = date('Y-m-d');
        }

        if ($status === 'finished' && empty($userBook['finished_at'])) {
            $updateData['finished_at'] = date('Y-m-d');
        }

        $this->userBookModel->update($userBookId, $updateData);

        return redirect()->back()->with('message', 'Shelf updated!');
    }

    // ─────────────────────────────────────────────────────
    //  POST books/delete/(:num)
    //  Remove a book from user's library
    //  Does NOT delete from books table — other users may have it
    // ─────────────────────────────────────────────────────
    public function delete(int $userBookId)
    {
        $userId   = auth()->id();
        $userBook = $this->userBookModel
            ->where('id', $userBookId)
            ->where('user_id', $userId)
            ->first();

        if (! $userBook) {
            return redirect()->to('/shelves');
        }

        // If it was an epub — delete the file from disk too
        if ($userBook['book_type'] === 'epub' && ! empty($userBook['file_path'])) {
            $uploader = new EpubUploader();
            $uploader->delete($userBook['file_path']);
        }

        $this->userBookModel->delete($userBookId);

        return redirect()->to('/shelves')
            ->with('message', 'Book removed from your library.');
    }
}
