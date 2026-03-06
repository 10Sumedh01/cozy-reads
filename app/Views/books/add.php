<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Book — Cozy Reads</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg:           #F3E8DF;
      --surface:      #FFFFFF;
      --surface2:     #FAF3EE;
      --surface3:     #E8D1C5;
      --border:       #DEC5B5;
      --border2:      #CDB09E;
      --accent:       #452829;
      --accent-hover: #5c3535;
      --accent-light: rgba(69,40,41,0.08);
      --charcoal:     #57595B;
      --text:         #2d1a1a;
      --text2:        #57595B;
      --text3:        #9a8070;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: var(--bg); color: var(--text); font-family: 'Crimson Pro', serif; min-height: 100vh; }

    /* ── TOPBAR ── */
    .topbar {
      height: 60px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center;
      padding: 0 24px; gap: 16px;
      position: sticky; top: 0; z-index: 10;
      box-shadow: 0 1px 8px rgba(69,40,41,0.06);
    }
    .back-btn {
      display: flex; align-items: center; gap: 6px;
      text-decoration: none;
      color: var(--text2); font-size: 14px;
      padding: 6px 12px; border-radius: 6px;
      border: 1px solid var(--border);
      transition: all 0.2s;
    }
    .back-btn:hover { border-color: var(--border2); color: var(--text); }
    .topbar-title { font-family: 'DM Serif Display', serif; font-size: 18px; }

    /* ── LAYOUT ── */
    .page-wrap {
      max-width: 860px;
      margin: 0 auto;
      padding: 32px 24px 60px;
    }

    .two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 28px;
    }
    @media (max-width: 640px) { .two-col { grid-template-columns: 1fr; } }

    /* ── PANELS ── */
    .panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 24px;
    }
    .panel-title {
      font-family: 'DM Serif Display', serif;
      font-size: 17px;
      margin-bottom: 18px;
      color: var(--text);
      display: flex; align-items: center; gap: 8px;
    }

    /* ── FORM FIELDS ── */
    .field { margin-bottom: 14px; }
    .field label {
      display: block;
      font-size: 11px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--text3);
      margin-bottom: 5px;
    }
    input[type=text], input[type=number], input[type=search],
    textarea, select {
      background: var(--surface2);
      border: 1px solid var(--border2);
      color: var(--text);
      font-family: 'Crimson Pro', serif;
      font-size: 15px;
      padding: 9px 13px;
      border-radius: 7px;
      width: 100%;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    input:focus, textarea:focus, select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(69,40,41,0.08);
    }
    input::placeholder, textarea::placeholder { color: var(--text3); }
    select option { background: var(--surface); }
    textarea { resize: vertical; min-height: 80px; line-height: 1.5; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    /* ── BOOK TYPE TOGGLE ── */
    .type-toggle {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 16px;
    }
    .type-option { display: none; }
    .type-label {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      gap: 6px;
      padding: 14px 12px;
      border: 2px solid var(--border);
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.2s;
      text-align: center;
      background: var(--surface2);
    }
    .type-label:hover { border-color: var(--border2); }
    .type-label .icon { font-size: 24px; }
    .type-label .label-text { font-size: 13px; color: var(--text2); font-weight: 600; }
    .type-label .label-sub  { font-size: 11px; color: var(--text3); }
    .type-option:checked + .type-label {
      border-color: var(--accent);
      background: var(--accent-light);
    }
    .type-option:checked + .type-label .label-text { color: var(--accent); }

    /* ── SEARCH ── */
    .search-wrap { position: relative; margin-bottom: 12px; }
    .search-wrap input { padding-right: 80px; }
    .search-btn {
      position: absolute; right: 6px; top: 50%;
      transform: translateY(-50%);
      background: var(--accent); color: var(--bg);
      border: none; border-radius: 5px;
      padding: 5px 12px;
      font-family: 'Crimson Pro', serif;
      font-size: 13px; cursor: pointer;
      transition: background 0.2s;
    }
    .search-btn:hover { background: var(--accent-hover); }

    /* Search results */
    .search-results { display: none; margin-bottom: 14px; }
    .search-result-item {
      display: flex; gap: 10px;
      padding: 10px;
      border: 1px solid var(--border);
      border-radius: 8px;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
      margin-bottom: 6px;
      background: var(--surface2);
    }
    .search-result-item:hover { border-color: var(--accent); background: var(--accent-light); }
    .search-result-item.selected { border-color: var(--accent); background: var(--accent-light); }
    .result-cover {
      width: 38px; min-width: 38px; height: 56px;
      border-radius: 2px 5px 5px 2px;
      background: linear-gradient(135deg, var(--accent), #8b4545);
      object-fit: cover;
    }
    .result-cover img { width: 100%; height: 100%; object-fit: cover; border-radius: 2px 4px 4px 2px; }
    .result-info { flex: 1; min-width: 0; }
    .result-title  { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .result-author { font-size: 12px; color: var(--text3); font-style: italic; }
    .result-meta   { font-size: 11px; color: var(--text3); margin-top: 2px; }

    .search-loading {
      display: none;
      text-align: center;
      padding: 12px;
      font-size: 13px;
      color: var(--text3);
      font-style: italic;
    }
    .search-empty {
      display: none;
      text-align: center;
      padding: 12px;
      font-size: 13px;
      color: var(--text3);
      font-style: italic;
    }
    .divider {
      display: flex; align-items: center; gap: 10px;
      font-size: 12px; color: var(--text3);
      margin-bottom: 14px;
    }
    .divider::before, .divider::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* ── EPUB UPLOAD ── */
    .epub-drop {
      border: 2px dashed var(--border2);
      border-radius: 10px;
      padding: 24px 16px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      background: var(--surface2);
    }
    .epub-drop:hover, .epub-drop.dragover {
      border-color: var(--accent);
      background: var(--accent-light);
    }
    .epub-drop-icon { font-size: 32px; margin-bottom: 8px; }
    .epub-drop-text { font-size: 14px; color: var(--text2); font-weight: 600; margin-bottom: 4px; }
    .epub-drop-sub  { font-size: 12px; color: var(--text3); }
    .epub-selected  {
      display: none;
      align-items: center; gap: 10px;
      padding: 10px 14px;
      background: rgba(69,40,41,0.06);
      border: 1px solid var(--accent);
      border-radius: 8px;
      font-size: 13px;
      color: var(--accent);
      margin-top: 8px;
    }
    .epub-selected.show { display: flex; }

    /* ── BOOK PREVIEW ── */
    .book-preview {
      display: none;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 14px;
      margin-bottom: 14px;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 8px;
    }
    .book-preview.show { display: flex; }
    .preview-cover {
      width: 70px; height: 100px;
      border-radius: 2px 6px 6px 2px;
      background: linear-gradient(135deg, var(--accent), #8b4545);
      object-fit: cover;
      box-shadow: 3px 4px 12px rgba(69,40,41,0.2);
    }
    .preview-title  { font-family: 'DM Serif Display', serif; font-size: 15px; color: var(--text); }
    .preview-author { font-size: 13px; color: var(--text3); font-style: italic; }
    .preview-pages  { font-size: 12px; color: var(--accent); }

    /* ── SHELF SELECTOR ── */
    .shelf-selector {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
      margin-bottom: 14px;
    }
    .shelf-option { display: none; }
    .shelf-label {
      display: flex; flex-direction: column;
      align-items: center; gap: 4px;
      padding: 10px 6px;
      border: 1px solid var(--border);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
      text-align: center;
      background: var(--surface2);
      font-size: 12px;
      color: var(--text3);
    }
    .shelf-label:hover { border-color: var(--border2); color: var(--text2); }
    .shelf-label .shelf-icon { font-size: 18px; }
    .shelf-option:checked + .shelf-label {
      border-color: var(--accent);
      background: var(--accent-light);
      color: var(--accent);
    }

    /* ── ERRORS ── */
    .alert-errors {
      background: rgba(69,40,41,0.06);
      border: 1px solid rgba(69,40,41,0.2);
      border-left: 3px solid var(--accent);
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 18px;
    }
    .alert-errors p { font-size: 13px; color: var(--accent); margin-bottom: 2px; }
    .alert-errors p:last-child { margin-bottom: 0; }

    /* ── SUBMIT ── */
    .btn-submit {
      width: 100%;
      background: var(--accent);
      color: var(--bg);
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-family: 'Crimson Pro', serif;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      margin-top: 4px;
    }
    .btn-submit:hover { background: var(--accent-hover); transform: translateY(-1px); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* hidden epub section */
    #epubSection { display: none; }
  </style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <a href="<?= base_url('dashboard') ?>" class="back-btn">← Back</a>
  <div class="topbar-title">Add New Book</div>
</header>

<div class="page-wrap">

  <!-- Errors -->
  <?php if (! empty($errors)): ?>
    <div class="alert-errors">
      <?php foreach ($errors as $error): ?>
        <p>⚠ <?= esc($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('books/add') ?>" method="POST" enctype="multipart/form-data" id="addBookForm">
    <?= csrf_field() ?>

    <!-- Hidden fields — populated by JS when user picks from search results -->
    <input type="hidden" name="cover_url"      id="cover_url">
    <input type="hidden" name="description"    id="description">
    <input type="hidden" name="genre"          id="genre">
    <input type="hidden" name="publisher"      id="publisher">
    <input type="hidden" name="published_date" id="published_date">
    <input type="hidden" name="language"       id="language" value="en">

    <div class="two-col">

      <!-- ══ LEFT PANEL — Search & Book Details ══ -->
      <div>
        <div class="panel">
          <div class="panel-title">📖 Book Details</div>

          <!-- Book type toggle -->
          <div class="field">
            <label>Book Type</label>
            <div class="type-toggle">
              <div>
                <input type="radio" name="book_type" id="type_physical" value="physical" class="type-option" checked onchange="toggleBookType()">
                <label for="type_physical" class="type-label">
                  <span class="icon">📗</span>
                  <span class="label-text">Physical</span>
                  <span class="label-sub">ISBN lookup</span>
                </label>
              </div>
              <div>
                <input type="radio" name="book_type" id="type_epub" value="epub" class="type-option" onchange="toggleBookType()">
                <label for="type_epub" class="type-label">
                  <span class="icon">📱</span>
                  <span class="label-text">EPUB</span>
                  <span class="label-sub">Upload file</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Search -->
          <div class="field">
            <label>Search by Title, Author or ISBN</label>
            <div class="search-wrap">
              <input type="search" id="searchInput" placeholder="e.g. Dune, Frank Herbert, 9780441172719...">
              <button type="button" class="search-btn" onclick="searchBooks()">Search</button>
            </div>
          </div>

          <!-- Search loading / empty / results -->
          <div class="search-loading" id="searchLoading">🔍 Searching Google Books...</div>
          <div class="search-empty"   id="searchEmpty">No results found. Try a different search.</div>
          <div class="search-results" id="searchResults"></div>

          <div class="divider">or enter manually</div>

          <!-- Book preview (shown after selecting from search) -->
          <div class="book-preview" id="bookPreview">
            <img class="preview-cover" id="previewCoverImg" src="" alt="">
            <div class="preview-title"  id="previewTitle"></div>
            <div class="preview-author" id="previewAuthor"></div>
            <div class="preview-pages"  id="previewPages"></div>
          </div>

          <!-- Manual fields -->
          <div class="field">
            <label>Title <span style="color:var(--accent)">*</span></label>
            <input type="text" name="title" id="title" placeholder="Book title" value="<?= old('title') ?>">
          </div>

          <div class="field">
            <label>Author(s)</label>
            <input type="text" name="author" id="author" placeholder="Author name(s)" value="<?= old('author') ?>">
          </div>

          <div class="form-row">
            <div class="field">
              <label>ISBN</label>
              <input type="text" name="isbn" id="isbn" placeholder="978..." value="<?= old('isbn') ?>">
            </div>
            <div class="field">
              <label>Total Pages</label>
              <input type="number" name="total_pages" id="total_pages" placeholder="Auto-filled" value="<?= old('total_pages') ?>">
            </div>
          </div>

          <!-- EPUB upload section (shown when epub type selected) -->
          <div id="epubSection">
            <div class="field">
              <label>EPUB File <span style="color:var(--accent)">*</span></label>
              <div class="epub-drop" id="epubDrop" onclick="document.getElementById('epub_file').click()">
                <div class="epub-drop-icon">📄</div>
                <div class="epub-drop-text">Drop your EPUB here</div>
                <div class="epub-drop-sub">or click to browse · max 50MB</div>
              </div>
              <input type="file" name="epub_file" id="epub_file" accept=".epub" style="display:none" onchange="onEpubSelected(this)">
              <div class="epub-selected" id="epubSelected">
                <span>📄</span>
                <span id="epubFileName">No file selected</span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- ══ RIGHT PANEL — Shelf & Save ══ -->
      <div>
        <div class="panel">
          <div class="panel-title">📚 Add to Shelf</div>

          <!-- Shelf selector -->
          <div class="field">
            <label>Which shelf?</label>
            <div class="shelf-selector">
              <div>
                <input type="radio" name="status" id="shelf_want" value="want_to_read" class="shelf-option" checked>
                <label for="shelf_want" class="shelf-label">
                  <span class="shelf-icon">🔖</span>
                  Want to Read
                </label>
              </div>
              <div>
                <input type="radio" name="status" id="shelf_reading" value="reading" class="shelf-option">
                <label for="shelf_reading" class="shelf-label">
                  <span class="shelf-icon">📖</span>
                  Reading
                </label>
              </div>
              <div>
                <input type="radio" name="status" id="shelf_finished" value="finished" class="shelf-option">
                <label for="shelf_finished" class="shelf-label">
                  <span class="shelf-icon">✅</span>
                  Finished
                </label>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-submit" id="submitBtn">
            Add to My Library 📚
          </button>
        </div>

        <!-- Tips card -->
        <div class="panel" style="margin-top:16px; background:var(--surface2);">
          <div class="panel-title" style="font-size:14px; color:var(--text2);">💡 Tips</div>
          <div style="font-size:13px; color:var(--text3); line-height:1.7;">
            <p>• Search by <strong>ISBN</strong> for the most accurate page count.</p>
            <p style="margin-top:6px;">• For <strong>EPUB</strong> books, page count is calculated automatically from the file.</p>
            <p style="margin-top:6px;">• Physical book pages are fetched from <strong>Google Books API</strong>.</p>
          </div>
        </div>
      </div>

    </div>
  </form>
</div>

<script>
  // ── Book type toggle ──────────────────────────────────
  function toggleBookType() {
    const isEpub = document.getElementById('type_epub').checked;
    document.getElementById('epubSection').style.display = isEpub ? 'block' : 'none';
  }

  // ── Google Books search ───────────────────────────────
  let searchTimeout;

  // Also trigger search on Enter key
  document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchBooks(); }
  });

  async function searchBooks() {
    const q = document.getElementById('searchInput').value.trim();
    if (q.length < 2) return;

    // Show loading
    document.getElementById('searchLoading').style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchEmpty').style.display   = 'none';

    try {
      const res  = await fetch(`<?= base_url('books/search') ?>?q=${encodeURIComponent(q)}`);
      const data = await res.json();

      document.getElementById('searchLoading').style.display = 'none';

      if (! data.success || data.results.length === 0) {
        document.getElementById('searchEmpty').style.display = 'block';
        return;
      }

      renderResults(data.results);

    } catch (err) {
      document.getElementById('searchLoading').style.display = 'none';
      document.getElementById('searchEmpty').style.display   = 'block';
    }
  }

  function renderResults(results) {
    const container = document.getElementById('searchResults');
    container.innerHTML = results.map((b, i) => `
      <div class="search-result-item" onclick="selectBook(${i})" id="result_${i}" data-index="${i}">
        <div class="result-cover">
          ${b.cover_url
            ? `<img src="${b.cover_url}" alt="${b.title}">`
            : ''}
        </div>
        <div class="result-info">
          <div class="result-title">${b.title}</div>
          <div class="result-author">${b.author}</div>
          <div class="result-meta">
            ${b.total_pages ? `📄 ${b.total_pages} pages` : ''}
            ${b.isbn ? ` · ISBN: ${b.isbn}` : ''}
          </div>
        </div>
      </div>
    `).join('');

    container.style.display = 'block';

    // Store results for selectBook()
    window._searchResults = results;
  }

  function selectBook(index) {
    const b = window._searchResults[index];

    // Highlight selected
    document.querySelectorAll('.search-result-item').forEach(el => el.classList.remove('selected'));
    document.getElementById('result_' + index).classList.add('selected');

    // Fill form fields
    document.getElementById('title').value       = b.title       ?? '';
    document.getElementById('author').value      = b.author      ?? '';
    document.getElementById('isbn').value        = b.isbn        ?? '';
    document.getElementById('total_pages').value = b.total_pages ?? '';

    // Fill hidden fields
    document.getElementById('cover_url').value      = b.cover_url      ?? '';
    document.getElementById('description').value    = b.description    ?? '';
    document.getElementById('genre').value          = b.genre          ?? '';
    document.getElementById('publisher').value      = b.publisher      ?? '';
    document.getElementById('published_date').value = b.published_date ?? '';
    document.getElementById('language').value       = b.language       ?? 'en';

    // Show book preview
    const preview = document.getElementById('bookPreview');
    document.getElementById('previewTitle').textContent  = b.title  ?? '';
    document.getElementById('previewAuthor').textContent = b.author ?? '';
    document.getElementById('previewPages').textContent  = b.total_pages ? `${b.total_pages} pages` : '';

    const img = document.getElementById('previewCoverImg');
    if (b.cover_url) {
      img.src   = b.cover_url;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }
    preview.classList.add('show');
  }

  // ── EPUB file selected ────────────────────────────────
  function onEpubSelected(input) {
    const file     = input.files[0];
    const selected = document.getElementById('epubSelected');
    if (file) {
      document.getElementById('epubFileName').textContent = file.name;
      selected.classList.add('show');
    } else {
      selected.classList.remove('show');
    }
  }

  // ── Drag and drop on epub drop zone ──────────────────
  const epubDrop = document.getElementById('epubDrop');
  if (epubDrop) {
    epubDrop.addEventListener('dragover', e => {
      e.preventDefault();
      epubDrop.classList.add('dragover');
    });
    epubDrop.addEventListener('dragleave', () => epubDrop.classList.remove('dragover'));
    epubDrop.addEventListener('drop', e => {
      e.preventDefault();
      epubDrop.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      if (file && file.name.endsWith('.epub')) {
        const input = document.getElementById('epub_file');
        const dt    = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        onEpubSelected(input);
      }
    });
  }
</script>

</body>
</html>
