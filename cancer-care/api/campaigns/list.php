<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$campaignModel = new Campaign(getDB());
$limit = min(50, max(1, (int) ($_GET['limit'] ?? 20)));
$offset = max(0, (int) ($_GET['offset'] ?? 0));

$campaigns = $campaignModel->publicList($limit, $offset);

$data = array_map(function ($c) {
    return [
        'id' => (int) $c['id'],
        'patient_name' => $c['patient_name'],
        'patient_photo' => $c['patient_photo'] ? UPLOAD_PATIENTS_URL . $c['patient_photo'] : null,
        'cancer_type' => $c['cancer_type'],
        'hospital' => $c['hospital'],
        'target_amount' => (float) $c['target_amount'],
        'raised_amount' => (float) $c['raised_amount'],
        'remaining_amount' => calc_remaining((float) $c['raised_amount'], (float) $c['target_amount']),
        'progress' => calc_progress((float) $c['raised_amount'], (float) $c['target_amount']),
        'donor_count' => (int) $c['donor_count'],
        'campaign_status' => $c['campaign_status'],
    ];
}, $campaigns);

json_response(['success' => true, 'data' => $data, 'meta' => ['limit' => $limit, 'offset' => $offset]]);
