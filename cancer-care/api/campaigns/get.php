<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';
require_once __DIR__ . '/../../models/Donation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    json_response(['success' => false, 'message' => 'Campaign id is required.'], 422);
}

$campaignModel = new Campaign(getDB());
$campaign = $campaignModel->findById($id);

$currentUser = current_user();
$isOwner = $currentUser && $campaign && (int) $campaign['user_id'] === (int) $currentUser['id'];
$isAdmin = $currentUser && $currentUser['role'] === 'admin';

if (!$campaign || ($campaign['verification_status'] !== 'verified' && !$isOwner && !$isAdmin)) {
    json_response(['success' => false, 'message' => 'Campaign not found.'], 404);
}

$donationModel = new Donation(getDB());
$donations = $donationModel->historyForCampaign($id);

json_response([
    'success' => true,
    'data' => [
        'id' => (int) $campaign['id'],
        'patient_name' => $campaign['patient_name'],
        'patient_photo' => $campaign['patient_photo'] ? UPLOAD_PATIENTS_URL . $campaign['patient_photo'] : null,
        'age' => (int) $campaign['age'],
        'cancer_type' => $campaign['cancer_type'],
        'cancer_stage' => $campaign['cancer_stage'],
        'hospital' => $campaign['hospital'],
        'treatment_details' => $campaign['treatment_details'],
        'story' => $campaign['story'],
        'target_amount' => (float) $campaign['target_amount'],
        'raised_amount' => (float) $campaign['raised_amount'],
        'remaining_amount' => calc_remaining((float) $campaign['raised_amount'], (float) $campaign['target_amount']),
        'progress' => calc_progress((float) $campaign['raised_amount'], (float) $campaign['target_amount']),
        'donor_count' => (int) $campaign['donor_count'],
        'verification_status' => $campaign['verification_status'],
        'campaign_status' => $campaign['campaign_status'],
        'donations' => array_map(function ($d) {
            return [
                'donor_name' => $d['anonymous'] ? 'Anonymous Donor' : $d['donor_name'],
                'amount' => (float) $d['amount'],
                'message' => $d['message'],
                'created_at' => $d['created_at'],
            ];
        }, $donations),
    ],
]);
