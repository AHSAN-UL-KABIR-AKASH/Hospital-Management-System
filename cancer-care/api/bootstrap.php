<?php
/**
 * Shared bootstrap for all API endpoints.
 * Ensures consistent session handling and JSON responses.
 * Authentication here uses the same PHP session cookie as the website.
 * (A future native mobile/PC app would authenticate via api/auth/login.php
 * and reuse the returned session cookie, or this can be swapped for
 * token-based auth later without touching the model layer.)
 */
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

function api_input(): array {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        return $json;
    }
    return $_POST;
}

function api_require_login(): array {
    $user = current_user();
    if (!$user) {
        json_response(['success' => false, 'message' => 'Authentication required.'], 401);
    }
    return $user;
}

function api_require_admin(): array {
    $user = api_require_login();
    if ($user['role'] !== 'admin') {
        json_response(['success' => false, 'message' => 'Admin access required.'], 403);
    }
    return $user;
}
