<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/User.php';

api_require_admin();
$userModel = new User(getDB());

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $term = trim($_GET['q'] ?? '');
    $users = $term !== '' ? $userModel->search($term) : $userModel->all(200);
    json_response(['success' => true, 'data' => $users, 'meta' => ['total' => $userModel->totalCount()]]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $targetId = (int) ($input['user_id'] ?? 0);
    $status = $input['status'] ?? '';

    if (!$targetId || !in_array($status, ['active', 'suspended'], true)) {
        json_response(['success' => false, 'message' => 'user_id and a valid status are required.'], 422);
    }

    $admin = current_user();
    if ($targetId === (int) $admin['id']) {
        json_response(['success' => false, 'message' => 'You cannot change your own account status.'], 400);
    }

    $userModel->setStatus($targetId, $status);
    json_response(['success' => true, 'message' => "User status updated to $status."]);
}

json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
