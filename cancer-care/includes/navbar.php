<?php $navUser = current_user(); ?>
<header class="navbar">
  <div class="container navbar-inner">
    <a href="<?= BASE_URL ?>index.php" class="navbar-logo">
      <span class="logo-icon">❤</span> <?= e(SITE_NAME) ?>
    </a>
    <button class="navbar-toggle" id="navToggle" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>
    <nav class="navbar-links" id="navLinks">
      <a href="<?= BASE_URL ?>index.php">Home</a>
      <a href="<?= BASE_URL ?>search.php">Find Patients</a>
      <a href="<?= BASE_URL ?>create_campaign.php">Create Fundraiser</a>
      <a href="<?= BASE_URL ?>how-it-works.php">How It Works</a>
      <a href="<?= BASE_URL ?>about.php">About</a>
      <?php if ($navUser): ?>
        <a href="<?= BASE_URL ?>dashboard.php">Dashboard</a>
        <?php if ($navUser['role'] === 'admin'): ?>
          <a href="<?= BASE_URL ?>admin/index.php">Admin</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline btn-sm">Logout</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>login.php">Login</a>
        <a href="<?= BASE_URL ?>register.php" class="btn btn-primary btn-sm">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
