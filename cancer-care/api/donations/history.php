<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Donation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$user = api_require_login();
$donationModel = new Donation(getDB());
$history = $donationModel->historyForUser($user['id']);

$data = array_map(function ($d) {
    return [
        'campaign_id' => (int) $d['campaign_id'],
        'patient_name' => $d['patient_name'],
        'amount' => (float) $d['amount'],
        'anonymous' => (bool) $d['anonymous'],
        'message' => $d['message'],
        'payment_method' => $d['payment_method'],
        'transaction_id' => $d['transaction_id'],
        'created_at' => $d['created_at'],
    ];
}, $history);

json_response([
    'success' => true,
    'data' => $data,
    'meta' => [
        'total_donated' => $donationModel->totalDonatedByUser($user['id']),
        'donation_count' => $donationModel->countByUser($user['id']),
    ],
]);
