<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Donation.php';

require_admin();
$donationModel = new Donation(getDB());
$donations = $donationModel->allForAdmin(300);

$db = getDB();
$totalRaised = (float) $db->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status='completed'")->fetchColumn();
$totalCount = (int) $db->query("SELECT COUNT(*) FROM donations WHERE payment_status='completed'")->fetchColumn();

$pageTitle = 'Donations';
$adminActive = 'donations';
include __DIR__ . '/admin_header.php';
?>
<h1>All Donations</h1>
<div class="dashboard-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 500px;">
  <div class="dashboard-card"><div class="value"><?= number_format($totalCount) ?></div><div class="label">Total Donations</div></div>
  <div class="dashboard-card"><div class="value"><?= money($totalRaised) ?></div><div class="label">Total Raised</div></div>
</div>

<table class="data-table">
  <thead><tr><th>Donor</th><th>Patient</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Date</th></tr></thead>
  <tbody>
    <?php foreach ($donations as $d): ?>
      <tr>
        <td><?= $d['anonymous'] ? 'Anonymous' : e($d['donor_name']) ?></td>
        <td><a href="../campaign.php?id=<?= (int)$d['campaign_id'] ?>" target="_blank"><?= e($d['patient_name']) ?></a></td>
        <td><?= money((float)$d['amount']) ?></td>
        <td><?= e(strtoupper($d['payment_method'])) ?></td>
        <td><?= e($d['transaction_id']) ?></td>
        <td><?= date('d M Y, h:i A', strtotime($d['created_at'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/admin_footer.php'; ?>
