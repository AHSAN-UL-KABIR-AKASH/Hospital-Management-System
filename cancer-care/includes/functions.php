<?php
/**
 * Shared helper functions used across the site.
 */

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatMoney($amount): string {
    return '৳' . number_format((float)$amount, 2);
}

function progressPercent($raised, $target): int {
    $target = (float)$target;
    if ($target <= 0) return 0;
    $pct = ((float)$raised / $target) * 100;
    return (int) max(0, min(100, round($pct)));
}

function remainingAmount($raised, $target): float {
    return max(0, (float)$target - (float)$raised);
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function generateTransactionId(): string {
    return 'TXN-' . strtoupper(bin2hex(random_bytes(6))) . '-' . time();
}

function flash(string $key, ?string $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/** CSRF token helpers */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Validate & move an uploaded image (patient photo / profile photo) */
function handleImageUpload(array $file, string $destDir, array $allowedExt = ['jpg','jpeg','png','webp']): ?string {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return null;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMime, true)) {
        return null;
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        return null;
    }
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $newName = uniqid('img_', true) . '.' . $ext;
    $target = rtrim($destDir, '/') . '/' . $newName;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return null;
    }
    return $newName;
}

/** Validate & move an uploaded medical document (never publicly accessible). */
function handleDocumentUpload(array $file, string $destDir): ?array {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowedExt, true)) {
        return null;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMime = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($mime, $allowedMime, true)) {
        return null;
    }
    if ($file['size'] > 10 * 1024 * 1024) { // 10MB max
        return null;
    }
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $newName = uniqid('doc_', true) . '.' . $ext;
    $target = rtrim($destDir, '/') . '/' . $newName;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return null;
    }
    return ['stored_name' => $newName, 'original_name' => basename($file['name'])];
}

function siteStats(PDO $pdo): array {
    $stats = [];
    $stats['total_patients'] = (int) $pdo->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified'")->fetchColumn();
    $stats['active_campaigns'] = (int) $pdo->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified' AND campaign_status='active'")->fetchColumn();
    $stats['total_donations'] = (int) $pdo->query("SELECT COUNT(*) FROM donations WHERE payment_status='completed'")->fetchColumn();
    $stats['total_raised'] = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status='completed'")->fetchColumn();
    return $stats;
}
