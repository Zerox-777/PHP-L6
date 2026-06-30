<?php
// app/Controllers/DashboardController.php

class DashboardController
{
    public function index(): void
    {
        // Checklist T04: chưa login → redirect /login
        require_login();

        // Lấy stats nếu có DB
        $custStats   = ['total' => '—', 'active' => '—', 'inactive' => '—', 'blacklist' => '—'];
        $rentalStats = ['total' => '—', 'active' => '—', 'returned' => '—', 'overdue' => '—', 'cancelled' => '—'];

        try {
            $config      = require __DIR__ . '/../../config/database.php';
            $pdo         = (new Database($config))->getConnection();
            $custRepo    = new CustomerRepository($pdo);
            $rentalRepo  = new RentalRepository($pdo);
            $custStats   = $custRepo->getStats();
            $rentalStats = $rentalRepo->getStats();
        } catch (Exception $e) {
            log_error('DashboardController: ' . $e->getMessage());
        }

        render('dashboard/index', [
            'title'       => 'Dashboard — RentalCRM',
            'custStats'   => $custStats,
            'rentalStats' => $rentalStats,
        ]);
    }
}
