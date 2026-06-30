<?php
// app/Repositories/CustomerRepository.php

class CustomerRepository
{
    public function __construct(private PDO $db) {}

    // ─── Count ───────────────────────────────────────────────────────────────

    public function countAll(string $keyword = '', string $status = ''): int
    {
        $sql    = "SELECT COUNT(*) AS total FROM customers WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (name LIKE :keyword
                       OR customer_code LIKE :keyword
                       OR email LIKE :keyword
                       OR phone LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    // ─── List with pagination ─────────────────────────────────────────────────

    public function getPaginated(
        string $keyword,
        string $status,
        int    $limit,
        int    $offset,
        string $sort,
        string $direction
    ): array {
        // Whitelist sort — không lấy thẳng từ $_GET vào SQL (TC20)
        $allowedSorts = ['id', 'customer_code', 'name', 'email', 'status', 'created_at'];
        $allowedDirs  = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }
        if (!in_array(strtolower($direction), $allowedDirs, true)) {
            $direction = 'desc';
        }

        $sql    = "SELECT id, customer_code, name, email, phone, status, created_at
                   FROM customers WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (name LIKE :keyword
                       OR customer_code LIKE :keyword
                       OR email LIKE :keyword
                       OR phone LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY {$sort} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // ─── Find ─────────────────────────────────────────────────────────────────

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM customers WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findAllActive(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, customer_code, name FROM customers WHERE status = 'active' ORDER BY name"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $sql = "INSERT INTO customers (customer_code, name, email, phone, status, note)
                VALUES (:customer_code, :name, :email, :phone, :status, :note)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'customer_code' => $data['customer_code'],
                'name'          => $data['name'],
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?: null,
                'status'        => $data['status'],
                'note'          => $data['note']  ?: null,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Mã khách hàng này đã tồn tại trong hệ thống.');
            }
            throw $e;
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE customers
                SET customer_code = :customer_code,
                    name          = :name,
                    email         = :email,
                    phone         = :phone,
                    status        = :status,
                    note          = :note,
                    updated_at    = NOW()
                WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id'            => $id,
                'customer_code' => $data['customer_code'],
                'name'          => $data['name'],
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?: null,
                'status'        => $data['status'],
                'note'          => $data['note']  ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Mã khách hàng này đã tồn tại trong hệ thống.');
            }
            throw $e;
        }
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    public function getStats(): array
    {
        $stmt  = $this->db->query(
            "SELECT status, COUNT(*) AS total FROM customers GROUP BY status"
        );
        $rows  = $stmt->fetchAll();
        $stats = ['active' => 0, 'inactive' => 0, 'blacklist' => 0, 'total' => 0];
        foreach ($rows as $row) {
            $stats[$row['status']]  = (int) $row['total'];
            $stats['total']        += (int) $row['total'];
        }
        return $stats;
    }
}
