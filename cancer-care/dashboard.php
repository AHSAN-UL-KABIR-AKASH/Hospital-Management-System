<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';
require_once __DIR__ . '/models/Donation.php';

require_login();
$user = current_user();
$campaignModel = new Campaign(getDB());
$donationModel = new Donation(getDB());

$myCampaigns = ($user['role'] === 'fundraiser' || $user['role'] === 'admin') ? $campaignModel->byUser($user['id']) : [];
$myDonations = $donationModel->historyForUser($user['id']);
$totalDonated = $donationModel->totalDonatedByUser($user['id']);
$donationCount = $donationModel->countByUser($user['id']);

$totalRaised = 0.0;
$totalDonors = 0;
foreach ($myCampaigns as $c) {
    $totalRaised += (float) $c['raised_amount'];
    $totalDonors += (int) $c['donor_count'];
}

$statusBadgeClass = [
    'verified' => 'badge-verified', 'pending' => 'badge-pending',
    'rejected' => 'badge-rejected', 'suspended' => 'badge-suspended',
];

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h1>Welcome, <?= e($user['name']) ?></h1>

  <div class="dashboard-grid">
    <div class="dashboard-card">
      <div class="value"><?= money($totalDonated) ?></div>
      <div class="label">Total Donated</div>
    </div>
    <div class="dashboard-card">
      <div class="value"><?= number_format($donationCount) ?></div>
      <div class="label">Number of Donations</div>
    </div>
    <?php if ($myCampaigns): ?>
    <div class="dashboard-card">
      <div class="value"><?= money($totalRaised) ?></div>
      <div class="label">Total Raised (My Campaigns)</div>
    </div>
    <div class="dashboard-card">
      <div class="value"><?= number_format($totalDonors) ?></div>
      <div class="label">Total Donors (My Campaigns)</div>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($myCampaigns): ?>
  <div class="section">
    <div class="section-header"><h2>My Campaigns</h2><a href="create_campaign.php" class="btn btn-primary btn-sm">+ New Fundraiser</a></div>
    <table class="data-table">
      <thead>
        <tr><th>Patient</th><th>Target</th><th>Raised</th><th>Progress</th><th>Verification</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($myCampaigns as $c): $progress = calc_progress((float)$c['raised_amount'], (float)$c['target_amount']); ?>
          <tr>
            <td><?= e($c['patient_name']) ?></td>
            <td><?= money((float)$c['target_amount']) ?></td>
            <td><?= money((float)$c['raised_amount']) ?></td>
            <td><?= $progress ?>%</td>
            <td><span class="badge <?= $statusBadgeClass[$c['verification_status']] ?? 'badge-pending' ?>"><?= e(ucfirst($c['verification_status'])) ?></span></td>
            <td><?= e(ucfirst(str_replace('_', ' ', $c['campaign_status']))) ?></td>
            <td><a href="campaign.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="section">
    <div class="empty-state">
      <h3>No fundraisers yet</h3>
      <p>Start a campaign for a cancer patient who needs help.</p>
      <a href="create_campaign.php" class="btn btn-primary">Create Fundraiser</a>
    </div>
  </div>
  <?php endif; ?>

  <div class="section">
    <div class="section-header">
      <h2>My Donation History</h2>
      <a href="saved_campaigns.php">Saved Campaigns &rarr;</a>
    </div>
    <?php if ($myDonations): ?>
    <table class="data-table">
      <thead><tr><th>Patient</th><th>Amount</th><th>Date</th><th>Message</th></tr></thead>
      <tbody>
        <?php foreach ($myDonations as $d): ?>
          <tr>
            <td><a href="campaign.php?id=<?= (int)$d['campaign_id'] ?>"><?= e($d['patient_name']) ?></a></td>
            <td><?= money((float)$d['amount']) ?></td>
            <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
            <td><?= e($d['message'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p class="empty-state">You haven't made any donations yet. <a href="search.php">Browse patients</a> who need help.</p>
    <?php endif; ?>
  </div>

  <div class="section">
    <h2>Profile</h2>
    <table class="data-table">
      <tbody>
        <tr><th>Name</th><td><?= e($user['name']) ?></td></tr>
        <tr><th>Email</th><td><?= e($user['email']) ?></td></tr>
        <tr><th>Phone</th><td><?= e($user['phone']) ?></td></tr>
        <tr><th>Role</th><td><?= e(ucfirst($user['role'])) ?></td></tr>
      </tbody>
    </table>
    <a href="profile.php" class="btn btn-outline btn-sm" style="margin-top:14px;">Edit Profile</a>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
