<?php
// app/Controllers/RentalController.php

class RentalController
{
    private function service(): RentalService
    {
        $config      = require __DIR__ . '/../../config/database.php';
        $pdo         = (new Database($config))->getConnection();
        $rentalRepo  = new RentalRepository($pdo);
        $customerRepo = new CustomerRepository($pdo);
        return new RentalService($rentalRepo, $customerRepo);
    }

    // ─── GET /rentals ─────────────────────────────────────────────────────────

    public function index(): void
    {
        require_login();

        try {
            $data = $this->service()->getList($_GET);
        } catch (Exception $e) {
            log_error('RentalController::index – ' . $e->getMessage());
            http_response_code(500);
            render('errors/500', ['title' => 'Lỗi hệ thống']);
            return;
        }

        render('rentals/index', ['title' => 'Quản lý phiếu thuê'] + $data);
    }

    // ─── GET /rentals/create ──────────────────────────────────────────────────

    public function create(): void
    {
        require_login();

        $nextCode  = 'RNT-' . date('Y') . '-0001';
        $customers = [];

        try {
            $svc       = $this->service();
            $nextCode  = $svc->getNextCode();
            $customers = $svc->getActiveCustomers();
        } catch (Exception $e) {
            log_error('RentalController::create – ' . $e->getMessage());
        }

        render('rentals/create', [
            'title'     => 'Tạo phiếu thuê',
            'errors'    => [],
            'old'       => [
                'rental_code'    => $nextCode,
                'customer_id'    => '',
                'equipment_name' => '',
                'equipment_code' => '',
                'rent_date'      => date('Y-m-d'),
                'due_date'       => date('Y-m-d', strtotime('+3 days')),
                'return_date'    => '',
                'status'         => 'active',
                'daily_rate'     => '',
                'total_amount'   => '',
                'note'           => '',
            ],
            'customers' => $customers,
        ]);
    }

    // ─── POST /rentals/store ──────────────────────────────────────────────────

    public function store(): void
    {
        require_login();

        $svc    = $this->service();
        $result = $svc->createRental($_POST);

        if (!$result['success']) {
            render('rentals/create', [
                'title'     => 'Tạo phiếu thuê',
                'errors'    => $result['errors'],
                'old'       => $result['values'],
                'customers' => $svc->getActiveCustomers(),
            ]);
            return;
        }

        flash('success', '✅ Phiếu thuê "' . $result['values']['rental_code'] . '" đã được tạo thành công!');
        redirect('/rentals');
    }

    // ─── GET /rentals/edit ────────────────────────────────────────────────────

    public function edit(): void
    {
        require_login();

        $id   = (int) ($_GET['id'] ?? 0);
        $item = null;
        $customers = [];

        try {
            $config      = require __DIR__ . '/../../config/database.php';
            $pdo         = (new Database($config))->getConnection();
            $item        = (new RentalRepository($pdo))->findById($id);
            $customers   = (new CustomerRepository($pdo))->findAllActive();
        } catch (Exception $e) {
            log_error('RentalController::edit – ' . $e->getMessage());
        }

        if (!$item) {
            http_response_code(404);
            render('errors/404', ['title' => '404 Not Found']);
            return;
        }

        render('rentals/edit', [
            'title'     => 'Sửa phiếu thuê',
            'errors'    => [],
            'old'       => $item,
            'item'      => $item,
            'customers' => $customers,
        ]);
    }

    // ─── POST /rentals/update ─────────────────────────────────────────────────

    public function update(): void
    {
        require_login();

        $id     = (int) ($_POST['id'] ?? 0);
        $svc    = $this->service();
        $result = $svc->updateRental($id, $_POST);

        if (!$result['success']) {
            $item = $result['values'];
            $item['id'] = $id;
            try {
                $config = require __DIR__ . '/../../config/database.php';
                $pdo    = (new Database($config))->getConnection();
                $item   = (new RentalRepository($pdo))->findById($id) ?? $item;
            } catch (Exception $e) {
                log_error('RentalController::update (reload) – ' . $e->getMessage());
            }

            render('rentals/edit', [
                'title'     => 'Sửa phiếu thuê',
                'errors'    => $result['errors'],
                'old'       => $result['values'],
                'item'      => $item,
                'customers' => $svc->getActiveCustomers(),
            ]);
            return;
        }

        flash('success', '✅ Phiếu thuê đã được cập nhật thành công!');
        redirect('/rentals');
    }

    // ─── POST /rentals/delete ─────────────────────────────────────────────────

    public function delete(): void
    {
        require_login();

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->service()->deleteRental($id);
            flash('success', '🗑️ Phiếu thuê đã được xóa khỏi hệ thống.');
        } catch (Exception $e) {
            log_error('RentalController::delete – ' . $e->getMessage());
            flash('error', 'Không thể xóa phiếu thuê này.');
        }

        redirect('/rentals');
    }
}
