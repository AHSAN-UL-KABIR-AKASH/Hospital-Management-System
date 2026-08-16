<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Campaign.php';

require_admin();
$db = getDB();
$campaignModel = new Campaign($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid session token. Please try again.');
    } else {
        $reportId = (int) ($_POST['report_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        if ($reportId && in_array($action, ['reviewed', 'dismissed'], true)) {
            $stmt = $db->prepare('UPDATE reports SET status = ? WHERE id = ?');
            $stmt->execute([$action, $reportId]);
            flash('success', 'Report marked as ' . $action . '.');
        } elseif ($action === 'suspend_campaign') {
            $campaignId = (int) ($_POST['campaign_id'] ?? 0);
            if ($campaignId) {
                $campaignModel->suspend($campaignId);
                flash('success', 'Campaign suspended due to report.');
            }
        }
    }
    redirect('admin/reports.php');
}

$stmt = $db->query(
    'SELECT r.*, c.patient_name, u.name AS reporter_name
     FROM reports r
     JOIN campaigns c ON c.id = r.campaign_id
     JOIN users u ON u.id = r.reported_by
     ORDER BY r.created_at DESC'
);
$reports = $stmt->fetchAll();

$pageTitle = 'Campaign Reports';
$adminActive = 'reports';
include __DIR__ . '/admin_header.php';
?>
<h1>Campaign Reports</h1>

<?php if ($reports): ?>
<table class="data-table">
  <thead><tr><th>Campaign</th><th>Reported By</th><th>Reason</th><th>Description</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($reports as $r): ?>
      <tr>
        <td><a href="../campaign.php?id=<?= (int)$r['campaign_id'] ?>" target="_blank"><?= e($r['patient_name']) ?></a></td>
        <td><?= e($r['reporter_name']) ?></td>
        <td><?= e($r['reason']) ?></td>
        <td><?= e($r['description'] ?? '') ?></td>
        <td><span class="badge <?= $r['status'] === 'open' ? 'badge-pending' : ($r['status'] === 'reviewed' ? 'badge-verified' : 'badge-suspended') ?>"><?= e(ucfirst($r['status'])) ?></span></td>
        <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
        <td style="display:flex; gap:6px; flex-wrap:wrap;">
          <?php if ($r['status'] === 'open'): ?>
            <form method="POST" action="reports.php" style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="reviewed">
              <button type="submit" class="btn btn-outline btn-sm">Mark Reviewed</button>
            </form>
            <form method="POST" action="reports.php" style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="dismissed">
              <button type="submit" class="btn btn-outline btn-sm">Dismiss</button>
            </form>
            <form method="POST" action="reports.php" style="display:inline;" onsubmit="return confirm('Suspend this campaign?');">
              <?= csrf_field() ?>
              <input type="hidden" name="campaign_id" value="<?= (int)$r['campaign_id'] ?>">
              <input type="hidden" name="action" value="suspend_campaign">
              <button type="submit" class="btn btn-danger btn-sm">Suspend Campaign</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
  <div class="empty-state"><h3>No reports</h3><p>No users have reported any campaigns.</p></div>
<?php endif; ?>
<?php include __DIR__ . '/admin_footer.php'; ?>
