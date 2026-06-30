<?php
// app/Views/home.php
// Trang giới thiệu công khai — hiển thị khi truy cập "/" và chưa đăng nhập
ob_start(); ?>

<div class="home-hero">
    <div class="home-hero-icon">🔧</div>
    <h1 class="home-title">Equipment Rental CRM</h1>
    <p class="home-subtitle">
        Hệ thống quản lý khách thuê &amp; phiếu thuê thiết bị quay phim, chụp ảnh chuyên nghiệp.
    </p>
    <div class="home-actions">
        <a href="/public-rental/create" class="btn primary btn-lg">📋 Đăng ký thuê thiết bị</a>
        <a href="/login" class="btn secondary btn-lg">🔑 Đăng nhập quản trị</a>
    </div>
</div>

<div class="home-features">
    <div class="card home-feature">
        <div class="home-feature-icon">👥</div>
        <h3>Quản lý khách thuê</h3>
        <p class="text-muted">Theo dõi thông tin khách hàng, lịch sử thuê và trạng thái hoạt động.</p>
    </div>
    <div class="card home-feature">
        <div class="home-feature-icon">📋</div>
        <h3>Phiếu thuê thiết bị</h3>
        <p class="text-muted">Tạo, theo dõi phiếu thuê với mã phiếu không trùng, cảnh báo quá hạn.</p>
    </div>
    <div class="card home-feature">
        <div class="home-feature-icon">🔒</div>
        <h3>Bảo mật chuẩn MVC</h3>
        <p class="text-muted">Prepared statements, session bảo mật, validate server-side, PRG pattern.</p>
    </div>
</div>

<div class="card home-cta">
    <h2>Bạn muốn thuê thiết bị?</h2>
    <p class="text-muted">Điền form đăng ký, chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>
    <a href="/public-rental/create" class="btn primary">Đăng ký ngay →</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/main.php';
