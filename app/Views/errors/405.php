<?php
// app/Views/errors/405.php

ob_start(); ?>

<div class="error-page">
    <div class="error-icon">🚫</div>
    <h1 class="error-code">405</h1>
    <h2>Method Not Allowed</h2>
    <p class="text-muted">
        URL này <strong>tồn tại</strong> nhưng HTTP method bạn dùng <strong>không được phép</strong>.
    </p>
    <p class="text-muted">
        Ví dụ: <code>GET /customers/delete</code> → 405 vì delete chỉ chấp nhận POST.<br>
        Khác với 404 (URL hoàn toàn không tồn tại).
    </p>
    <a class="btn primary" href="/">← Về trang chủ</a>
</div>

<?php
$content = ob_get_clean();
$title   = $title ?? '405 Method Not Allowed';
require __DIR__ . '/../layouts/main.php';