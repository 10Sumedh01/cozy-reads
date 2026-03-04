<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?? 'Cozy Reads' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #1a1410;
    --surface: #231c16;
    --surface2: #2d2318;
    --surface3: #3a2d20;
    --border: #4a3828;
    --border2: #5c4535;
    --accent: #c0392b;
    --accent2: #e74c3c;
    --accent-warm: #d4845a;
    --accent-gold: #c9a84c;
    --text: #f0e6d3;
    --text2: #c4a882;
    --text3: #8a7060;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Crimson Pro', serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .auth-container {
    display: flex;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    width: 820px;
    min-height: 520px;
  }

  /* Brand panel */
  .auth-brand {
    width: 280px;
    min-width: 280px;
    background: linear-gradient(160deg, #2a1a0e 0%, #1a0f08 100%);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 30px;
    position: relative;
    overflow: hidden;
  }
  .auth-brand::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(192,57,43,0.15) 0%, transparent 70%);
    border-radius: 50%;
  }
  .brand-icon { font-size: 52px; margin-bottom: 16px; filter: drop-shadow(0 4px 16px rgba(192,57,43,0.4)); }
  .brand-name { font-family: 'DM Serif Display', serif; font-size: 32px; color: var(--text); margin-bottom: 8px; }
  .brand-divider { width: 40px; height: 1px; background: var(--border2); margin: 16px auto; }
  .brand-tagline { font-size: 14px; color: var(--text3); text-align: center; font-style: italic; line-height: 1.6; }

  /* Form panel */
  .auth-panel { flex: 1; padding: 36px 40px; display: flex; flex-direction: column; justify-content: center; }

  .auth-tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 28px; }
  .auth-tab {
    flex: 1; padding: 13px; text-align: center;
    font-family: 'Crimson Pro', serif; font-size: 15px;
    color: var(--text3); border-bottom: 2px solid transparent;
    text-decoration: none; transition: all 0.2s; letter-spacing: 0.04em;
  }
  .auth-tab.active { color: var(--accent-warm); border-bottom-color: var(--accent-warm); }

  h2 { font-family: 'DM Serif Display', serif; font-size: 24px; margin-bottom: 22px; color: var(--text); }

  .field { margin-bottom: 14px; }
  label { font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text3); display: block; margin-bottom: 5px; }

  input[type=text], input[type=email], input[type=password],
  input[type=tel], input[type=number], select {
    background: var(--surface3);
    border: 1px solid var(--border2);
    color: var(--text);
    font-family: 'Crimson Pro', serif;
    font-size: 15px;
    padding: 10px 14px;
    border-radius: 6px;
    width: 100%;
    outline: none;
    transition: border-color 0.2s;
  }
  input:focus, select:focus { border-color: var(--accent-warm); }
  input::placeholder { color: var(--text3); }
  select option { background: var(--surface2); }

  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

  .profile-upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--surface3);
    border: 1px dashed var(--border2);
    color: var(--text3);
    font-family: 'Crimson Pro', serif;
    font-size: 14px;
    padding: 10px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: border-color 0.2s;
    width: 100%;
  }
  .profile-upload-label:hover { border-color: var(--accent-warm); color: var(--accent-warm); }
  input[type=file] { display: none; }

  .btn-primary {
    background: var(--accent);
    color: #fff;
    border: 1px solid var(--accent2);
    font-family: 'Crimson Pro', serif;
    font-size: 15px;
    letter-spacing: 0.05em;
    padding: 11px 20px;
    border-radius: 6px;
    width: 100%;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 4px;
  }
  .btn-primary:hover { background: var(--accent2); transform: translateY(-1px); }

  .forgot-link {
    text-align: right;
    margin-bottom: 16px;
  }
  .forgot-link a { font-size: 13px; color: var(--text3); text-decoration: none; }
  .forgot-link a:hover { color: var(--accent-warm); }

  /* Errors */
  .alert-errors {
    background: rgba(192,57,43,0.1);
    border: 1px solid rgba(192,57,43,0.3);
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 16px;
  }
  .alert-errors p { font-size: 13px; color: #e07060; margin-bottom: 2px; }
  .alert-errors p:last-child { margin-bottom: 0; }

  /* Success */
  .alert-success {
    background: rgba(126,176,104,0.1);
    border: 1px solid rgba(126,176,104,0.3);
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 16px;
    font-size: 13px;
    color: #a0c890;
  }

  /* Remember me */
  .remember-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 14px;
    color: var(--text3);
  }
  .remember-row input[type=checkbox] {
    width: auto;
    accent-color: var(--accent-warm);
  }

  @media (max-width: 700px) {
    .auth-container { flex-direction: column; width: 95vw; }
    .auth-brand { width: 100%; min-width: unset; padding: 28px 20px; flex-direction: row; gap: 16px; }
    .brand-tagline { display: none; }
    .brand-divider { display: none; }
    .auth-panel { padding: 24px 20px; }
  }
</style>
</head>
<body>
  <div class="auth-container">

    <!-- Brand Side -->
    <div class="auth-brand">
      <div class="brand-icon">📖</div>
      <div class="brand-name">Cozy Reads</div>
      <div class="brand-divider"></div>
      <div class="brand-tagline">Your personal reading sanctuary. Track, discover, and savour every page.</div>
    </div>

    <!-- Form Side -->
    <div class="auth-panel">

      <!-- Tab switcher -->
      <div class="auth-tabs">
        <a href="<?= base_url('login') ?>"    class="auth-tab <?= (uri_string() === 'login')    ? 'active' : '' ?>">Sign In</a>
        <a href="<?= base_url('register') ?>" class="auth-tab <?= (uri_string() === 'register') ? 'active' : '' ?>">Create Account</a>
      </div>

      <!-- Flash errors -->
      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-errors">
          <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <p>⚠ <?= esc($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Flash success -->
      <?php if (session()->getFlashdata('message')): ?>
        <div class="alert-success">✓ <?= esc(session()->getFlashdata('message')) ?></div>
      <?php endif; ?>

      <!-- Page content (login form or register form) -->
      <?= $this->renderSection('form') ?>

    </div>
  </div>
</body>
</html>
