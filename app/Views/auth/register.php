<?php $this->extend('layouts/auth_layout') ?>
<?php $this->section('form') ?>

<h2>Start your reading journey.</h2>

<form action="<?= base_url('register') ?>" method="POST" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="form-row">
    <div class="field">
      <label>Username</label>
      <input
        type="text"
        name="username"
        placeholder="cozyreader"
        value="<?= old('username') ?>"
        autocomplete="username"
      >
    </div>
    <div class="field">
      <label>Email</label>
      <input
        type="email"
        name="email"
        placeholder="jane@example.com"
        value="<?= old('email') ?>"
        autocomplete="email"
      >
    </div>
  </div>

  <div class="field">
    <label>Mobile</label>
    <input
      type="tel"
      name="mobile"
      placeholder="+91 98765 43210"
      value="<?= old('mobile') ?>"
    >
  </div>

  <div class="field">
    <label>Password</label>
    <input
      type="password"
      name="password"
      placeholder="Min. 8 characters"
      autocomplete="new-password"
    >
  </div>

  <div class="field">
    <label>Confirm Password</label>
    <input
      type="password"
      name="password_confirm"
      placeholder="Repeat password"
      autocomplete="new-password"
    >
  </div>

  <div class="form-row">
    <div class="field">
      <label>Gender</label>
      <select name="gender">
        <option value="">Select</option>
        <option value="female"            <?= old('gender') === 'female'            ? 'selected' : '' ?>>She / Her</option>
        <option value="male"              <?= old('gender') === 'male'              ? 'selected' : '' ?>>He / Him</option>
        <option value="other"             <?= old('gender') === 'other'             ? 'selected' : '' ?>>They / Them</option>
        <option value="prefer_not_to_say" <?= old('gender') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
      </select>
    </div>

    <div class="field">
      <label>Profile Picture</label>
      <label class="profile-upload-label" for="profile_pic">
        📷 <span id="pic-label">Upload Photo</span>
      </label>
      <input
        type="file"
        name="profile_pic"
        id="profile_pic"
        accept="image/jpeg,image/png,image/webp"
        onchange="document.getElementById('pic-label').textContent = this.files[0]?.name ?? 'Upload Photo'"
      >
    </div>
  </div>

  <button type="submit" class="btn-primary">Create Account</button>

</form>

<?php $this->endSection() ?>
