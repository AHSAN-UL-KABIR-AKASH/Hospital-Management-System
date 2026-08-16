<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';
require_once __DIR__ . '/models/Donation.php';

require_login();
$user = current_user();

$campaignId = isset($_GET['campaign_id']) ? (int) $_GET['campaign_id'] : (int) ($_POST['campaign_id'] ?? 0);
$campaignModel = new Campaign(getDB());
$campaign = $campaignId ? $campaignModel->findById($campaignId) : null;

if (!$campaign || $campaign['verification_status'] !== 'verified') {
    flash('error', 'This campaign is not available for donations.');
    redirect('search.php');
}

if ($campaign['campaign_status'] === 'fully_funded') {
    flash('error', 'This campaign has already reached its target amount. Thank you for your interest!');
    redirect('campaign.php?id=' . $campaignId);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid session token. Please try again.';
    } else {
        $amount = (float) ($_POST['amount'] ?? 0);
        $anonymous = isset($_POST['anonymous']);
        $message = trim($_POST['message'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'demo';

        if ($amount < 10) {
            $errors[] = 'Minimum donation amount is ৳10.';
        }
        if ($amount > 10000000) {
            $errors[] = 'Please enter a realistic donation amount.';
        }
        $allowedMethods = ['bkash', 'nagad', 'rocket', 'bank', 'card'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $errors[] = 'Please select a valid demo payment method.';
        }

        if (!$errors) {
            try {
                $donationModel = new Donation(getDB());
                $result = $donationModel->create($campaignId, $user['id'], $amount, $anonymous, $message ?: null, $paymentMethod);
                flash('success', 'Thank you! Your donation of ' . money($amount) . ' was successful. Transaction ID: ' . $result['transaction_id']);
                redirect('campaign.php?id=' . $campaignId);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
}

$progress = calc_progress((float)$campaign['raised_amount'], (float)$campaign['target_amount']);
$remaining = calc_remaining((float)$campaign['raised_amount'], (float)$campaign['target_amount']);

$pageTitle = 'Donate to ' . $campaign['patient_name'];
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="form-card">
    <h1>Donate to <?= e($campaign['patient_name']) ?></h1>
    <p><?= e($campaign['cancer_type']) ?> &middot; <?= e($campaign['hospital']) ?></p>
    <div class="progress-bar-track">
      <div class="progress-bar-fill" data-progress="<?= $progress ?>"></div>
    </div>
    <div class="campaign-amounts">
      <span>Raised: <strong><?= money((float)$campaign['raised_amount']) ?></strong> of <?= money((float)$campaign['target_amount']) ?></span>
      <span><?= $progress ?>%</span>
    </div>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" id="donateForm" action="donate.php" class="js-loading-state">
      <?= csrf_field() ?>
      <input type="hidden" name="campaign_id" value="<?= (int)$campaign['id'] ?>">

      <div class="form-group">
        <label>Quick Amount (<?= CURRENCY_SYMBOL ?>)</label>
        <div class="quick-amounts">
          <button type="button" class="quick-amount-btn" data-amount="100">৳100</button>
          <button type="button" class="quick-amount-btn" data-amount="500">৳500</button>
          <button type="button" class="quick-amount-btn" data-amount="1000">৳1,000</button>
          <button type="button" class="quick-amount-btn" data-amount="5000">৳5,000</button>
          <button type="button" class="quick-amount-btn" data-amount="custom">Custom</button>
        </div>
        <input type="number" id="donationAmount" name="amount" class="form-control" min="10" step="0.01" required placeholder="Enter amount">
        <div id="donateError" class="field-error"></div>
      </div>

      <div class="form-group">
        <label for="payment_method">Demo Payment Method</label>
        <select id="payment_method" name="payment_method" class="form-control" required>
          <option value="bkash">bKash (Demo)</option>
          <option value="nagad">Nagad (Demo)</option>
          <option value="rocket">Rocket (Demo)</option>
          <option value="bank">Bank Transfer (Demo)</option>
          <option value="card">Card (Demo)</option>
        </select>
        <div class="form-hint">This is a demo payment system. No real transaction will be made.</div>
      </div>

      <div class="form-group">
        <label for="message">Message (optional)</label>
        <textarea id="message" name="message" class="form-control" maxlength="500" placeholder="Leave a message of support..."></textarea>
      </div>

      <div class="form-group">
        <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
          <input type="checkbox" name="anonymous" value="1" style="width:auto;"> Donate anonymously
        </label>
      </div>

      <button type="submit" class="btn btn-accent btn-block">Confirm Donation</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
