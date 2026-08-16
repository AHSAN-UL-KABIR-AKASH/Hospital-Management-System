<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';

api_require_admin();
$campaignModel = new Campaign(getDB());

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // List campaigns pending verification (or by status query param)
    $status = trim($_GET['status'] ?? 'pending');
    $campaigns = $campaignModel->allForAdmin($status);

    $data = array_map(function ($c) {
        return [
            'id' => (int) $c['id'],
            'patient_name' => $c['patient_name'],
            'creator_name' => $c['creator_name'],
            'cancer_type' => $c['cancer_type'],
            'hospital' => $c['hospital'],
            'target_amount' => (float) $c['target_amount'],
            'verification_status' => $c['verification_status'],
            'created_at' => $c['created_at'],
        ];
    }, $campaigns);

    json_response(['success' => true, 'data' => $data]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $campaignId = (int) ($input['campaign_id'] ?? 0);
    $status = $input['status'] ?? '';

    if (!$campaignId || !in_array($status, ['verified', 'rejected', 'suspended', 'pending'], true)) {
        json_response(['success' => false, 'message' => 'campaign_id and a valid status are required.'], 422);
    }

    $campaign = $campaignModel->findById($campaignId);
    if (!$campaign) {
        json_response(['success' => false, 'message' => 'Campaign not found.'], 404);
    }

    $campaignModel->setVerificationStatus($campaignId, $status);
    json_response(['success' => true, 'message' => "Campaign marked as $status."]);
}

json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
