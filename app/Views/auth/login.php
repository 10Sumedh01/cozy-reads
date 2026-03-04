<?php $this->extend('layouts/auth_layout') ?>
<?php $this->section('form') ?>

<h2>Welcome back, reader.</h2>

<form action="<?= base_url('login') ?>" method="POST">
  <?= csrf_field() ?>

  <div class="field">
    <label>Username or Email</label>
    <input
      type="text"
      name="login"
      placeholder="your@email.com or username"
      value="<?= old('login') ?>"
      autocomplete="username"
    >
  </div>

  <div class="field">
    <label>Password</label>
    <input
      type="password"
      name="password"
      placeholder="••••••••"
      autocomplete="current-password"
    >
  </div>

  <div class="forgot-link">
    <a href="<?= base_url('forgot-password') ?>">Forgot password?</a>
  </div>

  <div class="remember-row">
    <input type="checkbox" name="remember" id="remember" value="1" <?= old('remember') ? 'checked' : '' ?>>
    <label for="remember" style="text-transform:none; letter-spacing:0; font-size:14px; margin:0;">Remember me</label>
  </div>

  <button type="submit" class="btn-primary">Sign In</button>

</form>

<?php $this->endSection() ?>
