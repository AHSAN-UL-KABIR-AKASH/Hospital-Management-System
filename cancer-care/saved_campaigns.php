<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';

require_login();
$user = current_user();
$db = getDB();
$campaignModel = new Campaign($db);

// Handle save / remove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid session token. Please try again.');
    } else {
        $action = $_POST['action'] ?? '';
        $campaignId = (int) ($_POST['campaign_id'] ?? 0);

        if ($action === 'save' && $campaignId) {
            $stmt = $db->prepare('INSERT IGNORE INTO saved_campaigns (user_id, campaign_id) VALUES (?, ?)');
            $stmt->execute([$user['id'], $campaignId]);
            flash('success', 'Campaign saved.');
        } elseif ($action === 'remove' && $campaignId) {
            $stmt = $db->prepare('DELETE FROM saved_campaigns WHERE user_id = ? AND campaign_id = ?');
            $stmt->execute([$user['id'], $campaignId]);
            flash('success', 'Campaign removed from saved list.');
        }
    }
    $redirectBack = $_POST['redirect'] ?? 'saved_campaigns.php';
    redirect($redirectBack);
}

$stmt = $db->prepare(
    "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
     FROM saved_campaigns sc
     JOIN campaigns c ON c.id = sc.campaign_id
     WHERE sc.user_id = ?
     ORDER BY sc.created_at DESC"
);
$stmt->execute([$user['id']]);
$saved = $stmt->fetchAll();

$pageTitle = 'Saved Campaigns';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h1>Saved Campaigns</h1>
  <?php if ($saved): ?>
  <div class="campaign-grid">
    <?php foreach ($saved as $c):
      $progress = calc_progress((float)$c['raised_amount'], (float)$c['target_amount']);
      $remaining = calc_remaining((float)$c['raised_amount'], (float)$c['target_amount']);
      $photo = $c['patient_photo'] ? UPLOAD_PATIENTS_URL . e($c['patient_photo']) : BASE_URL . 'css/placeholder-patient.svg';
    ?>
      <div class="campaign-card">
        <img class="campaign-photo" src="<?= $photo ?>" alt="<?= e($c['patient_name']) ?>" onerror="this.src='<?= BASE_URL ?>css/placeholder-patient.svg'">
        <div class="campaign-body">
          <h3><?= e($c['patient_name']) ?></h3>
          <div class="campaign-meta"><?= e($c['cancer_type']) ?> &middot; <?= e($c['hospital']) ?></div>
          <div class="progress-bar-track"><div class="progress-bar-fill" data-progress="<?= $progress ?>"></div></div>
          <div class="campaign-amounts">
            <span>Raised: <strong><?= money((float)$c['raised_amount']) ?></strong></span>
            <span><?= $progress ?>%</span>
          </div>
          <div class="campaign-actions">
            <a href="campaign.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">View Details</a>
            <form method="POST" action="saved_campaigns.php" style="flex:1;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm btn-block">Remove</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <div class="empty-state">
      <h3>No saved campaigns</h3>
      <p>Bookmark campaigns while browsing to find them here later.</p>
      <a href="search.php" class="btn btn-primary">Find Patients</a>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
