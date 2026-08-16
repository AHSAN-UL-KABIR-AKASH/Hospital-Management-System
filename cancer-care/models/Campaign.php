<?php
/**
 * Campaign model - handles all campaign-related database operations.
 */

class Campaign
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO campaigns
            (user_id, patient_name, patient_photo, age, cancer_type, cancer_stage, hospital, treatment_details, target_amount, story, contact_information)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['user_id'],
            $data['patient_name'],
            $data['patient_photo'],
            $data['age'],
            $data['cancer_type'],
            $data['cancer_stage'],
            $data['hospital'],
            $data['treatment_details'],
            $data['target_amount'],
            $data['story'],
            $data['contact_information'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.name AS creator_name, u.email AS creator_email,
                    (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status = "completed") AS donor_count
             FROM campaigns c JOIN users u ON u.id = c.user_id WHERE c.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function publicList(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
             FROM campaigns c
             WHERE c.verification_status = 'verified'
             ORDER BY c.created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function featured(int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
             FROM campaigns c
             WHERE c.verification_status = 'verified' AND c.campaign_status = 'active'
             ORDER BY (c.raised_amount / c.target_amount) DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function recentlyAdded(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
             FROM campaigns c WHERE c.verification_status = 'verified'
             ORDER BY c.created_at DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fullyFunded(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
             FROM campaigns c
             WHERE c.verification_status = 'verified' AND c.campaign_status = 'fully_funded'
             ORDER BY c.updated_at DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
             FROM campaigns c WHERE c.user_id = ? ORDER BY c.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function search(string $term, string $filter = ''): array
    {
        $sql = "SELECT c.*, (SELECT COUNT(DISTINCT donor_id) FROM donations WHERE campaign_id = c.id AND payment_status='completed') AS donor_count
                FROM campaigns c
                WHERE c.verification_status = 'verified'";
        $params = [];

        if ($term !== '') {
            $sql .= ' AND (c.patient_name LIKE ? OR c.cancer_type LIKE ? OR c.hospital LIKE ?)';
            $like = '%' . $term . '%';
            $params = [$like, $like, $like];
        }

        switch ($filter) {
            case 'most_funded':
                $sql .= ' ORDER BY c.raised_amount DESC';
                break;
            case 'almost_funded':
                $sql .= ' AND c.campaign_status = "active" ORDER BY (c.raised_amount / c.target_amount) DESC';
                break;
            case 'fully_funded':
                $sql .= ' AND c.campaign_status = "fully_funded" ORDER BY c.updated_at DESC';
                break;
            case 'recent':
            default:
                $sql .= ' ORDER BY c.created_at DESC';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateRaisedAmount(int $campaignId, float $newRaised, float $targetAmount): void
    {
        $status = $newRaised >= $targetAmount ? 'fully_funded' : 'active';
        $stmt = $this->db->prepare('UPDATE campaigns SET raised_amount = ?, campaign_status = ? WHERE id = ?');
        $stmt->execute([$newRaised, $status, $campaignId]);
    }

    public function setVerificationStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE campaigns SET verification_status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function suspend(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE campaigns SET verification_status = 'suspended' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM campaigns WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function allForAdmin(string $statusFilter = ''): array
    {
        $sql = 'SELECT c.*, u.name AS creator_name FROM campaigns c JOIN users u ON u.id = c.user_id';
        $params = [];
        if ($statusFilter !== '') {
            $sql .= ' WHERE c.verification_status = ?';
            $params[] = $statusFilter;
        }
        $sql .= ' ORDER BY c.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addDocument(int $campaignId, string $name, string $path, string $type): void
    {
        $stmt = $this->db->prepare('INSERT INTO medical_documents (campaign_id, document_name, document_path, document_type) VALUES (?, ?, ?, ?)');
        $stmt->execute([$campaignId, $name, $path, $type]);
    }

    public function documentsFor(int $campaignId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM medical_documents WHERE campaign_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$campaignId]);
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $db = $this->db;
        return [
            'total_patients'   => (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified'")->fetchColumn(),
            'active_campaigns' => (int) $db->query("SELECT COUNT(*) FROM campaigns WHERE verification_status='verified' AND campaign_status='active'")->fetchColumn(),
            'total_donations'  => (int) $db->query("SELECT COUNT(*) FROM donations WHERE payment_status='completed'")->fetchColumn(),
            'total_raised'     => (float) $db->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status='completed'")->fetchColumn(),
        ];
    }
}
