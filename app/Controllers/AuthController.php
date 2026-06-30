<?php
// app/Controllers/AuthController.php

class AuthController
{
    private function authService(): AuthService
    {
        $config = require __DIR__ . '/../../config/database.php';
        $pdo    = (new Database($config))->getConnection();
        return new AuthService(new UserRepository($pdo));
    }

    // ─── GET /login ───────────────────────────────────────────────────────────

    public function login(): void
    {
        // Nếu đã login thì về dashboard luôn
        if (is_logged_in()) {
            redirect('/dashboard');
        }

        render('auth/login', [
            'title'  => 'Đăng nhập — RentalCRM',
            'errors' => [],
            'old'    => [],
        ]);
    }

    // ─── POST /login ──────────────────────────────────────────────────────────

    public function handleLogin(): void
    {
        // Nếu đã login thì về dashboard luôn
        if (is_logged_in()) {
            redirect('/dashboard');
        }

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Gọi AuthService — Controller KHÔNG chứa business rule
        $result = $this->authService()->login($email, $password);

        // ── Login thất bại → render lại form với lỗi + giữ old email ──────
        if (!$result['success']) {
            render('auth/login', [
                'title'  => 'Đăng nhập — RentalCRM',
                'errors' => $result['errors'],
                'old'    => ['email' => $email],   // giữ email, KHÔNG giữ password
            ]);
            return;
        }

        // ── Login thành công ─────────────────────────────────────────────────

        // Checklist T11: PHẢI gọi trước khi set session data
        // → Đổi session ID mới, tránh Session Fixation Attack
        session_regenerate_id(true);

        $user = $result['user'];

        // Lưu thông tin vào session
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['login_at']      = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['user_agent']    = $_SERVER['HTTP_USER_AGENT'] ?? '';

        flash('success', 'Chào mừng ' . $user['name'] . '! Đăng nhập thành công.');
        redirect('/dashboard');
    }

    // ─── POST /logout ─────────────────────────────────────────────────────────

    public function logout(): void
    {
        // Checklist T12: logout sạch — xóa data, destroy session, xóa cookie
        logout_clean();
        flash('success', 'Bạn đã đăng xuất thành công.');
        redirect('/login');
    }
}
