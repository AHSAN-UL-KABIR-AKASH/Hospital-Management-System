<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/User.php';

require_login();
$user = current_user();
$userModel = new User(getDB());
$errors = [];
$passErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!csrf_verify()) {
        $errors[] = 'Invalid session token. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if (mb_strlen($name) < 2) $errors[] = 'Please enter your full name.';
        if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) $errors[] = 'Please enter a valid phone number.';

        if (!$errors) {
            $userModel->updateProfile($user['id'], $name, $phone);
            flash('success', 'Profile updated successfully.');
            redirect('profile.php');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!csrf_verify()) {
        $passErrors[] = 'Invalid session token. Please try again.';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_new_password'] ?? '';
        $fullUser = $userModel->findById($user['id']);

        if (!$userModel->verifyPassword($current, $fullUser['password'])) {
            $passErrors[] = 'Current password is incorrect.';
        }
        if (strlen($new) < 8) {
            $passErrors[] = 'New password must be at least 8 characters.';
        }
        if ($new !== $confirm) {
            $passErrors[] = 'New passwords do not match.';
        }

        if (!$passErrors) {
            $userModel->updatePassword($user['id'], password_hash($new, PASSWORD_BCRYPT));
            flash('success', 'Password changed successfully.');
            redirect('profile.php');
        }
    }
}

$pageTitle = 'My Profile';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="form-card">
    <h1>My Profile</h1>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="POST" action="profile.php" class="js-loading-state">
      <?= csrf_field() ?>
      <input type="hidden" name="update_profile" value="1">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" required value="<?= e($user['name']) ?>">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
        <div class="form-hint">Email cannot be changed.</div>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" class="form-control" required value="<?= e($user['phone']) ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
    </form>
  </div>

  <div class="form-card">
    <h2>Change Password</h2>
    <?php foreach ($passErrors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="POST" action="profile.php" class="js-loading-state">
      <?= csrf_field() ?>
      <input type="hidden" name="change_password" value="1">
      <div class="form-group">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
      </div>
      <div class="form-group">
        <label for="confirm_new_password">Confirm New Password</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required minlength="8">
      </div>
      <button type="submit" class="btn btn-outline btn-block">Change Password</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
