<?php
require_once __DIR__ . '/../bootstrap.php';

$user = api_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = getDB()->prepare(
        "SELECT c.id, c.patient_name, c.cancer_type, c.hospital, c.target_amount, c.raised_amount
         FROM saved_campaigns sc JOIN campaigns c ON c.id = sc.campaign_id
         WHERE sc.user_id = ? ORDER BY sc.created_at DESC"
    );
    $stmt->execute([$user['id']]);
    json_response(['success' => true, 'data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $campaignId = (int) ($input['campaign_id'] ?? 0);
    $action = $input['action'] ?? 'save';

    if (!$campaignId) {
        json_response(['success' => false, 'message' => 'campaign_id is required.'], 422);
    }

    $db = getDB();
    if ($action === 'remove') {
        $stmt = $db->prepare('DELETE FROM saved_campaigns WHERE user_id = ? AND campaign_id = ?');
        $stmt->execute([$user['id'], $campaignId]);
        json_response(['success' => true, 'message' => 'Campaign removed from saved list.']);
    }

    $stmt = $db->prepare('INSERT IGNORE INTO saved_campaigns (user_id, campaign_id) VALUES (?, ?)');
    $stmt->execute([$user['id'], $campaignId]);
    json_response(['success' => true, 'message' => 'Campaign saved.']);
}

json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
