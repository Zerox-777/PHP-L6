<?php
// app/Services/CustomerService.php

class CustomerService
{
    private const ALLOWED_STATUSES = ['active', 'inactive', 'blacklist'];

    public function __construct(private CustomerRepository $repo) {}

    // ─── List + Pagination ────────────────────────────────────────────────────

    public function getList(array $query): array
    {
        $keyword   = trim($query['q']         ?? '');
        $status    = trim($query['status']    ?? '');
        $sort      = trim($query['sort']      ?? 'created_at');
        $direction = trim($query['direction'] ?? 'desc');
        $page      = max(1, (int) ($query['page'] ?? 1));
        $perPage   = 10;

        // Whitelist status filter
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            $status = '';
        }

        $total      = $this->repo->countAll($keyword, $status);
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Chuẩn hóa page — không để page > totalPages hoặc < 1
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $customers = $this->repo->getPaginated(
            $keyword, $status, $perPage, $offset, $sort, $direction
        );

        return compact('customers', 'keyword', 'status', 'sort', 'direction',
                       'page', 'perPage', 'total', 'totalPages');
    }

    // ─── Validate ─────────────────────────────────────────────────────────────

    public function validate(array $input): array
    {
        $values = [
            'customer_code' => strtoupper(trim($input['customer_code'] ?? '')),
            'name'          => trim($input['name']   ?? ''),
            'email'         => trim($input['email']  ?? ''),
            'phone'         => trim($input['phone']  ?? ''),
            'status'        => trim($input['status'] ?? 'active'),
            'note'          => trim($input['note']   ?? ''),
        ];
        $errors = [];

        // customer_code: required, regex
        if ($values['customer_code'] === '') {
            $errors['customer_code'] = 'Vui lòng nhập mã khách hàng.';
        } elseif (!preg_match('/^[A-Z0-9\-]{3,20}$/', $values['customer_code'])) {
            $errors['customer_code'] = 'Mã chỉ gồm chữ hoa, số, dấu gạch ngang (3–20 ký tự). VD: KH-001';
        }

        // name: required, 2–100 ký tự
        if ($values['name'] === '') {
            $errors['name'] = 'Vui lòng nhập họ tên khách hàng.';
        } elseif (mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 100) {
            $errors['name'] = 'Họ tên phải từ 2 đến 100 ký tự.';
        }

        // email: required + format
        if ($values['email'] === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        }

        // phone: optional, nếu nhập thì phải đúng định dạng
        if ($values['phone'] !== '' && !preg_match('/^[0-9+\-\s]{9,15}$/', $values['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (9–15 ký tự số, +, -)';
        }

        // status: whitelist
        if (!in_array($values['status'], self::ALLOWED_STATUSES, true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return ['values' => $values, 'errors' => $errors];
    }

    // ─── CRUD operations ─────────────────────────────────────────────────────

    public function createCustomer(array $input): array
    {
        $result = $this->validate($input);
        if (!empty($result['errors'])) {
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }

        try {
            $id = $this->repo->create($result['values']);
            return ['success' => true, 'id' => $id, 'errors' => [], 'values' => $result['values']];
        } catch (DuplicateRecordException $e) {
            $result['errors']['customer_code'] = 'Mã khách hàng này đã tồn tại. Vui lòng dùng mã khác.';
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
    }

    public function updateCustomer(int $id, array $input): array
    {
        $result = $this->validate($input);
        if (!empty($result['errors'])) {
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }

        try {
            $this->repo->update($id, $result['values']);
            return ['success' => true, 'errors' => [], 'values' => $result['values']];
        } catch (DuplicateRecordException $e) {
            $result['errors']['customer_code'] = 'Mã khách hàng này đã tồn tại. Vui lòng dùng mã khác.';
            return ['success' => false, 'errors' => $result['errors'], 'values' => $result['values']];
        }
    }

    public function deleteCustomer(int $id): void
    {
        $this->repo->delete($id);
    }
}
