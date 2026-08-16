<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Campaign.php';

require_admin();
$campaignModel = new Campaign(getDB());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid session token. Please try again.');
    } else {
        $campaignId = (int) ($_POST['campaign_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($action === 'suspend' && $campaignId) {
            $campaignModel->suspend($campaignId);
            flash('success', 'Campaign suspended.');
        } elseif ($action === 'delete' && $campaignId) {
            $campaignModel->delete($campaignId);
            flash('success', 'Campaign deleted.');
        } elseif ($action === 'verify' && $campaignId) {
            $campaignModel->setVerificationStatus($campaignId, 'verified');
            flash('success', 'Campaign verified.');
        }
    }
    redirect('admin/campaigns.php');
}

$term = trim($_GET['q'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$db = getDB();

if ($term !== '') {
    $sql = 'SELECT c.*, u.name AS creator_name FROM campaigns c JOIN users u ON u.id = c.user_id
            WHERE c.patient_name LIKE ? OR c.hospital LIKE ? OR c.cancer_type LIKE ?';
    $params = ["%$term%", "%$term%", "%$term%"];
    if ($statusFilter !== '') { $sql .= ' AND c.verification_status = ?'; $params[] = $statusFilter; }
    $sql .= ' ORDER BY c.created_at DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $campaigns = $stmt->fetchAll();
} else {
    $campaigns = $campaignModel->allForAdmin($statusFilter);
}

$statusBadgeClass = [
    'verified' => 'badge-verified', 'pending' => 'badge-pending',
    'rejected' => 'badge-rejected', 'suspended' => 'badge-suspended',
];

$pageTitle = 'Manage Campaigns';
$adminActive = 'campaigns';
include __DIR__ . '/admin_header.php';
?>
<h1>Manage Campaigns</h1>

<form method="GET" action="campaigns.php" class="search-bar">
  <input type="text" name="q" placeholder="Search by patient, hospital, cancer type..." value="<?= e($term) ?>">
  <select name="status" class="form-control" style="max-width:200px;">
    <option value="">All Statuses</option>
    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="verified" <?= $statusFilter === 'verified' ? 'selected' : '' ?>>Verified</option>
    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
    <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
  </select>
  <button type="submit" class="btn btn-primary">Filter</button>
</form>

<table class="data-table">
  <thead><tr><th>Patient</th><th>Created By</th><th>Target</th><th>Raised</th><th>Verification</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($campaigns as $c): ?>
      <tr>
        <td><a href="../campaign.php?id=<?= (int)$c['id'] ?>" target="_blank"><?= e($c['patient_name']) ?></a></td>
        <td><?= e($c['creator_name']) ?></td>
        <td><?= money((float)$c['target_amount']) ?></td>
        <td><?= money((float)$c['raised_amount']) ?></td>
        <td><span class="badge <?= $statusBadgeClass[$c['verification_status']] ?? 'badge-pending' ?>"><?= e(ucfirst($c['verification_status'])) ?></span></td>
        <td><?= e(ucfirst(str_replace('_',' ', $c['campaign_status']))) ?></td>
        <td style="display:flex; gap:6px; flex-wrap:wrap;">
          <a href="verification.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">Review</a>
          <?php if ($c['verification_status'] !== 'suspended'): ?>
          <form method="POST" action="campaigns.php" style="display:inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="action" value="suspend">
            <button type="submit" class="btn btn-danger btn-sm">Suspend</button>
          </form>
          <?php endif; ?>
          <form method="POST" action="campaigns.php" style="display:inline;" onsubmit="return confirm('Delete this campaign permanently? This cannot be undone.');">
            <?= csrf_field() ?>
            <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/admin_footer.php'; ?>
