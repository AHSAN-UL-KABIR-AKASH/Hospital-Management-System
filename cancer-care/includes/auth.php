<?php
/**
 * Session bootstrap + authentication/authorization helpers.
 * Include this file at the very top of any page (before output).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        $stmt = getDB()->prepare('SELECT id, name, email, phone, role, profile_photo, status FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
        if ($user && $user['status'] === 'suspended') {
            session_destroy();
            $user = null;
        }
    }
    return $user;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function require_login(): void {
    if (!is_logged_in()) {
        flash('error', 'Please login to continue.');
        redirect('login.php');
    }
}

function require_role(string ...$roles): void {
    require_login();
    $user = current_user();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        die('Access denied. You do not have permission to view this page.');
    }
}

function require_admin(): void {
    require_role('admin');
}

function is_admin(): bool {
    $u = current_user();
    return $u && $u['role'] === 'admin';
}
