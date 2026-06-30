<?php
// app/Repositories/UserRepository.php

class UserRepository
{
    public function __construct(private PDO $db) {}

    /**
     * Tìm user active theo email — dùng cho login
     * Chỉ trả về user có status = 'active'
     */
    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, password_hash, role, status
             FROM users
             WHERE email = :email
               AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Tìm user theo id — dùng để load lại thông tin session
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, status
             FROM users
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
