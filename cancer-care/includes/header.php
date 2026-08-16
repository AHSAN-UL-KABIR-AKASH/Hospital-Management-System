<?php
// Expects $pageTitle to optionally be set before including this file.
$pageTitle = $pageTitle ?? SITE_NAME;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="site-main">
<?php
$flashSuccess = flash('success');
$flashError = flash('error');
if ($flashSuccess): ?>
    <div class="alert alert-success container"><?= e($flashSuccess) ?></div>
<?php endif;
if ($flashError): ?>
    <div class="alert alert-error container"><?= e($flashError) ?></div>
<?php endif; ?>
