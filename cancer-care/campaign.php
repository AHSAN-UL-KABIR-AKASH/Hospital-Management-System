<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';
require_once __DIR__ . '/models/Donation.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$campaignModel = new Campaign(getDB());
$campaign = $id ? $campaignModel->findById($id) : null;

$user = current_user();
$isOwner = $user && $campaign && (int)$campaign['user_id'] === (int)$user['id'];
$isAdmin = $user && $user['role'] === 'admin';

if (!$campaign || ($campaign['verification_status'] !== 'verified' && !$isOwner && !$isAdmin)) {
    $pageTitle = 'Campaign Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container empty-state"><h2>Campaign not found</h2><p>This fundraiser may not exist, or is still pending verification.</p><a href="search.php" class="btn btn-primary">Browse Campaigns</a></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$donationModel = new Donation(getDB());
$donations = $donationModel->historyForCampaign($id);

$reportSubmitted = false;
$reportError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_campaign'])) {
    require_login();
    if (!csrf_verify()) {
        $reportError = 'Invalid session token. Please try again.';
    } else {
        $reason = trim($_POST['reason'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($reason === '') {
            $reportError = 'Please select a reason for reporting this campaign.';
        } else {
            $stmt = getDB()->prepare('INSERT INTO reports (campaign_id, reported_by, reason, description) VALUES (?, ?, ?, ?)');
            $stmt->execute([$id, $user['id'], $reason, $description ?: null]);
            $reportSubmitted = true;
        }
    }
}

$isSaved = false;
if ($user) {
    $stmt = getDB()->prepare('SELECT 1 FROM saved_campaigns WHERE user_id = ? AND campaign_id = ?');
    $stmt->execute([$user['id'], $id]);
    $isSaved = (bool) $stmt->fetchColumn();
}

$progress = calc_progress((float)$campaign['raised_amount'], (float)$campaign['target_amount']);
$remaining = calc_remaining((float)$campaign['raised_amount'], (float)$campaign['target_amount']);
$photo = $campaign['patient_photo'] ? UPLOAD_PATIENTS_URL . e($campaign['patient_photo']) : BASE_URL . 'css/placeholder-patient.svg';

$statusBadgeClass = [
    'verified' => 'badge-verified', 'pending' => 'badge-pending',
    'rejected' => 'badge-rejected', 'suspended' => 'badge-suspended',
][$campaign['verification_status']] ?? 'badge-pending';

$pageTitle = $campaign['patient_name'];
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="patient-hero">
    <div class="patient-photo-wrap">
      <img src="<?= $photo ?>" alt="<?= e($campaign['patient_name']) ?>" onerror="this.src='<?= BASE_URL ?>css/placeholder-patient.svg'">
    </div>
    <div class="patient-info">
      <span class="badge <?= $statusBadgeClass ?>"><?= e(ucfirst($campaign['verification_status'])) ?></span>
      <?php if ($campaign['campaign_status'] === 'fully_funded'): ?>
        <span class="badge badge-fully-funded">Fully Funded</span>
      <?php endif; ?>
      <h1><?= e($campaign['patient_name']) ?></h1>

      <div class="patient-facts">
        <div class="patient-fact"><strong>Age</strong><?= (int)$campaign['age'] ?></div>
        <div class="patient-fact"><strong>Cancer Type</strong><?= e($campaign['cancer_type']) ?></div>
        <div class="patient-fact"><strong>Cancer Stage</strong><?= e($campaign['cancer_stage']) ?></div>
        <div class="patient-fact"><strong>Hospital</strong><?= e($campaign['hospital']) ?></div>
      </div>

      <?php if (!empty($campaign['treatment_details'])): ?>
        <h3>Treatment Details</h3>
        <p><?= nl2br(e($campaign['treatment_details'])) ?></p>
      <?php endif; ?>

      <h3>Patient Story</h3>
      <p><?= nl2br(e($campaign['story'])) ?></p>

      <div class="donation-progress-box">
        <div class="progress-bar-track">
          <div class="progress-bar-fill" data-progress="<?= $progress ?>"></div>
        </div>
        <div class="campaign-amounts">
          <span>Raised: <strong><?= money((float)$campaign['raised_amount']) ?></strong></span>
          <span><?= $progress ?>%</span>
        </div>
        <div class="campaign-amounts">
          <span>Target: <?= money((float)$campaign['target_amount']) ?></span>
          <span>Remaining: <?= money($remaining) ?></span>
        </div>
        <div class="campaign-meta"><?= (int)$campaign['donor_count'] ?> donor(s)</div>

        <?php if ($campaign['campaign_status'] === 'fully_funded'): ?>
          <button class="btn btn-primary btn-block" disabled style="margin-top:14px;">Fully Funded</button>
        <?php elseif ($campaign['verification_status'] !== 'verified'): ?>
          <button class="btn btn-primary btn-block" disabled style="margin-top:14px;">Pending Verification</button>
        <?php else: ?>
          <a href="donate.php?campaign_id=<?= (int)$campaign['id'] ?>" class="btn btn-accent btn-block" style="margin-top:14px;">Donate Now</a>
        <?php endif; ?>

        <?php if ($user): ?>
        <form method="POST" action="saved_campaigns.php" style="margin-top:12px;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="<?= $isSaved ? 'remove' : 'save' ?>">
          <input type="hidden" name="campaign_id" value="<?= (int)$campaign['id'] ?>">
          <input type="hidden" name="redirect" value="campaign.php?id=<?= (int)$campaign['id'] ?>">
          <button type="submit" class="btn btn-outline btn-block"><?= $isSaved ? '★ Saved (click to remove)' : '☆ Save Campaign' ?></button>
        </form>
        <?php endif; ?>

        <div class="share-buttons">
          <button class="share-btn copy-link-btn" data-url="<?= e((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">Copy Link</button>
          <a class="share-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">Facebook</a>
          <a class="share-btn" target="_blank" rel="noopener" href="https://wa.me/?text=<?= urlencode('Help ' . $campaign['patient_name'] . ' fight cancer: ' . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">WhatsApp</a>
        </div>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>Donation History</h2>
    <?php if ($donations): ?>
      <ul class="donation-list">
        <?php foreach ($donations as $d): ?>
          <li>
            <span><?= $d['anonymous'] ? 'Anonymous Donor' : e($d['donor_name']) ?><?= !empty($d['message']) ? ' — ' . e($d['message']) : '' ?></span>
            <strong><?= money((float)$d['amount']) ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="empty-state">No donations yet. Be the first to help!</p>
    <?php endif; ?>
  </div>

  <div class="section">
    <h2>Report This Campaign</h2>
    <p style="font-size:14px; color:#6b7280;">If you believe this campaign is fraudulent or inappropriate, let our team know.</p>
    <?php if ($reportSubmitted): ?>
      <div class="alert alert-success">Thank you. Your report has been submitted and our admin team will review it.</div>
    <?php else: ?>
      <?php if ($reportError): ?><div class="alert alert-error"><?= e($reportError) ?></div><?php endif; ?>
      <?php if ($user): ?>
      <form method="POST" action="campaign.php?id=<?= (int)$campaign['id'] ?>" class="js-loading-state" style="max-width:520px;">
        <?= csrf_field() ?>
        <input type="hidden" name="report_campaign" value="1">
        <div class="form-group">
          <label for="reason">Reason</label>
          <select id="reason" name="reason" class="form-control" required>
            <option value="">Select a reason</option>
            <option value="Suspicious / fraudulent">Suspicious / fraudulent</option>
            <option value="Duplicate campaign">Duplicate campaign</option>
            <option value="Inappropriate content">Inappropriate content</option>
            <option value="Incorrect information">Incorrect information</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label for="description">Details (optional)</label>
          <textarea id="description" name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-outline">Submit Report</button>
      </form>
      <?php else: ?>
        <p><a href="login.php">Login</a> to report this campaign.</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
