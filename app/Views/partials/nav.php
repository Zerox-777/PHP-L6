<?php
// app/Views/partials/nav.php

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

function nav_is_active(string $prefix, string $uri): string
{
    return str_starts_with($uri, $prefix) ? 'class="active"' : '';
}
?>
<nav class="navbar">
    <a href="<?= is_logged_in() ? '/dashboard' : '/' ?>" class="brand">
        🔧 <span>Rental</span>CRM
    </a>

    <?php if (is_logged_in()): ?>
        <a href="/dashboard" <?= $uri === '/dashboard' ? 'class="active"' : '' ?>>
            📊 Dashboard
        </a>
        <a href="/customers" <?= nav_is_active('/customers', $uri) ?>>
            👥 Khách thuê
        </a>
        <a href="/rentals" <?= nav_is_active('/rentals', $uri) ?>>
            📋 Phiếu thuê
        </a>
        <a href="/health" <?= $uri === '/health' ? 'class="active"' : '' ?>>
            ❤️ Health
        </a>
        <div class="nav-user">
            <span class="nav-username">
                👤 <?= e($_SESSION['user_name'] ?? '') ?>
                <span class="nav-role">(<?= e($_SESSION['role'] ?? '') ?>)</span>
            </span>
            <form method="post" action="/logout" class="inline-form">
                <button type="submit" class="btn btn-sm danger">Đăng xuất</button>
            </form>
        </div>
    <?php else: ?>
        <a href="/public-rental/create" <?= nav_is_active('/public-rental', $uri) ?>>
            📋 Đăng ký thuê
        </a>
        <a href="/login" <?= nav_is_active('/login', $uri) ?>>
            🔑 Đăng nhập
        </a>
        <a href="/health" <?= $uri === '/health' ? 'class="active"' : '' ?>>
            ❤️ Health
        </a>
    <?php endif; ?>
</nav>
