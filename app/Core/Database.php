<?php
// app/Core/Database.php

class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['database'],
            $config['charset']
        );

        $options = [
            // Ném exception khi DB gặp lỗi → dùng try/catch bắt được
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Kết quả trả về dạng mảng theo tên cột (không dùng index số)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Dùng prepared statement thật của MySQL, không giả lập ở PHP
            // → tránh SQL Injection tốt hơn, type binding chính xác hơn
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            $options
        );
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
