<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../models/Campaign.php';
require_once __DIR__ . '/../../models/Donation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$user = api_require_login();
$input = api_input();

$campaignId = (int) ($input['campaign_id'] ?? 0);
$amount = (float) ($input['amount'] ?? 0);
$anonymous = !empty($input['anonymous']);
$message = isset($input['message']) ? trim((string) $input['message']) : null;
$paymentMethod = $input['payment_method'] ?? 'demo';

$allowedMethods = ['bkash', 'nagad', 'rocket', 'bank', 'card', 'demo'];
if (!in_array($paymentMethod, $allowedMethods, true)) {
    json_response(['success' => false, 'message' => 'Invalid demo payment method.'], 422);
}
if ($amount < 10 || $amount > 10000000) {
    json_response(['success' => false, 'message' => 'Please provide a valid donation amount.'], 422);
}

$campaignModel = new Campaign(getDB());
$campaign = $campaignModel->findById($campaignId);
if (!$campaign || $campaign['verification_status'] !== 'verified') {
    json_response(['success' => false, 'message' => 'Campaign not available for donations.'], 404);
}

try {
    $donationModel = new Donation(getDB());
    $result = $donationModel->create($campaignId, $user['id'], $amount, $anonymous, $message ?: null, $paymentMethod);

    json_response([
        'success' => true,
        'message' => 'Donation successful',
        'data' => [
            'campaign_id' => $campaignId,
            'donated_amount' => $result['donated_amount'],
            'total_raised' => $result['total_raised'],
            'remaining_amount' => $result['remaining_amount'],
            'progress' => $result['progress'],
            'transaction_id' => $result['transaction_id'],
            'campaign_status' => $result['campaign_status'],
        ],
    ], 201);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 409);
}
