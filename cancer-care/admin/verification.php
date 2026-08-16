<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Campaign.php';

require_admin();
$campaignModel = new Campaign(getDB());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid session token. Please try again.');
    } else {
        $campaignId = (int) ($_POST['campaign_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        if ($campaignId && in_array($action, ['verified', 'rejected', 'suspended'], true)) {
            $campaignModel->setVerificationStatus($campaignId, $action);
            flash('success', 'Campaign marked as ' . $action . '.');
        }
    }
    redirect('admin/verification.php');
}

$detailId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$pageTitle = 'Campaign Verification';
$adminActive = 'verification';
include __DIR__ . '/admin_header.php';

if ($detailId) {
    $campaign = $campaignModel->findById($detailId);
    if (!$campaign) {
        echo '<div class="empty-state"><h3>Campaign not found</h3><a href="verification.php" class="btn btn-primary">Back to list</a></div>';
    } else {
        $documents = $campaignModel->documentsFor($detailId);
        ?>
        <a href="verification.php">&larr; Back to verification queue</a>
        <h1><?= e($campaign['patient_name']) ?></h1>
        <p>Submitted by <?= e($campaign['creator_name']) ?> (<?= e($campaign['creator_email']) ?>)</p>

        <table class="data-table">
          <tbody>
            <tr><th>Age</th><td><?= (int)$campaign['age'] ?></td></tr>
            <tr><th>Cancer Type</th><td><?= e($campaign['cancer_type']) ?></td></tr>
            <tr><th>Cancer Stage</th><td><?= e($campaign['cancer_stage']) ?></td></tr>
            <tr><th>Hospital</th><td><?= e($campaign['hospital']) ?></td></tr>
            <tr><th>Treatment Details</th><td><?= nl2br(e($campaign['treatment_details'])) ?></td></tr>
            <tr><th>Required Amount</th><td><?= money((float)$campaign['target_amount']) ?></td></tr>
            <tr><th>Patient Story</th><td><?= nl2br(e($campaign['story'])) ?></td></tr>
            <tr><th>Contact Info</th><td><?= e($campaign['contact_information']) ?></td></tr>
            <tr><th>Current Status</th><td><span class="badge badge-<?= $campaign['verification_status'] === 'verified' ? 'verified' : ($campaign['verification_status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= e(ucfirst($campaign['verification_status'])) ?></span></td></tr>
          </tbody>
        </table>

        <h2>Medical Documents (Private)</h2>
        <?php if ($documents): ?>
          <table class="data-table">
            <thead><tr><th>Document</th><th>Type</th><th>Uploaded</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach ($documents as $doc): ?>
                <tr>
                  <td><?= e($doc['document_name']) ?></td>
                  <td><?= e($doc['document_type']) ?></td>
                  <td><?= date('d M Y', strtotime($doc['uploaded_at'])) ?></td>
                  <td><a href="view_document.php?doc=<?= (int)$doc['id'] ?>" target="_blank" class="btn btn-outline btn-sm">View Document</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="empty-state">No medical documents were uploaded for this campaign.</p>
        <?php endif; ?>

        <div style="display:flex; gap:12px; margin-top:24px;">
          <form method="POST" action="verification.php">
            <?= csrf_field() ?>
            <input type="hidden" name="campaign_id" value="<?= (int)$campaign['id'] ?>">
            <input type="hidden" name="action" value="verified">
            <button type="submit" class="btn btn-accent">Approve &amp; Verify</button>
          </form>
          <form method="POST" action="verification.php">
            <?= csrf_field() ?>
            <input type="hidden" name="campaign_id" value="<?= (int)$campaign['id'] ?>">
            <input type="hidden" name="action" value="rejected">
            <button type="submit" class="btn btn-danger">Reject</button>
          </form>
        </div>
        <?php
    }
} else {
    $pending = $campaignModel->allForAdmin('pending');
    ?>
    <h1>Campaign Verification Queue</h1>
    <?php if ($pending): ?>
      <table class="data-table">
        <thead><tr><th>Patient</th><th>Cancer Type</th><th>Hospital</th><th>Target</th><th>Submitted</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach ($pending as $c): ?>
            <tr>
              <td><?= e($c['patient_name']) ?></td>
              <td><?= e($c['cancer_type']) ?></td>
              <td><?= e($c['hospital']) ?></td>
              <td><?= money((float)$c['target_amount']) ?></td>
              <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
              <td><a href="verification.php?id=<?= (int)$c['id'] ?>" class="btn btn-primary btn-sm">Review</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-state"><h3>No campaigns pending verification</h3><p>All caught up!</p></div>
    <?php endif; ?>
    <?php
}
include __DIR__ . '/admin_footer.php';
?>
