<?php
// app/Controllers/HealthController.php

class HealthController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $pdo    = (new Database($config))->getConnection();
            $pdo->query('SELECT 1');
            echo json_encode([
                'status'   => 'ok',
                'database' => 'connected',
                'app'      => 'Equipment Rental CRM',
                'time'     => date('Y-m-d H:i:s'),
                'php'      => PHP_VERSION,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            log_error('Health check failed: ' . $e->getMessage());
            echo json_encode([
                'status'   => 'error',
                'database' => 'failed',
                'time'     => date('Y-m-d H:i:s'),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}
