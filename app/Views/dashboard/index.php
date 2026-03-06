<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Cozy Reads</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
  <style>
    /* ── PALETTE ──────────────────────────────────────────
       #452829  deep burgundy  → accent / buttons / active
       #57595B  charcoal grey  → secondary text / icons
       #E8D1C5  blush beige    → surfaces / inputs
       #F3E8DF  linen white    → page background
    ────────────────────────────────────────────────────── */
    :root {
      --bg:            #F3E8DF;
      --surface:       #FFFFFF;
      --surface2:      #FAF3EE;
      --surface3:      #E8D1C5;
      --border:        #DEC5B5;
      --border2:       #CDB09E;
      --accent:        #452829;
      --accent-hover:  #5c3535;
      --accent-light:  rgba(69,40,41,0.08);
      --charcoal:      #57595B;
      --text:          #2d1a1a;
      --text2:         #57595B;
      --text3:         #9a8070;
      --sidebar-w:     240px;
      --topbar-h:      60px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Crimson Pro', serif;
      min-height: 100vh;
      display: flex;
    }

    /* ════════════════════════════════════════════════════
       SIDEBAR
    ════════════════════════════════════════════════════ */
    .sidebar {
      width: var(--sidebar-w);
      min-width: var(--sidebar-w);
      background: var(--accent);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 0;
      position: fixed;
      left: 0; top: 0; bottom: 0;
      z-index: 200;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      box-shadow: 4px 0 24px rgba(69,40,41,0.25);
    }
    .sidebar.open { transform: translateX(0); }

    .sidebar-header {
      padding: 24px 20px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .sidebar-logo {
      font-family: 'DM Serif Display', serif;
      font-size: 22px;
      color: var(--bg);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar-close {
      background: transparent;
      border: none;
      color: rgba(243,232,223,0.6);
      font-size: 22px;
      cursor: pointer;
      line-height: 1;
      padding: 4px;
      transition: color 0.2s;
    }
    .sidebar-close:hover { color: var(--bg); }

    /* User card inside sidebar */
    .sidebar-user {
      padding: 16px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .sidebar-avatar {
      width: 40px; height: 40px;
      border-radius: 50%;
      background: var(--surface3);
      border: 2px solid rgba(255,255,255,0.2);
      object-fit: cover;
      display: flex; align-items: center; justify-content: center;
      font-family: 'DM Serif Display', serif;
      font-size: 16px;
      color: var(--accent);
      overflow: hidden;
    }
    .sidebar-username { font-size: 15px; color: var(--bg); font-weight: 600; }
    .sidebar-email    { font-size: 12px; color: rgba(243,232,223,0.55); }

    /* Nav items */
    .sidebar-nav { flex: 1; padding: 12px 12px; }
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: 8px;
      text-decoration: none;
      color: rgba(243,232,223,0.75);
      font-size: 15px;
      transition: all 0.2s;
      margin-bottom: 2px;
      border: 1px solid transparent;
    }
    .nav-item:hover {
      background: rgba(255,255,255,0.08);
      color: var(--bg);
    }
    .nav-item.active {
      background: rgba(255,255,255,0.15);
      color: var(--bg);
      border-color: rgba(255,255,255,0.15);
    }
    .nav-icon { font-size: 17px; width: 22px; text-align: center; }

    .sidebar-bottom {
      padding: 12px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .nav-item.logout { color: rgba(243,232,223,0.5); }
    .nav-item.logout:hover { color: #ffaaaa; background: rgba(255,100,100,0.1); }

    /* Sidebar overlay */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(45,26,26,0.45);
      backdrop-filter: blur(2px);
      z-index: 199;
    }
    .sidebar-overlay.open { display: block; }

    /* ════════════════════════════════════════════════════
       MAIN LAYOUT
    ════════════════════════════════════════════════════ */
    .main-wrap {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      width: 100%;
    }

    /* ── TOP BAR ── */
    .topbar {
      height: var(--topbar-h);
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 8px rgba(69,40,41,0.06);
    }

    .topbar-left { display: flex; align-items: center; gap: 16px; }

    /* Hamburger menu button — top left */
    .menu-btn {
      width: 38px; height: 38px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--surface2);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 5px;
      transition: all 0.2s;
    }
    .menu-btn:hover { border-color: var(--border2); background: var(--surface3); }
    .menu-btn span {
      display: block;
      width: 16px; height: 1.5px;
      background: var(--accent);
      border-radius: 2px;
      transition: all 0.2s;
    }

    .topbar-title {
      font-family: 'DM Serif Display', serif;
      font-size: 18px;
      color: var(--text);
    }

    .topbar-right { display: flex; align-items: center; gap: 10px; }

    /* Add book button — top right */
    .btn-add-book {
      display: flex;
      align-items: center;
      gap: 7px;
      background: var(--accent);
      color: var(--bg);
      border: none;
      border-radius: 8px;
      padding: 9px 16px;
      font-family: 'Crimson Pro', serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      letter-spacing: 0.02em;
    }
    .btn-add-book:hover { background: var(--accent-hover); transform: translateY(-1px); }
    .btn-add-book .plus { font-size: 18px; line-height: 1; }

    /* ── PAGE CONTENT ── */
    .page-content {
      flex: 1;
      padding: 28px 28px 48px;
      max-width: 1100px;
      width: 100%;
      margin: 0 auto;
    }

    /* ════════════════════════════════════════════════════
       GREETING
    ════════════════════════════════════════════════════ */
    .greeting {
      margin-bottom: 24px;
    }
    .greeting-title {
      font-family: 'DM Serif Display', serif;
      font-size: 26px;
      color: var(--text);
      margin-bottom: 3px;
    }
    .greeting-sub {
      font-size: 15px;
      color: var(--text3);
      font-style: italic;
    }

    /* ════════════════════════════════════════════════════
       EMPTY STATE
    ════════════════════════════════════════════════════ */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 80px 20px;
      background: var(--surface);
      border: 1px dashed var(--border2);
      border-radius: 16px;
      margin-top: 8px;
    }
    .empty-icon { font-size: 64px; margin-bottom: 20px; opacity: 0.7; }
    .empty-title {
      font-family: 'DM Serif Display', serif;
      font-size: 24px;
      color: var(--text);
      margin-bottom: 8px;
    }
    .empty-sub {
      font-size: 15px;
      color: var(--text3);
      max-width: 360px;
      line-height: 1.6;
      margin-bottom: 28px;
      font-style: italic;
    }

    /* ════════════════════════════════════════════════════
       CURRENT READ CARD
    ════════════════════════════════════════════════════ */
    .current-read {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 22px;
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
      position: relative;
      overflow: hidden;
    }
    .current-read::before {
      content: '';
      position: absolute;
      top: 0; right: 0;
      width: 180px; height: 100%;
      background: radial-gradient(ellipse at right, rgba(69,40,41,0.05) 0%, transparent 70%);
    }

    .book-cover-lg {
      width: 85px; min-width: 85px; height: 124px;
      border-radius: 3px 8px 8px 3px;
      background: linear-gradient(135deg, var(--accent) 0%, #6b3535 100%);
      box-shadow: 4px 4px 16px rgba(69,40,41,0.3), inset -2px 0 4px rgba(0,0,0,0.15);
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; color: rgba(255,255,255,0.4);
      text-align: center; padding: 8px;
      position: relative; overflow: hidden;
      flex-shrink: 0;
    }
    .book-cover-lg img {
      width: 100%; height: 100%;
      object-fit: cover;
      border-radius: 2px 6px 6px 2px;
    }
    .book-cover-lg::before {
      content: '';
      position: absolute; left: 0; top: 0; bottom: 0; width: 5px;
      background: rgba(0,0,0,0.2);
      border-radius: 3px 0 0 3px;
    }

    .current-read-info { flex: 1; min-width: 0; }
    .current-label {
      font-size: 10px;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 5px;
      font-weight: 600;
    }
    .current-title {
      font-family: 'DM Serif Display', serif;
      font-size: 20px;
      color: var(--text);
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .current-author {
      font-size: 13px;
      color: var(--text3);
      margin-bottom: 14px;
      font-style: italic;
    }

    .progress-label-row {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: var(--text3);
      margin-bottom: 6px;
    }
    .progress-label-row strong { color: var(--accent); }
    .progress-bar {
      height: 6px;
      background: var(--surface3);
      border-radius: 3px;
      overflow: hidden;
      margin-bottom: 12px;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--accent), #8b4545);
      border-radius: 3px;
      transition: width 0.5s ease;
    }

    .progress-update {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .progress-update span { font-size: 13px; color: var(--text3); }
    .page-input {
      width: 72px;
      background: var(--surface2);
      border: 1px solid var(--border2);
      color: var(--text);
      font-family: 'Crimson Pro', serif;
      font-size: 14px;
      padding: 5px 8px;
      border-radius: 6px;
      outline: none;
      transition: border-color 0.2s;
    }
    .page-input:focus { border-color: var(--accent); }
    .btn-sm {
      background: transparent;
      border: 1px solid var(--border2);
      color: var(--text2);
      font-family: 'Crimson Pro', serif;
      font-size: 13px;
      padding: 5px 12px;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-sm:hover { border-color: var(--accent); color: var(--accent); }
    .btn-sm.primary {
      background: var(--accent);
      border-color: var(--accent);
      color: var(--bg);
    }
    .btn-sm.primary:hover { background: var(--accent-hover); }

    /* no current read placeholder */
    .no-current-read {
      background: var(--surface);
      border: 1px dashed var(--border2);
      border-radius: 14px;
      padding: 22px;
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
      color: var(--text3);
      font-style: italic;
      font-size: 15px;
    }
    .no-current-read span { font-size: 32px; }

    /* ════════════════════════════════════════════════════
       STATS ROW
    ════════════════════════════════════════════════════ */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      margin-bottom: 24px;
    }
    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 18px 20px;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: default;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(69,40,41,0.08);
    }
    .stat-number {
      font-family: 'DM Serif Display', serif;
      font-size: 30px;
      color: var(--accent);
      line-height: 1;
      margin-bottom: 4px;
    }
    .stat-label { font-size: 12px; color: var(--text3); margin-bottom: 6px; }

    /* mini goal bar inside stat card */
    .stat-goal-bar { height: 3px; background: var(--surface3); border-radius: 2px; }
    .stat-goal-fill { height: 100%; background: var(--accent); border-radius: 2px; }
    .stat-goal-text { font-size: 11px; color: var(--text3); margin-top: 4px; }

    /* ════════════════════════════════════════════════════
       GOALS ROW
    ════════════════════════════════════════════════════ */
    .goals-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 28px;
    }
    .goal-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 18px 20px;
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .goal-ring { position: relative; width: 68px; height: 68px; flex-shrink: 0; }
    .goal-ring svg { transform: rotate(-90deg); }
    .goal-ring-text {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      font-family: 'DM Serif Display', serif;
      font-size: 15px;
      color: var(--accent);
    }
    .goal-info { flex: 1; min-width: 0; }
    .goal-title { font-family: 'DM Serif Display', serif; font-size: 15px; margin-bottom: 3px; color: var(--text); }
    .goal-sub { font-size: 12px; color: var(--text3); line-height: 1.4; }
    .goal-cta { font-size: 11px; color: var(--accent); margin-top: 5px; }

    /* ════════════════════════════════════════════════════
       SECTION HEADER
    ════════════════════════════════════════════════════ */
    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }
    .section-title {
      font-family: 'DM Serif Display', serif;
      font-size: 18px;
      color: var(--text);
    }
    .section-link {
      font-size: 13px;
      color: var(--accent);
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: border-color 0.2s;
    }
    .section-link:hover { border-bottom-color: var(--accent); }

    /* Shelf filter tabs */
    .shelf-tabs {
      display: flex;
      gap: 6px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }
    .shelf-tab {
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      border: 1px solid var(--border);
      color: var(--text3);
      transition: all 0.2s;
      font-family: 'Crimson Pro', serif;
      background: var(--surface);
    }
    .shelf-tab.active {
      background: var(--accent);
      border-color: var(--accent);
      color: var(--bg);
    }
    .shelf-tab:hover:not(.active) { border-color: var(--border2); color: var(--text2); }

    /* ════════════════════════════════════════════════════
       BOOK GRID
    ════════════════════════════════════════════════════ */
    .book-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
      gap: 16px;
      margin-bottom: 12px;
    }
    .book-card {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .book-card:hover { transform: translateY(-4px); }
    .book-card:hover .book-cover-sm { box-shadow: 4px 8px 20px rgba(69,40,41,0.25); }

    .book-cover-sm {
      width: 100%;
      aspect-ratio: 2/3;
      border-radius: 3px 7px 7px 3px;
      background: linear-gradient(135deg, var(--accent) 0%, #8b4545 100%);
      box-shadow: 3px 5px 12px rgba(69,40,41,0.2), inset -2px 0 3px rgba(0,0,0,0.1);
      display: flex; align-items: center; justify-content: center;
      font-size: 9px; color: rgba(255,255,255,0.45);
      text-align: center; padding: 6px;
      position: relative; overflow: hidden;
      margin-bottom: 7px;
    }
    .book-cover-sm img {
      width: 100%; height: 100%;
      object-fit: cover;
      border-radius: 2px 6px 6px 2px;
    }
    .book-cover-sm::before {
      content: '';
      position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
      background: rgba(0,0,0,0.15);
      border-radius: 3px 0 0 3px;
    }
    .epub-badge {
      position: absolute;
      bottom: 5px; right: 5px;
      background: rgba(69,40,41,0.85);
      color: #fff;
      font-size: 7px;
      padding: 2px 4px;
      border-radius: 3px;
      letter-spacing: 0.05em;
    }
    .status-dot {
      position: absolute;
      top: 5px; right: 5px;
      width: 8px; height: 8px;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,0.5);
    }
    .status-dot.reading  { background: #e8a87c; }
    .status-dot.finished { background: #7cae8a; }
    .status-dot.want     { background: #a0a0b0; }

    .book-title-sm {
      font-size: 12px; font-weight: 600;
      color: var(--text);
      line-height: 1.3;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .book-author-sm { font-size: 11px; color: var(--text3); font-style: italic; }

    /* ════════════════════════════════════════════════════
       FLASH MESSAGE
    ════════════════════════════════════════════════════ */
    .flash-success {
      background: rgba(124,174,138,0.15);
      border: 1px solid rgba(124,174,138,0.4);
      border-left: 3px solid #7cae8a;
      border-radius: 8px;
      padding: 10px 16px;
      font-size: 14px;
      color: #3a6645;
      margin-bottom: 20px;
    }

    /* ════════════════════════════════════════════════════
       RESPONSIVE
    ════════════════════════════════════════════════════ */
    @media (max-width: 640px) {
      .stats-row  { grid-template-columns: 1fr 1fr; }
      .goals-row  { grid-template-columns: 1fr; }
      .page-content { padding: 16px 16px 48px; }
      .current-title { font-size: 17px; }
    }
    @media (max-width: 420px) {
      .stats-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════════════════════════════
     SIDEBAR OVERLAY
════════════════════════════════════════════════════ -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ════════════════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">

  <div class="sidebar-header">
    <div class="sidebar-logo">📖 Cozy Reads</div>
    <button class="sidebar-close" onclick="closeSidebar()">×</button>
  </div>

  <!-- User info -->
  <div class="sidebar-user">
    <div class="sidebar-avatar">
      <?php
        $pic = auth()->user()->profile_pic ?? 'default.png';
        if ($pic !== 'default.png'):
      ?>
        <img src="<?= base_url('uploads/avatars/' . esc($pic)) ?>" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
      <?php else: ?>
        <?= strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) ?>
      <?php endif; ?>
    </div>
    <div>
      <div class="sidebar-username"><?= esc(auth()->user()->username ?? 'Reader') ?></div>
      <div class="sidebar-email"><?= esc(auth()->user()->email ?? '') ?></div>
    </div>
  </div>

  <!-- Nav links -->
  <nav class="sidebar-nav">
    <a href="<?= base_url('dashboard') ?>" class="nav-item active">
      <span class="nav-icon">🏠</span> Home
    </a>
    <a href="<?= base_url('shelves') ?>" class="nav-item">
      <span class="nav-icon">📚</span> My Shelves
    </a>
    <a href="<?= base_url('stats') ?>" class="nav-item">
      <span class="nav-icon">📊</span> Reading Stats
    </a>
    <a href="<?= base_url('goals') ?>" class="nav-item">
      <span class="nav-icon">🎯</span> Goals
    </a>
    <a href="<?= base_url('profile') ?>" class="nav-item">
      <span class="nav-icon">👤</span> Profile
    </a>
  </nav>

  <div class="sidebar-bottom">
    <a href="<?= base_url('logout') ?>" class="nav-item logout">
      <span class="nav-icon">🚪</span> Logout
    </a>
  </div>

</aside>

<!-- ════════════════════════════════════════════════════
     MAIN WRAP
════════════════════════════════════════════════════ -->
<div class="main-wrap">

  <!-- TOP BAR -->
  <header class="topbar">
    <div class="topbar-left">

      <!-- ☰ Sidebar toggle — top left -->
      <button class="menu-btn" onclick="openSidebar()" title="Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="topbar-title">Dashboard</div>
    </div>

    <div class="topbar-right">
      <!-- + Add Book button — top right -->
      <a href="<?= base_url('books/add') ?>" class="btn-add-book">
        <span class="plus">+</span> Add Book
      </a>
    </div>
  </header>

  <!-- PAGE CONTENT -->
  <main class="page-content">

    <!-- Flash success message -->
    <?php if (session()->getFlashdata('message')): ?>
      <div class="flash-success">✓ <?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>

    <!-- Greeting -->
    <div class="greeting">
      <?php
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $username = esc(auth()->user()->username ?? 'reader');
      ?>
      <div class="greeting-title"><?= $greeting ?>, <?= $username ?> 👋</div>
      <div class="greeting-sub">
        <?php if ($isNewUser): ?>
          Welcome to Cozy Reads! Start by adding your first book.
        <?php else: ?>
          <?= date('l, F j, Y') ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════
         EMPTY STATE — shown when user has no books yet
    ════════════════════════════════════════════════ -->
    <?php if ($isNewUser): ?>

      <div class="empty-state">
        <div class="empty-icon">📚</div>
        <div class="empty-title">Your shelf is empty</div>
        <div class="empty-sub">
          Add your first book to start tracking your reading journey.
          Search by title, author, or ISBN — or upload an EPUB to read right here.
        </div>
        <a href="<?= base_url('books/add') ?>" class="btn-add-book" style="font-size:15px; padding:11px 24px;">
          <span class="plus">+</span> Add Your First Book
        </a>
      </div>

    <?php else: ?>

      <!-- ════════════════════════════════════════════
           CURRENT READ CARD
      ════════════════════════════════════════════ -->
      <?php if ($currentRead): ?>
        <?php
          $totalPages  = (int) ($currentRead['total_pages'] ?? 0);
          $currentPage = (int) ($currentRead['current_page'] ?? 0);
          $percent     = $totalPages > 0
            ? (int) min(100, round(($currentPage / $totalPages) * 100))
            : 0;
        ?>
        <div class="current-read">
          <div class="book-cover-lg">
            <?php if (! empty($currentRead['cover_url'])): ?>
              <img src="<?= esc($currentRead['cover_url']) ?>" alt="<?= esc($currentRead['title']) ?>">
            <?php else: ?>
              <span><?= esc($currentRead['title']) ?></span>
            <?php endif; ?>
          </div>
          <div class="current-read-info">
            <div class="current-label">📖 Currently Reading</div>
            <div class="current-title"><?= esc($currentRead['title']) ?></div>
            <div class="current-author"><?= esc($currentRead['author'] ?? 'Unknown Author') ?></div>

            <div class="progress-label-row">
              <span>Page <?= $currentPage ?> of <?= $totalPages ?: '?' ?></span>
              <strong><?= $percent ?>% complete</strong>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width:<?= $percent ?>%"></div>
            </div>

            <div class="progress-update">
              <span>Update:</span>
              <form action="<?= base_url('books/progress/' . $currentRead['id']) ?>" method="POST" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <?= csrf_field() ?>
                <input
                  type="number"
                  name="current_page"
                  class="page-input"
                  value="<?= $currentPage ?>"
                  min="0"
                  max="<?= $totalPages ?: 9999 ?>"
                >
                <span>/ <?= $totalPages ?: '?' ?> pages</span>
                <button type="submit" class="btn-sm primary">Save</button>
              </form>
              <?php if ($currentRead['book_type'] === 'epub'): ?>
                <a href="<?= base_url('reader/' . $currentRead['id']) ?>" class="btn-sm">📱 Read</a>
              <?php endif; ?>
            </div>
          </div>
        </div>

      <?php else: ?>
        <!-- No book currently being read -->
        <div class="no-current-read">
          <span>📖</span>
          <div>
            Not reading anything right now.
            <a href="<?= base_url('shelves?status=want_to_read') ?>" style="color:var(--accent); text-decoration:none; border-bottom:1px solid var(--accent);">Pick something from your shelf</a>
            or
            <a href="<?= base_url('books/add') ?>" style="color:var(--accent); text-decoration:none; border-bottom:1px solid var(--accent);">add a new book</a>.
          </div>
        </div>
      <?php endif; ?>

      <!-- ════════════════════════════════════════════
           STATS ROW
      ════════════════════════════════════════════ -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-number"><?= $stats['finished_this_year'] ?></div>
          <div class="stat-label">Books Read This Year</div>
          <div class="stat-goal-bar">
            <div class="stat-goal-fill" style="width:<?= $annualProgress['percent'] ?>%"></div>
          </div>
          <div class="stat-goal-text"><?= $annualProgress['current'] ?> / <?= $annualProgress['target'] ?> goal</div>
        </div>

        <div class="stat-card">
          <div class="stat-number"><?= number_format($stats['total_pages']) ?></div>
          <div class="stat-label">Total Pages Read</div>
          <div class="stat-goal-bar">
            <div class="stat-goal-fill" style="width:<?= $monthlyProgress['percent'] ?>%"></div>
          </div>
          <div class="stat-goal-text"><?= number_format($monthlyProgress['current']) ?> / <?= number_format($monthlyProgress['target']) ?> this month</div>
        </div>

        <div class="stat-card">
          <div class="stat-number" style="font-size:20px; padding-top:4px;"><?= esc($stats['favourite_genre'] ?? 'N/A') ?></div>
          <div class="stat-label">Favourite Genre</div>
          <div class="stat-goal-text"><?= $stats['finished_count'] ?> books finished total</div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════
           READING GOALS
      ════════════════════════════════════════════ -->
      <div class="goals-row">

        <!-- Annual books goal -->
        <div class="goal-card">
          <?php
            $annualPct   = $annualProgress['percent'];
            $circumference = 2 * M_PI * 28; // r=28
            $offset      = $circumference - ($annualPct / 100) * $circumference;
          ?>
          <div class="goal-ring">
            <svg width="68" height="68" viewBox="0 0 68 68">
              <circle cx="34" cy="34" r="28" fill="none" stroke="var(--surface3)" stroke-width="6"/>
              <circle cx="34" cy="34" r="28" fill="none" stroke="var(--accent)" stroke-width="6"
                stroke-dasharray="<?= round($circumference, 2) ?>"
                stroke-dashoffset="<?= round($offset, 2) ?>"
                stroke-linecap="round"/>
            </svg>
            <div class="goal-ring-text"><?= $annualPct ?>%</div>
          </div>
          <div class="goal-info">
            <div class="goal-title">📅 Annual Reading Goal</div>
            <div class="goal-sub">
              <?= $annualProgress['current'] ?> of <?= $annualProgress['target'] ?> books read in <?= date('Y') ?>
            </div>
            <?php $remaining = $annualProgress['target'] - $annualProgress['current']; ?>
            <?php if ($remaining > 0): ?>
              <div class="goal-cta"><?= $remaining ?> more book<?= $remaining > 1 ? 's' : '' ?> to go!</div>
            <?php else: ?>
              <div class="goal-cta" style="color:#7cae8a;">🎉 Goal achieved!</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Monthly pages goal -->
        <div class="goal-card">
          <?php
            $monthPct    = $monthlyProgress['percent'];
            $offsetM     = $circumference - ($monthPct / 100) * $circumference;
          ?>
          <div class="goal-ring">
            <svg width="68" height="68" viewBox="0 0 68 68">
              <circle cx="34" cy="34" r="28" fill="none" stroke="var(--surface3)" stroke-width="6"/>
              <circle cx="34" cy="34" r="28" fill="none" stroke="#8b4545" stroke-width="6"
                stroke-dasharray="<?= round($circumference, 2) ?>"
                stroke-dashoffset="<?= round($offsetM, 2) ?>"
                stroke-linecap="round"/>
            </svg>
            <div class="goal-ring-text"><?= $monthPct ?>%</div>
          </div>
          <div class="goal-info">
            <div class="goal-title">📄 Monthly Page Goal</div>
            <div class="goal-sub">
              <?= number_format($monthlyProgress['current']) ?> of <?= number_format($monthlyProgress['target']) ?> pages in <?= date('F') ?>
            </div>
            <?php $remainingPages = $monthlyProgress['target'] - $monthlyProgress['current']; ?>
            <?php if ($remainingPages > 0): ?>
              <div class="goal-cta"><?= number_format($remainingPages) ?> pages left this month</div>
            <?php else: ?>
              <div class="goal-cta" style="color:#7cae8a;">🎉 Goal achieved!</div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- ════════════════════════════════════════════
           RECENTLY ADDED BOOKS
      ════════════════════════════════════════════ -->
      <div class="section-header">
        <div class="section-title">Recently Added</div>
        <a href="<?= base_url('shelves') ?>" class="section-link">View all →</a>
      </div>

      <div class="shelf-tabs">
        <div class="shelf-tab active" onclick="filterBooks(this, 'all')">All</div>
        <div class="shelf-tab" onclick="filterBooks(this, 'want_to_read')">Want to Read</div>
        <div class="shelf-tab" onclick="filterBooks(this, 'reading')">Reading</div>
        <div class="shelf-tab" onclick="filterBooks(this, 'finished')">Finished</div>
      </div>

      <?php if (empty($recentBooks)): ?>
        <p style="color:var(--text3); font-style:italic; font-size:14px; padding:20px 0;">No books here yet.</p>
      <?php else: ?>
        <div class="book-grid" id="bookGrid">
          <?php foreach ($recentBooks as $book): ?>
            <div class="book-card" data-status="<?= esc($book['status']) ?>" onclick="window.location='<?= base_url('books/' . $book['id']) ?>'">
              <div class="book-cover-sm">
                <?php if (! empty($book['cover_url'])): ?>
                  <img src="<?= esc($book['cover_url']) ?>" alt="<?= esc($book['title']) ?>">
                <?php else: ?>
                  <span><?= esc(substr($book['title'], 0, 20)) ?></span>
                <?php endif; ?>

                <?php if ($book['book_type'] === 'epub'): ?>
                  <span class="epub-badge">epub</span>
                <?php endif; ?>

                <span class="status-dot <?= $book['status'] === 'reading' ? 'reading' : ($book['status'] === 'finished' ? 'finished' : 'want') ?>"></span>
              </div>
              <div class="book-title-sm"><?= esc($book['title']) ?></div>
              <div class="book-author-sm"><?= esc($book['author'] ?? '') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    <?php endif; // end isNewUser ?>

  </main>
</div>

<script>
  // ── Sidebar open/close ──────────────────────────────
  function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }
  // Close sidebar on Escape key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSidebar();
  });

  // ── Shelf filter tabs ───────────────────────────────
  function filterBooks(el, status) {
    // Update active tab
    document.querySelectorAll('.shelf-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    // Show/hide book cards
    document.querySelectorAll('#bookGrid .book-card').forEach(card => {
      const match = status === 'all' || card.dataset.status === status;
      card.style.display = match ? 'block' : 'none';
    });
  }
</script>

</body>
</html>
