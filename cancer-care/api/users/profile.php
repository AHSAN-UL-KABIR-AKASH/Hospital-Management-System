<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/User.php';

$user = api_require_login();
$userModel = new User(getDB());

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response([
        'success' => true,
        'data' => [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
        ],
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $name = trim($input['name'] ?? $user['name']);
    $phone = trim($input['phone'] ?? $user['phone']);

    if (mb_strlen($name) < 2 || !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        json_response(['success' => false, 'message' => 'Invalid name or phone number.'], 422);
    }

    $userModel->updateProfile($user['id'], $name, $phone);
    json_response(['success' => true, 'message' => 'Profile updated', 'data' => ['name' => $name, 'phone' => $phone]]);
}

json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
