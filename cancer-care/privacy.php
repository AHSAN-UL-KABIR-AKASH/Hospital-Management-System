<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Privacy Policy';
include __DIR__ . '/includes/header.php';
?>
<div class="static-page">
  <h1>Privacy Policy</h1>
  <p>Cancer Care is committed to protecting the privacy of our users, patients, and donors.</p>
  <h2>Information We Collect</h2>
  <p>We collect account information (name, email, phone), campaign information (patient details, medical documents), and donation records.</p>
  <h2>Medical Documents</h2>
  <p>Medical documents uploaded for campaign verification are private and are only accessible to authorized administrators for the purpose of verifying a patient's condition.</p>
  <h2>Donor Privacy</h2>
  <p>Donors may choose to donate anonymously. We never expose private payment information publicly.</p>
  <h2>Data Security</h2>
  <p>Passwords are stored using industry-standard one-way hashing. We use prepared statements and input validation throughout the platform to protect against common security threats.</p>
  <h2>Contact</h2>
  <p>For privacy-related questions, please reach out through our <a href="contact.php">Contact page</a>.</p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
