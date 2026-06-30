<?php
// app/Repositories/RentalRepository.php

class RentalRepository
{
    public function __construct(private PDO $db) {}

    // ─── Count ───────────────────────────────────────────────────────────────

    public function countAll(string $keyword = '', string $status = ''): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM rentals r
                JOIN customers c ON r.customer_id = c.id
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (r.rental_code    LIKE :keyword
                       OR c.name            LIKE :keyword
                       OR r.equipment_name  LIKE :keyword
                       OR r.equipment_code  LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $sql .= " AND r.status = :status";
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
        $allowedSorts = [
            'r.id', 'r.rental_code', 'r.rent_date', 'r.due_date',
            'r.status', 'r.total_amount', 'r.created_at', 'c.name',
        ];
        $allowedDirs = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'r.created_at';
        }
        if (!in_array(strtolower($direction), $allowedDirs, true)) {
            $direction = 'desc';
        }

        $sql = "SELECT r.id, r.rental_code, r.equipment_name, r.equipment_code,
                       r.rent_date, r.due_date, r.return_date, r.status,
                       r.daily_rate, r.total_amount, r.created_at,
                       c.id AS customer_id, c.name AS customer_name,
                       c.customer_code, c.phone AS customer_phone
                FROM rentals r
                JOIN customers c ON r.customer_id = c.id
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (r.rental_code    LIKE :keyword
                       OR c.name            LIKE :keyword
                       OR r.equipment_name  LIKE :keyword
                       OR r.equipment_code  LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $sql .= " AND r.status = :status";
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
            "SELECT r.*, c.name AS customer_name, c.customer_code, c.email AS customer_email
             FROM rentals r
             JOIN customers c ON r.customer_id = c.id
             WHERE r.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $sql = "INSERT INTO rentals
                    (rental_code, customer_id, equipment_name, equipment_code,
                     rent_date, due_date, return_date, status,
                     daily_rate, total_amount, note)
                VALUES
                    (:rental_code, :customer_id, :equipment_name, :equipment_code,
                     :rent_date, :due_date, :return_date, :status,
                     :daily_rate, :total_amount, :note)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'rental_code'    => $data['rental_code'],
                'customer_id'    => $data['customer_id'],
                'equipment_name' => $data['equipment_name'],
                'equipment_code' => $data['equipment_code'] ?: null,
                'rent_date'      => $data['rent_date'],
                'due_date'       => $data['due_date'],
                'return_date'    => $data['return_date']    ?: null,
                'status'         => $data['status'],
                'daily_rate'     => $data['daily_rate'],
                'total_amount'   => $data['total_amount'],
                'note'           => $data['note']           ?: null,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Mã phiếu thuê này đã tồn tại trong hệ thống.');
            }
            throw $e;
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE rentals
                SET rental_code    = :rental_code,
                    customer_id    = :customer_id,
                    equipment_name = :equipment_name,
                    equipment_code = :equipment_code,
                    rent_date      = :rent_date,
                    due_date       = :due_date,
                    return_date    = :return_date,
                    status         = :status,
                    daily_rate     = :daily_rate,
                    total_amount   = :total_amount,
                    note           = :note,
                    updated_at     = NOW()
                WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id'             => $id,
                'rental_code'    => $data['rental_code'],
                'customer_id'    => $data['customer_id'],
                'equipment_name' => $data['equipment_name'],
                'equipment_code' => $data['equipment_code'] ?: null,
                'rent_date'      => $data['rent_date'],
                'due_date'       => $data['due_date'],
                'return_date'    => $data['return_date']    ?: null,
                'status'         => $data['status'],
                'daily_rate'     => $data['daily_rate'],
                'total_amount'   => $data['total_amount'],
                'note'           => $data['note']           ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Mã phiếu thuê này đã tồn tại trong hệ thống.');
            }
            throw $e;
        }
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM rentals WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    public function getStats(): array
    {
        $stmt  = $this->db->query(
            "SELECT status, COUNT(*) AS total FROM rentals GROUP BY status"
        );
        $rows  = $stmt->fetchAll();
        $stats = ['active' => 0, 'returned' => 0, 'overdue' => 0, 'cancelled' => 0, 'total' => 0];
        foreach ($rows as $row) {
            $stats[$row['status']]  = (int) $row['total'];
            $stats['total']        += (int) $row['total'];
        }
        return $stats;
    }

    // ─── Generate next rental code ────────────────────────────────────────────

    public function generateNextCode(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare(
            "SELECT rental_code FROM rentals
             WHERE rental_code LIKE :prefix
             ORDER BY id DESC
             LIMIT 1"
        );
        $stmt->execute(['prefix' => "RNT-{$year}-%"]);
        $last = $stmt->fetchColumn();

        if ($last) {
            // Lấy phần số ở cuối: RNT-2026-0021 → 21
            $num = (int) substr($last, strrpos($last, '-') + 1) + 1;
        } else {
            $num = 1;
        }

        return 'RNT-' . $year . '-' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }
}
