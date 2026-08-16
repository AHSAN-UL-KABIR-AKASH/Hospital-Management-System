<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/User.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid session token. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $old = ['name' => $name, 'email' => $email, 'phone' => $phone];

        if ($name === '' || mb_strlen($name) < 2) {
            $errors[] = 'Please enter your full name.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($phone === '' || !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
            $errors[] = 'Please enter a valid phone number.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        $userModel = new User(getDB());
        if (!$errors && $userModel->emailExists($email)) {
            $errors[] = 'An account with this email already exists.';
        }

        if (!$errors) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $userId = $userModel->create($name, $email, $phone, $hash, 'donor');
            $_SESSION['user_id'] = $userId;
            flash('success', 'Welcome to Cancer Care! Your account has been created.');
            redirect('dashboard.php');
        }
    }
}

$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-page container">
  <div class="form-card">
    <h1>Create an Account</h1>
    <p>Join Cancer Care to donate or start a fundraiser for a patient.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" id="registerForm" action="register.php" class="js-loading-state">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" required value="<?= e($old['name']) ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" required value="<?= e($old['email']) ?>">
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" class="form-control" required value="<?= e($old['phone']) ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="form-control" required minlength="8">
          <button type="button" class="password-toggle" data-target="password">Show</button>
        </div>
        <div class="form-hint">Minimum 8 characters.</div>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <div class="password-wrapper">
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
          <button type="button" class="password-toggle" data-target="confirm_password">Show</button>
        </div>
        <div id="confirmError" class="field-error"></div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
    <p style="margin-top:16px;">Already have an account? <a href="login.php">Login here</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
