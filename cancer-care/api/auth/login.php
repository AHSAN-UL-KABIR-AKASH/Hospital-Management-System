<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = api_input();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    json_response(['success' => false, 'message' => 'Email and password are required.'], 422);
}

$userModel = new User(getDB());
$user = $userModel->findByEmail($email);

if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
    json_response(['success' => false, 'message' => 'Invalid email or password.'], 401);
}
if ($user['status'] === 'suspended') {
    json_response(['success' => false, 'message' => 'Account suspended.'], 403);
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];

json_response([
    'success' => true,
    'message' => 'Login successful',
    'data' => [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ],
]);
