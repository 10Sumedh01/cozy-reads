<?php

namespace App\Libraries;

/**
 * EpubUploader
 *
 * Handles EPUB file upload, validation, storage, and
 * page count calculation by parsing the EPUB's spine/manifest.
 *
 * Usage:
 *   $uploader = new \App\Libraries\EpubUploader();
 *   $result   = $uploader->upload($request->getFile('epub_file'), $userId);
 *
 *   if ($result['success']) {
 *       $filePath  = $result['file_path'];   // store in user_books.file_path
 *       $pageCount = $result['page_count'];  // store in books.total_pages
 *   } else {
 *       $error = $result['error'];
 *   }
 */
class EpubUploader
{
    // Where EPUBs are stored (relative to WRITEPATH)
    private string $uploadDir = 'uploads/epubs/';

    // Max file size: 50 MB
    private int $maxSize = 50 * 1024 * 1024;

    // Average words per page (used for page count estimation)
    private int $wordsPerPage = 250;

    // ─────────────────────────────────────────────────────
    //  Main upload method
    //  Returns array with 'success', 'file_path', 'page_count', 'error'
    // ─────────────────────────────────────────────────────
    public function upload(\CodeIgniter\HTTP\Files\UploadedFile $file, int $userId): array
    {
        // 1. Validate
        $validation = $this->validate($file);
        if (! $validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }

        // 2. Build user-specific upload directory
        $userDir  = $this->uploadDir . 'user_' . $userId . '/';
        $fullPath = WRITEPATH . $userDir;

        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // 3. Generate safe unique filename
        $fileName = $this->sanitizeFilename($file->getClientName());
        $fileName = time() . '_' . $fileName;

        // 4. Move file to destination
        if (! $file->move($fullPath, $fileName)) {
            return ['success' => false, 'error' => 'Failed to save file. Check writable permissions.'];
        }

        $storedPath = $userDir . $fileName;

        // 5. Calculate page count from EPUB content
        $pageCount = $this->calculatePageCount($fullPath . $fileName);

        return [
            'success'    => true,
            'file_path'  => $storedPath,   // relative from WRITEPATH
            'page_count' => $pageCount,
            'file_size'  => $file->getSize(),
            'file_name'  => $fileName,
        ];
    }

    // ─────────────────────────────────────────────────────
    //  Validate uploaded file
    // ─────────────────────────────────────────────────────
    private function validate(\CodeIgniter\HTTP\Files\UploadedFile $file): array
    {
        if (! $file->isValid()) {
            return ['valid' => false, 'error' => $file->getErrorString()];
        }

        if ($file->hasMoved()) {
            return ['valid' => false, 'error' => 'File has already been moved.'];
        }

        // Check MIME type — EPUB is essentially a ZIP
        $allowedMimes = ['application/epub+zip', 'application/zip', 'application/octet-stream'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            return ['valid' => false, 'error' => 'Only .epub files are allowed.'];
        }

        // Check extension
        if (strtolower($file->getClientExtension()) !== 'epub') {
            return ['valid' => false, 'error' => 'File must have .epub extension.'];
        }

        // Check file size
        if ($file->getSize() > $this->maxSize) {
            return ['valid' => false, 'error' => 'File size must be under 50 MB.'];
        }

        return ['valid' => true, 'error' => null];
    }

    // ─────────────────────────────────────────────────────
    //  Calculate page count by reading EPUB content
    //
    //  EPUB is a ZIP archive containing HTML files.
    //  We extract all HTML, strip tags, count words,
    //  then divide by average words-per-page.
    // ─────────────────────────────────────────────────────
    public function calculatePageCount(string $filePath): int
    {
        if (! file_exists($filePath)) return 0;

        // EPUB files are ZIP archives
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) return 0;

        $totalWords = 0;

        // Read the OPF file to get the correct spine order
        $opfPath = $this->findOpfPath($zip);

        if ($opfPath) {
            $spineFiles = $this->getSpineFiles($zip, $opfPath);
            foreach ($spineFiles as $htmlFile) {
                $content = $zip->getFromName($htmlFile);
                if ($content !== false) {
                    $totalWords += $this->countWords($content);
                }
            }
        } else {
            // Fallback: read all HTML/XHTML files in the archive
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/\.(html|xhtml|htm)$/i', $name)) {
                    $content = $zip->getFromIndex($i);
                    if ($content !== false) {
                        $totalWords += $this->countWords($content);
                    }
                }
            }
        }

        $zip->close();

        if ($totalWords === 0) return 0;

        // Estimate pages based on average words per page
        $estimated = (int) ceil($totalWords / $this->wordsPerPage);

        // Sanity bounds: at least 1 page, max 5000
        return max(1, min($estimated, 5000));
    }

    // ─────────────────────────────────────────────────────
    //  Find the OPF (Open Packaging Format) file path
    //  by reading META-INF/container.xml
    // ─────────────────────────────────────────────────────
    private function findOpfPath(\ZipArchive $zip): ?string
    {
        $container = $zip->getFromName('META-INF/container.xml');
        if ($container === false) return null;

        // Parse XML to find rootfile path
        $xml = @simplexml_load_string($container);
        if (! $xml) return null;

        $namespaces = $xml->getNamespaces(true);
        $ns         = $namespaces[''] ?? '';

        foreach ($xml->rootfiles->rootfile ?? [] as $rootfile) {
            $attrs = $rootfile->attributes();
            if (isset($attrs['full-path'])) {
                return (string) $attrs['full-path'];
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────
    //  Parse OPF to get spine file paths in reading order
    // ─────────────────────────────────────────────────────
    private function getSpineFiles(\ZipArchive $zip, string $opfPath): array
    {
        $opfContent = $zip->getFromName($opfPath);
        if ($opfContent === false) return [];

        $xml = @simplexml_load_string($opfContent);
        if (! $xml) return [];

        // Base directory of the OPF file
        $baseDir = dirname($opfPath);
        if ($baseDir === '.') $baseDir = '';
        else $baseDir .= '/';

        // Build id → href map from manifest
        $manifest = [];
        foreach ($xml->manifest->item ?? [] as $item) {
            $attrs = $item->attributes();
            $id    = (string) ($attrs['id']         ?? '');
            $href  = (string) ($attrs['href']        ?? '');
            $type  = (string) ($attrs['media-type']  ?? '');

            if ($id && $href && str_contains($type, 'html')) {
                $manifest[$id] = $baseDir . $href;
            }
        }

        // Follow spine order
        $spineFiles = [];
        foreach ($xml->spine->itemref ?? [] as $itemref) {
            $attrs = $itemref->attributes();
            $idref = (string) ($attrs['idref'] ?? '');
            if (isset($manifest[$idref])) {
                $spineFiles[] = $manifest[$idref];
            }
        }

        return $spineFiles;
    }

    // ─────────────────────────────────────────────────────
    //  Strip HTML tags and count words in content
    // ─────────────────────────────────────────────────────
    private function countWords(string $html): int
    {
        // Remove script and style blocks
        $html = preg_replace('/<(script|style)[^>]*>.*?<\/\1>/si', '', $html);

        // Strip all HTML tags
        $text = strip_tags($html);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', trim($text));

        if (empty($text)) return 0;

        return str_word_count($text);
    }

    // ─────────────────────────────────────────────────────
    //  Sanitize filename — remove dangerous characters
    // ─────────────────────────────────────────────────────
    private function sanitizeFilename(string $filename): string
    {
        // Keep only alphanumeric, dash, underscore, dot
        $name      = pathinfo($filename, PATHINFO_FILENAME);
        $name      = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        $name      = preg_replace('/_+/', '_', $name); // collapse multiple underscores
        return $name . '.epub';
    }

    // ─────────────────────────────────────────────────────
    //  Delete an EPUB file (e.g. when user removes book)
    // ─────────────────────────────────────────────────────
    public function delete(string $filePath): bool
    {
        $fullPath = WRITEPATH . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    // ─────────────────────────────────────────────────────
    //  Get full server path from stored relative path
    // ─────────────────────────────────────────────────────
    public function getFullPath(string $filePath): string
    {
        return WRITEPATH . $filePath;
    }
}
