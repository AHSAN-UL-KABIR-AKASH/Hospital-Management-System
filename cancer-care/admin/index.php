<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Donation.php';

require_admin();

$db = getDB();
$campaignModel = new Campaign($db);
$userModel = new User($db);
$donationModel = new Donation($db);

$totalUsers = $userModel->totalCount();
$totalCampaigns = (int) $db->query('SELECT COUNT(*) FROM campaigns')->fetchColumn();
$pendingCampaigns = (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='pending'")->fetchColumn();
$verifiedCampaigns = (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified'")->fetchColumn();
$totalDonationsCount = (int) $db->query("SELECT COUNT(*) FROM donations WHERE payment_status='completed'")->fetchColumn();
$totalRaised = (float) $db->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status='completed'")->fetchColumn();
$activeCampaigns = (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified' AND campaign_status='active'")->fetchColumn();
$fullyFundedCampaigns = (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE campaign_status='fully_funded'")->fetchColumn();

$pageTitle = 'Admin Dashboard';
$adminActive = 'index';
include __DIR__ . '/admin_header.php';
?>
<h1>Admin Dashboard</h1>
<div class="dashboard-grid">
  <div class="dashboard-card"><div class="value"><?= number_format($totalUsers) ?></div><div class="label">Total Users</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($totalCampaigns) ?></div><div class="label">Total Campaigns</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($pendingCampaigns) ?></div><div class="label">Pending Campaigns</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($verifiedCampaigns) ?></div><div class="label">Verified Campaigns</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($totalDonationsCount) ?></div><div class="label">Total Donations</div></div>
  <div class="dashboard-card"><div class="value"><?= money($totalRaised) ?></div><div class="label">Total Amount Raised</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($activeCampaigns) ?></div><div class="label">Active Campaigns</div></div>
  <div class="dashboard-card"><div class="value"><?= number_format($fullyFundedCampaigns) ?></div><div class="label">Fully Funded Campaigns</div></div>
</div>

<?php if ($pendingCampaigns > 0): ?>
  <div class="alert alert-error" style="max-width:100%;">
    You have <?= $pendingCampaigns ?> campaign(s) awaiting verification.
    <a href="verification.php">Review now &rarr;</a>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/admin_footer.php'; ?>
