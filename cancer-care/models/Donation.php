<?php
/**
 * Donation model - handles all donation-related database operations.
 */

class Donation
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a donation and atomically update the campaign's raised amount.
     * Returns the donation record (with computed totals) on success.
     */
    public function create(int $campaignId, int $donorId, float $amount, bool $anonymous, ?string $message, string $paymentMethod): array
    {
        $this->db->beginTransaction();
        try {
            // Lock the campaign row for update to avoid race conditions
            $stmt = $this->db->prepare('SELECT target_amount, raised_amount, campaign_status FROM campaigns WHERE id = ? FOR UPDATE');
            $stmt->execute([$campaignId]);
            $campaign = $stmt->fetch();

            if (!$campaign) {
                throw new RuntimeException('Campaign not found.');
            }
            if ($campaign['campaign_status'] === 'fully_funded') {
                throw new RuntimeException('This campaign has already reached its target and cannot accept further donations.');
            }

            $transactionId = 'TXN' . strtoupper(bin2hex(random_bytes(6)));

            $insert = $this->db->prepare(
                'INSERT INTO donations (campaign_id, donor_id, amount, anonymous, message, payment_method, transaction_id, payment_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, "completed")'
            );
            $insert->execute([$campaignId, $donorId, $amount, $anonymous ? 1 : 0, $message, $paymentMethod, $transactionId]);
            $donationId = (int) $this->db->lastInsertId();

            $newRaised = (float) $campaign['raised_amount'] + $amount;
            $newStatus = $newRaised >= (float) $campaign['target_amount'] ? 'fully_funded' : 'active';

            $update = $this->db->prepare('UPDATE campaigns SET raised_amount = ?, campaign_status = ? WHERE id = ?');
            $update->execute([$newRaised, $newStatus, $campaignId]);

            $this->db->commit();

            return [
                'donation_id'      => $donationId,
                'transaction_id'   => $transactionId,
                'donated_amount'   => $amount,
                'total_raised'     => $newRaised,
                'remaining_amount' => calc_remaining($newRaised, (float) $campaign['target_amount']),
                'progress'         => calc_progress($newRaised, (float) $campaign['target_amount']),
                'campaign_status'  => $newStatus,
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function historyForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, c.patient_name, c.id AS campaign_id
             FROM donations d JOIN campaigns c ON c.id = d.campaign_id
             WHERE d.donor_id = ? ORDER BY d.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function historyForCampaign(int $campaignId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.name AS donor_name
             FROM donations d JOIN users u ON u.id = d.donor_id
             WHERE d.campaign_id = ? AND d.payment_status = "completed"
             ORDER BY d.created_at DESC'
        );
        $stmt->execute([$campaignId]);
        return $stmt->fetchAll();
    }

    public function totalDonatedByUser(int $userId): float
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(amount),0) FROM donations WHERE donor_id = ? AND payment_status = "completed"');
        $stmt->execute([$userId]);
        return (float) $stmt->fetchColumn();
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM donations WHERE donor_id = ? AND payment_status = "completed"');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function allForAdmin(int $limit = 200): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.name AS donor_name, c.patient_name
             FROM donations d
             JOIN users u ON u.id = d.donor_id
             JOIN campaigns c ON c.id = d.campaign_id
             ORDER BY d.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
