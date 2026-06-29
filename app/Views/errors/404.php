<?php
// app/Views/errors/404.php

ob_start(); ?>

<div class="error-page">
    <div class="error-icon">🔍</div>
    <h1 class="error-code">404</h1>
    <h2>Không tìm thấy trang</h2>
    <p class="text-muted">URL bạn truy cập <strong>không tồn tại</strong> trong hệ thống.</p>
    <p class="text-muted">
        Đây là <strong>404 Not Found</strong> — khác với 405 (URL tồn tại nhưng sai HTTP method).
    </p>
    <a class="btn primary" href="/">← Về trang chủ</a>
</div>

<?php
$content = ob_get_clean();
$title   = $title ?? '404 Not Found';
require __DIR__ . '/../layouts/main.php';
