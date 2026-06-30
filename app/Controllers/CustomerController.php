<?php
// app/Controllers/CustomerController.php

class CustomerController
{
    private function service(): CustomerService
    {
        $config = require __DIR__ . '/../../config/database.php';
        $pdo    = (new Database($config))->getConnection();
        return new CustomerService(new CustomerRepository($pdo));
    }

    // ─── GET /customers ───────────────────────────────────────────────────────

    public function index(): void
    {
        require_login();

        try {
            $data = $this->service()->getList($_GET);
        } catch (Exception $e) {
            log_error('CustomerController::index – ' . $e->getMessage());
            http_response_code(500);
            render('errors/500', ['title' => 'Lỗi hệ thống']);
            return;
        }

        render('customers/index', ['title' => 'Quản lý khách thuê'] + $data);
    }

    // ─── GET /customers/create ────────────────────────────────────────────────

    public function create(): void
    {
        require_login();

        render('customers/create', [
            'title'  => 'Thêm khách thuê',
            'errors' => [],
            'old'    => [
                'customer_code' => '',
                'name'          => '',
                'email'         => '',
                'phone'         => '',
                'status'        => 'active',
                'note'          => '',
            ],
        ]);
    }

    // ─── POST /customers/store ────────────────────────────────────────────────

    public function store(): void
    {
        require_login();

        $result = $this->service()->createCustomer($_POST);

        if (!$result['success']) {
            render('customers/create', [
                'title'  => 'Thêm khách thuê',
                'errors' => $result['errors'],
                'old'    => $result['values'],
            ]);
            return;
        }

        flash('success', '✅ Khách hàng "' . $result['values']['name'] . '" đã được thêm thành công!');
        redirect('/customers');
    }

    // ─── GET /customers/edit ──────────────────────────────────────────────────

    public function edit(): void
    {
        require_login();

        $id   = (int) ($_GET['id'] ?? 0);
        $item = null;

        try {
            $config = require __DIR__ . '/../../config/database.php';
            $pdo    = (new Database($config))->getConnection();
            $item   = (new CustomerRepository($pdo))->findById($id);
        } catch (Exception $e) {
            log_error('CustomerController::edit – ' . $e->getMessage());
        }

        if (!$item) {
            http_response_code(404);
            render('errors/404', ['title' => '404 Not Found']);
            return;
        }

        render('customers/edit', [
            'title'  => 'Sửa khách thuê',
            'errors' => [],
            'old'    => $item,
            'item'   => $item,
        ]);
    }

    // ─── POST /customers/update ───────────────────────────────────────────────

    public function update(): void
    {
        require_login();

        $id     = (int) ($_POST['id'] ?? 0);
        $result = $this->service()->updateCustomer($id, $_POST);

        if (!$result['success']) {
            // Cần item để render panel thông tin bên phải
            $item = $result['values'];
            $item['id'] = $id;
            try {
                $config = require __DIR__ . '/../../config/database.php';
                $pdo    = (new Database($config))->getConnection();
                $item   = (new CustomerRepository($pdo))->findById($id) ?? $item;
            } catch (Exception $e) {
                log_error('CustomerController::update (reload item) – ' . $e->getMessage());
            }

            render('customers/edit', [
                'title'  => 'Sửa khách thuê',
                'errors' => $result['errors'],
                'old'    => $result['values'],
                'item'   => $item,
            ]);
            return;
        }

        flash('success', '✅ Thông tin khách hàng đã được cập nhật thành công!');
        redirect('/customers');
    }

    // ─── POST /customers/delete ───────────────────────────────────────────────

    public function delete(): void
    {
        require_login();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->service()->deleteCustomer($id);
            flash('success', '🗑️ Khách hàng đã được xóa khỏi hệ thống.');
        } catch (Exception $e) {
            log_error('CustomerController::delete – ' . $e->getMessage());
            flash('error', 'Không thể xóa khách hàng này (có thể đang có phiếu thuê liên kết).');
        }

        redirect('/customers');
    }
}
