<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$campaignModel = new Campaign(getDB());
$term = trim($_GET['q'] ?? '');
$filter = trim($_GET['filter'] ?? 'recent');
$allowedFilters = ['recent', 'most_funded', 'almost_funded', 'fully_funded'];
if (!in_array($filter, $allowedFilters, true)) $filter = 'recent';

$results = $campaignModel->search($term, $filter);

$data = array_map(function ($c) {
    return [
        'id' => (int) $c['id'],
        'patient_name' => $c['patient_name'],
        'cancer_type' => $c['cancer_type'],
        'hospital' => $c['hospital'],
        'target_amount' => (float) $c['target_amount'],
        'raised_amount' => (float) $c['raised_amount'],
        'progress' => calc_progress((float) $c['raised_amount'], (float) $c['target_amount']),
        'campaign_status' => $c['campaign_status'],
    ];
}, $results);

json_response(['success' => true, 'data' => $data]);
