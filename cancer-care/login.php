<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/User.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid session token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $oldEmail = $email;

        if ($email === '' || $password === '') {
            $errors[] = 'Please enter both email and password.';
        } else {
            $userModel = new User(getDB());
            $user = $userModel->findByEmail($email);

            if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            } elseif ($user['status'] === 'suspended') {
                $errors[] = 'Your account has been suspended. Please contact support.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                flash('success', 'Welcome back, ' . $user['name'] . '!');
                if ($user['role'] === 'admin') {
                    redirect('admin/index.php');
                }
                redirect('dashboard.php');
            }
        }
    }
}

$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-page container">
  <div class="form-card">
    <h1>Welcome Back</h1>
    <p>Login to donate or manage your fundraising campaigns.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="login.php" class="js-loading-state">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" required value="<?= e($oldEmail) ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="form-control" required>
          <button type="button" class="password-toggle" data-target="password">Show</button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p style="margin-top:16px;">Don't have an account? <a href="register.php">Register here</a></p>
    <p style="margin-top:8px; font-size:13px; color:#6b7280;">Demo admin: admin@cancercare.test / Password123!</p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
