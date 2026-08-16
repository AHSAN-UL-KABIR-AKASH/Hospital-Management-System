<?php
// Expects $pageTitle and $adminActive (current nav key) to be set before including.
require_admin();
$pageTitle = $pageTitle ?? 'Admin';
$adminActive = $adminActive ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?> Admin</title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<?php
$flashSuccess = flash('success');
$flashError = flash('error');
if ($flashSuccess): ?>
    <div class="alert alert-success container"><?= e($flashSuccess) ?></div>
<?php endif;
if ($flashError): ?>
    <div class="alert alert-error container"><?= e($flashError) ?></div>
<?php endif; ?>

<div class="admin-layout">
  <aside class="admin-sidebar">
    <a href="index.php" class="<?= $adminActive === 'index' ? 'active' : '' ?>">Dashboard</a>
    <a href="users.php" class="<?= $adminActive === 'users' ? 'active' : '' ?>">Users</a>
    <a href="campaigns.php" class="<?= $adminActive === 'campaigns' ? 'active' : '' ?>">Campaigns</a>
    <a href="verification.php" class="<?= $adminActive === 'verification' ? 'active' : '' ?>">Verification</a>
    <a href="donations.php" class="<?= $adminActive === 'donations' ? 'active' : '' ?>">Donations</a>
    <a href="reports.php" class="<?= $adminActive === 'reports' ? 'active' : '' ?>">Reports</a>
  </aside>
  <div class="admin-content">
