<?php
// app/Services/AuthService.php

class AuthService
{
    public function __construct(private UserRepository $repo) {}

    /**
     * Xử lý đăng nhập
     *
     * Flow: validate input → tìm user → verify password
     * Controller sẽ xử lý session sau khi Service trả về success
     *
     * @return array ['success' => bool, 'user' => array|null, 'errors' => array]
     */
    public function login(string $email, string $password): array
    {
        $errors = [];

        // ── 1. Validate input ──────────────────────────────────────────────
        $email    = trim($email);
        $password = trim($password);

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'user' => null, 'errors' => $errors];
        }

        // ── 2. Tìm user theo email ─────────────────────────────────────────
        $user = $this->repo->findActiveByEmail($email);

        // ── 3. Verify password — dùng password_verify() ───────────────────
        // Không phân biệt "email sai" hay "password sai" → tránh user enumeration
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'user'    => null,
                'errors'  => ['general' => 'Email hoặc mật khẩu không đúng.'],
            ];
        }

        // ── 4. Login thành công ────────────────────────────────────────────
        return [
            'success' => true,
            'user'    => $user,
            'errors'  => [],
        ];
    }
}
