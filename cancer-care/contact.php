<?php
require_once __DIR__ . '/includes/auth.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (csrf_verify()) {
        // Demo only: in production this would send an email or store a support ticket.
        $sent = true;
    }
}

$pageTitle = 'Contact Us';
include __DIR__ . '/includes/header.php';
?>
<div class="static-page">
  <h1>Contact Us</h1>
  <p>Have a question, concern, or need help with a campaign? Reach out to our support team.</p>

  <?php if ($sent): ?>
    <div class="alert alert-success">Thank you for reaching out. Our team will respond to you shortly.</div>
  <?php endif; ?>

  <form method="POST" action="contact.php" class="js-loading-state" style="max-width:520px;">
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="message">Message</label>
      <textarea id="message" name="message" class="form-control" required rows="5"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Send Message</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
