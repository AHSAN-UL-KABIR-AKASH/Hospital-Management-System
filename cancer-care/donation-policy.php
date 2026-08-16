<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Donation Policy';
include __DIR__ . '/includes/header.php';
?>
<div class="static-page">
  <h1>Donation Policy</h1>
  <p>This policy explains how donations are processed and applied on Cancer Care.</p>
  <h2>Demo Payment System</h2>
  <p>The current platform uses a demo donation system for development and testing purposes. No real financial transactions are processed. In a production deployment, donations would be processed through a licensed Bangladesh payment gateway (bKash, Nagad, Rocket, bank transfer, or card).</p>
  <h2>How Donations Are Applied</h2>
  <p>Each donation is immediately credited to the selected patient's campaign, updating the raised amount, remaining amount, and progress percentage in real time.</p>
  <h2>Anonymous Donations</h2>
  <p>Donors may choose to hide their identity. Anonymous donations are displayed publicly as "Anonymous Donor" while still being recorded internally for accounting purposes.</p>
  <h2>Fully Funded Campaigns</h2>
  <p>Once a campaign reaches its required amount, it is marked "Fully Funded" and no longer accepts further donations.</p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
