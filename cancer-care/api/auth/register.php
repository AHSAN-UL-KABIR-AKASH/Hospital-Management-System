<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = api_input();
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$password = $input['password'] ?? '';

$errors = [];
if (mb_strlen($name) < 2) $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if ($phone === '' || !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) $errors[] = 'A valid phone number is required.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';

$userModel = new User(getDB());
if (!$errors && $userModel->emailExists($email)) {
    $errors[] = 'An account with this email already exists.';
}

if ($errors) {
    json_response(['success' => false, 'message' => implode(' ', $errors)], 422);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$userId = $userModel->create($name, $email, $phone, $hash, 'donor');
$_SESSION['user_id'] = $userId;

json_response([
    'success' => true,
    'message' => 'Registration successful',
    'data' => ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => 'donor'],
], 201);
