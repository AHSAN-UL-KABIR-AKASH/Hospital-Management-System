</main>
<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <h4><?= e(SITE_NAME) ?></h4>
      <p>A trusted platform connecting verified cancer patients with generous donors.</p>
    </div>
    <div>
      <h5>Company</h5>
      <a href="<?= BASE_URL ?>about.php">About</a>
      <a href="<?= BASE_URL ?>contact.php">Contact</a>
      <a href="<?= BASE_URL ?>how-it-works.php">How It Works</a>
    </div>
    <div>
      <h5>Legal</h5>
      <a href="<?= BASE_URL ?>privacy.php">Privacy Policy</a>
      <a href="<?= BASE_URL ?>terms.php">Terms &amp; Conditions</a>
      <a href="<?= BASE_URL ?>donation-policy.php">Donation Policy</a>
      <a href="<?= BASE_URL ?>refund-policy.php">Refund Policy</a>
    </div>
  </div>
  <div class="container footer-bottom">
    &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.
  </div>
</footer>
<script src="<?= BASE_URL ?>js/script.js"></script>
</body>
</html>
