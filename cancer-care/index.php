<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';

$campaignModel = new Campaign(getDB());
$stats = $campaignModel->stats();
$featured = $campaignModel->featured(3);
$recent = $campaignModel->recentlyAdded(6);
$fullyFunded = $campaignModel->fullyFunded(3);

function render_campaign_card(array $c): void {
    $progress = calc_progress((float)$c['raised_amount'], (float)$c['target_amount']);
    $remaining = calc_remaining((float)$c['raised_amount'], (float)$c['target_amount']);
    $photo = $c['patient_photo'] ? UPLOAD_PATIENTS_URL . e($c['patient_photo']) : BASE_URL . 'css/placeholder-patient.svg';
    ?>
    <div class="campaign-card">
      <img class="campaign-photo" src="<?= $photo ?>" alt="<?= e($c['patient_name']) ?>" onerror="this.src='<?= BASE_URL ?>css/placeholder-patient.svg'">
      <div class="campaign-body">
        <span class="badge badge-verified">Verified</span>
        <?php if ($c['campaign_status'] === 'fully_funded'): ?>
          <span class="badge badge-fully-funded">Fully Funded</span>
        <?php endif; ?>
        <h3><?= e($c['patient_name']) ?></h3>
        <div class="campaign-meta"><?= e($c['cancer_type']) ?> &middot; <?= e($c['hospital']) ?></div>
        <div class="progress-bar-track">
          <div class="progress-bar-fill" data-progress="<?= $progress ?>"></div>
        </div>
        <div class="campaign-amounts">
          <span>Raised: <strong><?= money((float)$c['raised_amount']) ?></strong></span>
          <span><?= $progress ?>%</span>
        </div>
        <div class="campaign-amounts">
          <span>Target: <?= money((float)$c['target_amount']) ?></span>
          <span>Remaining: <?= money($remaining) ?></span>
        </div>
        <div class="campaign-meta"><?= (int)$c['donor_count'] ?> donor(s)</div>
        <div class="campaign-actions">
          <a href="campaign.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">View Details</a>
          <a href="donate.php?campaign_id=<?= (int)$c['id'] ?>" class="btn btn-accent btn-sm">Donate Now</a>
        </div>
      </div>
    </div>
    <?php
}

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Help Someone Fight Cancer</h1>
    <p>Cancer Care connects generous donors with verified cancer patients who urgently need financial support for treatment. Every donation, big or small, brings hope closer.</p>
    <div class="hero-buttons">
      <a href="search.php" class="btn btn-accent">Donate Now</a>
      <a href="create_campaign.php" class="btn btn-outline">Create a Fundraiser</a>
    </div>
  </div>
</section>

<section class="stats-section container">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-value"><?= number_format($stats['total_patients']) ?></div>
      <div class="stat-label">Total Patients Helped</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= number_format($stats['active_campaigns']) ?></div>
      <div class="stat-label">Total Active Campaigns</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= number_format($stats['total_donations']) ?></div>
      <div class="stat-label">Total Donations</div>
    </div>
    <div class="stat-card">
      <div class="stat-value"><?= money($stats['total_raised']) ?></div>
      <div class="stat-label">Total Amount Raised</div>
    </div>
  </div>
</section>

<?php if ($featured): ?>
<section class="section container">
  <div class="section-header">
    <h2>Featured Campaigns</h2>
    <a href="search.php">View all &rarr;</a>
  </div>
  <div class="campaign-grid">
    <?php foreach ($featured as $c) render_campaign_card($c); ?>
  </div>
</section>
<?php endif; ?>

<?php if ($recent): ?>
<section class="section container">
  <div class="section-header">
    <h2>Recently Added Campaigns</h2>
    <a href="search.php?filter=recent">View all &rarr;</a>
  </div>
  <div class="campaign-grid">
    <?php foreach ($recent as $c) render_campaign_card($c); ?>
  </div>
</section>
<?php else: ?>
<section class="section container">
  <div class="empty-state">
    <h3>No verified campaigns yet</h3>
    <p>Be the first to create a fundraiser for a patient in need.</p>
    <a href="create_campaign.php" class="btn btn-primary">Create a Fundraiser</a>
  </div>
</section>
<?php endif; ?>

<?php if ($fullyFunded): ?>
<section class="section container">
  <div class="section-header">
    <h2>Fully Funded Campaigns</h2>
    <a href="search.php?filter=fully_funded">View all &rarr;</a>
  </div>
  <div class="campaign-grid">
    <?php foreach ($fullyFunded as $c) render_campaign_card($c); ?>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
