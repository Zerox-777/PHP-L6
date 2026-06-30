<?php
// app/Services/RentalService.php

class RentalService
{
    private const ALLOWED_STATUSES = ['active', 'returned', 'overdue', 'cancelled'];

    public function __construct(
        private RentalRepository   $rentalRepo,
        private CustomerRepository $customerRepo
    ) {}

    // ─── List + Pagination ────────────────────────────────────────────────────

    public function getList(array $query): array
    {
        $keyword   = trim($query['q']         ?? '');
        $status    = trim($query['status']    ?? '');
        $sort      = trim($query['sort']      ?? 'r.created_at');
        $direction = trim($query['direction'] ?? 'desc');
        $page      = max(1, (int) ($query['page'] ?? 1));
        $perPage   = 10;

        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            $status = '';
        }

        $total      = $this->rentalRepo->countAll($keyword, $status);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $rentals = $this->rentalRepo->getPaginated(
            $keyword, $status, $perPage, $offset, $sort, $direction
        );

        return compact('rentals', 'keyword', 'status', 'sort', 'direction',
                       'page', 'perPage', 'total', 'totalPages');
    }

    // ─── Validate ─────────────────────────────────────────────────────────────

    public function validate(array $input, bool $isUpdate = false): array
    {
        $values = [
            'rental_code'    => strtoupper(trim($input['rental_code']    ?? '')),
            'customer_id'    => (int) ($input['customer_id']             ?? 0),
            'equipment_name' => trim($input['equipment_name']            ?? ''),
            'equipment_code' => strtoupper(trim($input['equipment_code'] ?? '')),
            'rent_date'      => trim($input['rent_date']                 ?? ''),
            'due_date'       => trim($input['due_date']                  ?? ''),
            'return_date'    => trim($input['return_date']               ?? ''),
            'status'         => trim($input['status']                    ?? 'active'),
            'daily_rate'     => (float) str_replace(',', '.', $input['daily_rate']   ?? '0'),
            'total_amount'   => (float) str_replace(',', '.', $input['total_amount'] ?? '0'),
            'note'           => trim($input['note'] ?? ''),
        ];
        $errors = [];

        // rental_code
        if ($values['rental_code'] === '') {
            $errors['rental_code'] = 'Vui lòng nhập mã phiếu thuê.';
        } elseif (!preg_match('/^[A-Z0-9\-]{5,30}$/', $values['rental_code'])) {
            $errors['rental_code'] = 'Mã phiếu chỉ gồm chữ hoa, số, gạch ngang (5–30 ký tự). VD: RNT-2026-0001';
        }

        // customer_id
        if ($values['customer_id'] <= 0) {
            $errors['customer_id'] = 'Vui lòng chọn khách hàng.';
        }

        // equipment_name
        if ($values['equipment_name'] === '') {
            $errors['equipment_name'] = 'Vui lòng nhập tên thiết bị.';
        } elseif (mb_strlen($values['equipment_name']) > 150) {
            $errors['equipment_name'] = 'Tên thiết bị không vượt quá 150 ký tự.';
        }

        // rent_date
        if ($values['rent_date'] === '') {
            $errors['rent_date'] = 'Vui lòng chọn ngày bắt đầu thuê.';
        }

        // due_date: phải sau rent_date
        if ($values['due_date'] === '') {
            $errors['due_date'] = 'Vui lòng chọn ngày hẹn trả.';
        } elseif ($values['rent_date'] !== '' && $values['due_date'] <= $values['rent_date']) {
            $errors['due_date'] = 'Ngày hẹn trả phải sau ngày bắt đầu thuê.';
        }

        // status whitelist
        if (!in_array($values['status'], self::ALLOWED_STATUSES, true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        // daily_rate & total_amount không âm
        if ($values['daily_rate'] < 0) {
            $errors['daily_rate'] = 'Giá thuê/ngày không được âm.';
        }
        if ($values['total_amount'] < 0) {
            $errors['total_amount'] = 'Tổng tiền không được âm.';
        }

        // return_date: optional
        if ($values['return_date'] === '') {
            $values['return_date'] = null;
        }

        return ['values' => $values, 'errors' => $errors];
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function createRental(array $input): array
    {
        $result = $this->validate($input);
        if (!empty($result['errors'])) {
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
        try {
            $id = $this->rentalRepo->create($result['values']);
            return ['success' => true, 'id' => $id, 'errors' => [], 'values' => $result['values']];
        } catch (DuplicateRecordException $e) {
            $result['errors']['rental_code'] = 'Mã phiếu thuê này đã tồn tại. Vui lòng dùng mã khác.';
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
    }

    public function updateRental(int $id, array $input): array
    {
        $result = $this->validate($input, true);
        if (!empty($result['errors'])) {
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
        try {
            $this->rentalRepo->update($id, $result['values']);
            return ['success' => true, 'errors' => [], 'values' => $result['values']];
        } catch (DuplicateRecordException $e) {
            $result['errors']['rental_code'] = 'Mã phiếu thuê này đã tồn tại. Vui lòng dùng mã khác.';
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
    }

    public function deleteRental(int $id): void
    {
        $this->rentalRepo->delete($id);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getNextCode(): string
    {
        return $this->rentalRepo->generateNextCode();
    }

    public function getActiveCustomers(): array
    {
        return $this->customerRepo->findAllActive();
    }
}
