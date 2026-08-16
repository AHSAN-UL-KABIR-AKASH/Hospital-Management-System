<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/User.php';

require_admin();
$userModel = new User(getDB());
$currentAdmin = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid session token. Please try again.');
    } else {
        $targetId = (int) ($_POST['user_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($targetId === (int) $currentAdmin['id']) {
            flash('error', 'You cannot change your own account status.');
        } elseif ($action === 'suspend') {
            $userModel->setStatus($targetId, 'suspended');
            flash('success', 'User suspended.');
        } elseif ($action === 'activate') {
            $userModel->setStatus($targetId, 'active');
            flash('success', 'User activated.');
        }
    }
    redirect('admin/users.php');
}

$term = trim($_GET['q'] ?? '');
$users = $term !== '' ? $userModel->search($term) : $userModel->all(200);

$pageTitle = 'Manage Users';
$adminActive = 'users';
include __DIR__ . '/admin_header.php';
?>
<h1>Manage Users</h1>

<form method="GET" action="users.php" class="search-bar">
  <input type="text" name="q" placeholder="Search by name or email..." value="<?= e($term) ?>">
  <button type="submit" class="btn btn-primary">Search</button>
</form>

<table class="data-table">
  <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= e($u['name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e($u['phone']) ?></td>
        <td><?= e(ucfirst($u['role'])) ?></td>
        <td><span class="badge <?= $u['status'] === 'active' ? 'badge-verified' : 'badge-rejected' ?>"><?= e(ucfirst($u['status'])) ?></span></td>
        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        <td>
          <?php if ((int)$u['id'] !== (int)$currentAdmin['id']): ?>
            <form method="POST" action="users.php" style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
              <?php if ($u['status'] === 'active'): ?>
                <input type="hidden" name="action" value="suspend">
                <button type="submit" class="btn btn-danger btn-sm">Suspend</button>
              <?php else: ?>
                <input type="hidden" name="action" value="activate">
                <button type="submit" class="btn btn-accent btn-sm">Activate</button>
              <?php endif; ?>
            </form>
          <?php else: ?>
            <span class="campaign-meta">(You)</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/admin_footer.php'; ?>
