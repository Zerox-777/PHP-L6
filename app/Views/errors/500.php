<?php
// app/Views/errors/500.php

ob_start(); ?>

<div class="error-page">
    <div class="error-icon">⚙️</div>
    <h1 class="error-code">500</h1>
    <h2>Lỗi hệ thống</h2>
    <p class="text-muted">Đã xảy ra lỗi kỹ thuật. Vui lòng thử lại sau.</p>
    <p class="text-muted">
        Thông tin lỗi đã được ghi vào log để kỹ thuật viên xử lý.<br>
        <strong>Hệ thống không hiển thị SQLSTATE hay thông tin nội bộ cho người dùng.</strong>
    </p>
    <a class="btn primary" href="/">← Về trang chủ</a>
</div>

<?php
$content = ob_get_clean();
$title   = $title ?? '500 Server Error';
require __DIR__ . '/../layouts/main.php';