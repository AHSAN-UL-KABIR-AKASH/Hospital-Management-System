<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$user = api_require_login();
$campaignModel = new Campaign(getDB());

$input = api_input();
$id = (int) ($input['id'] ?? $_POST['id'] ?? 0);
if (!$id) {
    json_response(['success' => false, 'message' => 'Campaign id is required.'], 422);
}

$campaign = $campaignModel->findById($id);
if (!$campaign) {
    json_response(['success' => false, 'message' => 'Campaign not found.'], 404);
}
if ((int) $campaign['user_id'] !== (int) $user['id'] && $user['role'] !== 'admin') {
    json_response(['success' => false, 'message' => 'You do not have permission to edit this campaign.'], 403);
}

// Only allow editing a limited, safe set of fields. Editing does NOT change
// verification_status, raised_amount, or campaign_status via this endpoint.
$db = getDB();
$allowed = ['treatment_details', 'story', 'contact_information'];
$updates = [];
$params = [];
foreach ($allowed as $field) {
    if (array_key_exists($field, $input)) {
        $updates[] = "$field = ?";
        $params[] = trim((string) $input[$field]);
    }
}

if (!$updates) {
    json_response(['success' => false, 'message' => 'No editable fields were provided.'], 422);
}

$params[] = $id;
$stmt = $db->prepare('UPDATE campaigns SET ' . implode(', ', $updates) . ' WHERE id = ?');
$stmt->execute($params);

json_response(['success' => true, 'message' => 'Campaign updated.']);
