<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';

$campaignModel = new Campaign(getDB());
$term = trim($_GET['q'] ?? '');
$filter = trim($_GET['filter'] ?? 'recent');
$allowedFilters = ['recent', 'most_funded', 'almost_funded', 'fully_funded'];
if (!in_array($filter, $allowedFilters, true)) $filter = 'recent';

$results = $campaignModel->search($term, $filter);

$pageTitle = 'Find Patients';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h1>Find Patients</h1>
  <p>Search verified cancer patients by name, cancer type, or hospital.</p>

  <form method="GET" action="search.php" id="searchForm" class="search-bar">
    <input type="text" name="q" placeholder="Search by patient name, cancer type, or hospital..." value="<?= e($term) ?>">
    <input type="hidden" name="filter" id="filterValue" value="<?= e($filter) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <div class="filter-pills">
    <button type="button" class="filter-pill <?= $filter === 'recent' ? 'active' : '' ?>" data-filter="recent">Recently Added</button>
    <button type="button" class="filter-pill <?= $filter === 'most_funded' ? 'active' : '' ?>" data-filter="most_funded">Most Funded</button>
    <button type="button" class="filter-pill <?= $filter === 'almost_funded' ? 'active' : '' ?>" data-filter="almost_funded">Almost Fully Funded</button>
    <button type="button" class="filter-pill <?= $filter === 'fully_funded' ? 'active' : '' ?>" data-filter="fully_funded">Fully Funded</button>
  </div>

  <?php if ($results): ?>
  <div class="campaign-grid">
    <?php foreach ($results as $c):
      $progress = calc_progress((float)$c['raised_amount'], (float)$c['target_amount']);
      $remaining = calc_remaining((float)$c['raised_amount'], (float)$c['target_amount']);
      $photo = $c['patient_photo'] ? UPLOAD_PATIENTS_URL . e($c['patient_photo']) : BASE_URL . 'css/placeholder-patient.svg';
    ?>
      <div class="campaign-card">
        <img class="campaign-photo" src="<?= $photo ?>" alt="<?= e($c['patient_name']) ?>" onerror="this.src='<?= BASE_URL ?>css/placeholder-patient.svg'">
        <div class="campaign-body">
          <span class="badge badge-verified">Verified</span>
          <?php if ($c['campaign_status'] === 'fully_funded'): ?><span class="badge badge-fully-funded">Fully Funded</span><?php endif; ?>
          <h3><?= e($c['patient_name']) ?></h3>
          <div class="campaign-meta"><?= e($c['cancer_type']) ?> &middot; <?= e($c['hospital']) ?></div>
          <div class="progress-bar-track"><div class="progress-bar-fill" data-progress="<?= $progress ?>"></div></div>
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
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <div class="empty-state">
      <h3>No campaigns found</h3>
      <p>Try a different search term or filter.</p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
