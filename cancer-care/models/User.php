<?php
/**
 * User model - handles all user-related database operations.
 */

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function create(string $name, string $email, string $phone, string $passwordHash, string $role = 'donor'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $phone, $passwordHash, $role]);
        return (int) $this->db->lastInsertId();
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function updateProfile(int $id, string $name, string $phone): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
        return $stmt->execute([$name, $phone, $id]);
    }

    public function updatePassword(int $id, string $newHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$newHash, $id]);
    }

    public function promoteToFundraiser(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role = 'fundraiser' WHERE id = ? AND role = 'donor'");
        return $stmt->execute([$id]);
    }

    public function setStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, phone, role, status, created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC');
        $like = '%' . $term . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function totalCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
