<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Donation.php';

require_login();
$user = current_user();
$donationModel = new Donation(getDB());
$donations = $donationModel->historyForUser($user['id']);
$total = $donationModel->totalDonatedByUser($user['id']);

$pageTitle = 'Donation History';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="section-header">
    <h1>My Donation History</h1>
    <div class="dashboard-card" style="text-align:right;">
      <div class="value"><?= money($total) ?></div>
      <div class="label">Total Donated</div>
    </div>
  </div>

  <?php if ($donations): ?>
  <table class="data-table">
    <thead><tr><th>Patient</th><th>Amount</th><th>Date</th><th>Payment Method</th><th>Transaction ID</th><th>Message</th></tr></thead>
    <tbody>
      <?php foreach ($donations as $d): ?>
        <tr>
          <td><a href="campaign.php?id=<?= (int)$d['campaign_id'] ?>"><?= e($d['patient_name']) ?></a></td>
          <td><?= money((float)$d['amount']) ?></td>
          <td><?= date('d M Y, h:i A', strtotime($d['created_at'])) ?></td>
          <td><?= e(strtoupper($d['payment_method'])) ?> (Demo)</td>
          <td><?= e($d['transaction_id']) ?></td>
          <td><?= e($d['message'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="empty-state">
      <h3>No donations yet</h3>
      <p>Browse verified patients and make your first donation.</p>
      <a href="search.php" class="btn btn-primary">Find Patients</a>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
