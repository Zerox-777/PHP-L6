<?php
// app/Core/helpers.php

// ─── Output Escaping ──────────────────────────────────────────────────────────

/**
 * Escape output để tránh XSS
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ─── Routing ──────────────────────────────────────────────────────────────────

/**
 * Redirect về path và dừng script (PRG Pattern)
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

// ─── View Rendering ───────────────────────────────────────────────────────────

/**
 * Render view con vào layout chính
 * Dùng ob_start/ob_get_clean để view con capture content → layout hiển thị
 */
function render(string $view, array $data = [], string $layout = 'layouts/main'): void
{
    extract($data);
    ob_start();
    require __DIR__ . '/../Views/' . $view . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/../Views/' . $layout . '.php';
}

/**
 * Include partial view (nav, flash, ...) — dùng trong layout
 */
function partial(string $name, array $data = []): void
{
    extract($data);
    require_once __DIR__ . '/../Views/partials/' . $name . '.php';
}

// ─── Flash Messages ───────────────────────────────────────────────────────────

/**
 * Lưu flash message — chỉ tồn tại 1 request (sau redirect)
 */
function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Lấy và xóa flash message (chỉ hiện 1 lần)
 */
function get_flash(string $key): ?string
{
    if (empty($_SESSION['flash'][$key])) {
        return null;
    }
    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

// ─── Form Helpers ─────────────────────────────────────────────────────────────

/**
 * Lấy old input — giữ lại dữ liệu cũ khi form lỗi
 */
function old(string $key, string $default = ''): string
{
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Lưu old input vào session (gọi trước khi redirect về form)
 */
function flash_old(array $input): void
{
    $_SESSION['old_input'] = $input;
}

/**
 * Xóa old input khỏi session
 */
function clear_old(): void
{
    unset($_SESSION['old_input']);
}

// ─── Auth Guard ───────────────────────────────────────────────────────────────

/**
 * Kiểm tra đã đăng nhập — nếu chưa thì redirect /login
 */
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        flash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('/login');
    }
}

/**
 * Kiểm tra có đăng nhập không (trả bool, không redirect)
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

// ─── Session Security ─────────────────────────────────────────────────────────

/**
 * Kiểm tra session timeout
 * Nếu không hoạt động quá $timeout giây → logout và redirect /login
 */
function check_session_timeout(): void
{
    if (empty($_SESSION['user_id'])) {
        return; // chưa login, không cần check
    }

    $appConfig = require __DIR__ . '/../../config/app.php';
    $timeout   = $appConfig['session_timeout'] ?? 900; // mặc định 15 phút

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        logout_clean();
        flash('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        redirect('/login');
    }

    // Cập nhật thời điểm hoạt động gần nhất
    $_SESSION['last_activity'] = time();
}

/**
 * Kiểm tra context session — phát hiện session hijacking qua User-Agent
 */
function check_session_context(): void
{
    if (empty($_SESSION['user_id'])) {
        return;
    }

    if (isset($_SESSION['user_agent'])) {
        $currentUA = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($_SESSION['user_agent'] !== $currentUA) {
            // User-Agent thay đổi → có thể bị chiếm session
            logout_clean();
            flash('error', 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại.');
            redirect('/login');
        }
    }
}

/**
 * Logout sạch — xóa session data, destroy session, xóa cookie session
 */
function logout_clean(): void
{
    // 1. Xóa tất cả session data
    $_SESSION = [];
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}
// ─── Logging ──────────────────────────────────────────────────────────────────

/**
 * Log lỗi DB vào file — KHÔNG hiện ra user
 */
function log_error(string $message): void
{
    $logFile = __DIR__ . '/../../storage/logs/app.log';
    $line    = '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $message . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// ─── Formatting ───────────────────────────────────────────────────────────────

/**
 * Format tiền VND
 */
function format_money(float $amount): string
{
    return number_format($amount, 0, ',', '.') . ' ₫';
}

/**
 * Format ngày dd/mm/yyyy
 */
function format_date(?string $date): string
{
    if (!$date) return '—';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}

/**
 * Kiểm tra phiếu thuê có quá hạn không
 */
function is_overdue(?string $dueDate, string $status): bool
{
    if ($status !== 'active') return false;
    return $dueDate && date('Y-m-d') > $dueDate;
}

// ─── Query String ─────────────────────────────────────────────────────────────

/**
 * Build query string giữ param hiện tại, ghi đè các param truyền vào
 * (dùng cho pagination, sort links)
 */
function query_string(array $params = []): string
{
    $current = $_GET;
    foreach ($params as $key => $value) {
        $current[$key] = $value;
    }
    return http_build_query($current);
}