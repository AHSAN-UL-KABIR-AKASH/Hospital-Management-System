<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';
require_once __DIR__ . '/../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$user = api_require_login();
$campaignModel = new Campaign(getDB());

// Uses $_POST directly (not api_input) because file uploads require multipart/form-data.
$fields = [
    'patient_name' => trim($_POST['patient_name'] ?? ''),
    'age' => trim($_POST['age'] ?? ''),
    'cancer_type' => trim($_POST['cancer_type'] ?? ''),
    'cancer_stage' => trim($_POST['cancer_stage'] ?? ''),
    'hospital' => trim($_POST['hospital'] ?? ''),
    'treatment_details' => trim($_POST['treatment_details'] ?? ''),
    'target_amount' => trim($_POST['target_amount'] ?? ''),
    'story' => trim($_POST['story'] ?? ''),
    'contact_information' => trim($_POST['contact_information'] ?? ''),
];

$errors = [];
if (mb_strlen($fields['patient_name']) < 2) $errors[] = 'patient_name is required.';
if (!ctype_digit($fields['age']) || (int) $fields['age'] > 120) $errors[] = 'A valid age is required.';
if ($fields['cancer_type'] === '') $errors[] = 'cancer_type is required.';
if ($fields['cancer_stage'] === '') $errors[] = 'cancer_stage is required.';
if ($fields['hospital'] === '') $errors[] = 'hospital is required.';
if (!is_numeric($fields['target_amount']) || (float) $fields['target_amount'] < 1000) $errors[] = 'target_amount must be at least 1000.';
if (mb_strlen($fields['story']) < 30) $errors[] = 'story must be at least 30 characters.';
if ($fields['contact_information'] === '') $errors[] = 'contact_information is required.';

if ($errors) {
    json_response(['success' => false, 'message' => implode(' ', $errors)], 422);
}

$photoFilename = null;
if (!empty($_FILES['patient_photo']['name'])) {
    [$ok, $msg] = validate_image_upload($_FILES['patient_photo']);
    if (!$ok) {
        json_response(['success' => false, 'message' => $msg], 422);
    }
    $photoFilename = safe_filename($_FILES['patient_photo']['name']);
    move_uploaded_file($_FILES['patient_photo']['tmp_name'], UPLOAD_PATIENTS_DIR . $photoFilename);
}

$campaignId = $campaignModel->create([
    'user_id' => $user['id'],
    'patient_name' => $fields['patient_name'],
    'patient_photo' => $photoFilename,
    'age' => (int) $fields['age'],
    'cancer_type' => $fields['cancer_type'],
    'cancer_stage' => $fields['cancer_stage'],
    'hospital' => $fields['hospital'],
    'treatment_details' => $fields['treatment_details'],
    'target_amount' => (float) $fields['target_amount'],
    'story' => $fields['story'],
    'contact_information' => $fields['contact_information'],
]);

if ($user['role'] === 'donor') {
    (new User(getDB()))->promoteToFundraiser($user['id']);
}

json_response([
    'success' => true,
    'message' => 'Campaign submitted for verification.',
    'data' => ['campaign_id' => $campaignId, 'verification_status' => 'pending'],
], 201);
