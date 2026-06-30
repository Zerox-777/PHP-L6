<?php
// app/Views/dashboard/index.php

ob_start(); ?>

<div class="page-header">
    <div>
        <h1>📊 Dashboard</h1>
        <p class="text-muted">
            Xin chào, <strong><?= e($_SESSION['user_name'] ?? '') ?></strong>!
            &nbsp;·&nbsp;
            <span class="badge badge-teal"><?= e(strtoupper($_SESSION['role'] ?? '')) ?></span>
        </p>
    </div>
    <div class="flex gap-2">
        <a class="btn primary" href="/customers/create">+ Thêm khách thuê</a>
        <a class="btn success" href="/rentals/create">+ Tạo phiếu thuê</a>
    </div>
</div>

<!-- ─── Stats Khách thuê ─────────────────────────────────────── -->
<h2 class="section-title">👥 Tổng quan khách thuê</h2>
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-icon">👤</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($custStats['total'] ?? 0)) ?></div>
            <div class="stat-label">Tổng khách</div>
        </div>
    </div>
    <div class="stat-card active">
        <div class="stat-icon">✅</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($custStats['active'] ?? 0)) ?></div>
            <div class="stat-label">Đang hoạt động</div>
        </div>
    </div>
    <div class="stat-card maintenance">
        <div class="stat-icon">⏸️</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($custStats['inactive'] ?? 0)) ?></div>
            <div class="stat-label">Không hoạt động</div>
        </div>
    </div>
    <div class="stat-card overdue">
        <div class="stat-icon">🚫</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($custStats['blacklist'] ?? 0)) ?></div>
            <div class="stat-label">Blacklist</div>
        </div>
    </div>
</div>

<!-- ─── Stats Phiếu thuê ─────────────────────────────────────── -->
<h2 class="section-title">📋 Tổng quan phiếu thuê</h2>
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-icon">📄</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($rentalStats['total'] ?? 0)) ?></div>
            <div class="stat-label">Tổng phiếu</div>
        </div>
    </div>
    <div class="stat-card active">
        <div class="stat-icon">⏳</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($rentalStats['active'] ?? 0)) ?></div>
            <div class="stat-label">Đang thuê</div>
        </div>
    </div>
    <div class="stat-card returned">
        <div class="stat-icon">✔️</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($rentalStats['returned'] ?? 0)) ?></div>
            <div class="stat-label">Đã trả</div>
        </div>
    </div>
    <div class="stat-card overdue">
        <div class="stat-icon">🚨</div>
        <div class="stat-body">
            <div class="stat-number"><?= e((string)($rentalStats['overdue'] ?? 0)) ?></div>
            <div class="stat-label">Quá hạn</div>
        </div>
    </div>
</div>

<!-- ─── Kiến trúc ────────────────────────────────────────────── -->
<div class="card mt-4">
    <h2>🏗️ Kiến trúc ứng dụng</h2>
    <div class="arch-flow">
        <div class="arch-step">🌐<br><small>Browser</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">⚡<br><small>index.php</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">🗺️<br><small>Router</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">🎮<br><small>Controller</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">⚙️<br><small>Service</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">🗃️<br><small>Repository</small></div>
        <div class="arch-arrow">→</div>
        <div class="arch-step">🐬<br><small>MySQL</small></div>
    </div>
</div>

<!-- ─── Route Map ────────────────────────────────────────────── -->
<div class="card">
    <h2>🗺️ Route Map</h2>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Method</th><th>URL</th><th>Controller@Action</th><th>Mô tả</th></tr>
            </thead>
            <tbody>
                <tr><td><span class="badge get">GET</span></td><td>/</td><td>HomeController@index</td><td>Redirect → /login hoặc /dashboard</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/login</td><td>AuthController@login</td><td>Form đăng nhập</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/login</td><td>AuthController@handleLogin</td><td>Validate + session_regenerate_id + redirect</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/logout</td><td>AuthController@logout</td><td>Destroy session sạch + redirect /login</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/dashboard</td><td>DashboardController@index</td><td>Tổng quan (require login)</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/health</td><td>HealthController@index</td><td>JSON kiểm tra DB</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/public-rental/create</td><td>PublicRentalController@create</td><td>Form công khai (honeypot + rate limit)</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/public-rental</td><td>PublicRentalController@store</td><td>Anti-spam + validate + PRG</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/customers</td><td>CustomerController@index</td><td>Danh sách + search + pagination + sort</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/customers/create</td><td>CustomerController@create</td><td>Form thêm khách</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/customers/store</td><td>CustomerController@store</td><td>Validate + duplicate handling + PRG</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/customers/edit?id=1</td><td>CustomerController@edit</td><td>Form sửa có dữ liệu cũ</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/customers/update</td><td>CustomerController@update</td><td>Validate + update + PRG</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/customers/delete</td><td>CustomerController@delete</td><td>Xóa bằng POST + PRG</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/rentals</td><td>RentalController@index</td><td>Danh sách + search + pagination + sort</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/rentals/create</td><td>RentalController@create</td><td>Form tạo phiếu thuê</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/rentals/store</td><td>RentalController@store</td><td>Validate + rental_code unique + PRG</td></tr>
                <tr><td><span class="badge get">GET</span></td><td>/rentals/edit?id=1</td><td>RentalController@edit</td><td>Form sửa có dữ liệu cũ</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/rentals/update</td><td>RentalController@update</td><td>Validate + update + PRG</td></tr>
                <tr><td><span class="badge post">POST</span></td><td>/rentals/delete</td><td>RentalController@delete</td><td>Xóa bằng POST + PRG</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
