<?php
// app/Views/public/thank_you.php

ob_start(); ?>

<div class="thank-you-wrap">
    <div class="card thank-you-card">
        <div class="thank-you-icon">🎉</div>
        <h1>Đăng ký thành công!</h1>
        <p class="text-muted">Cảm ơn bạn đã quan tâm đến dịch vụ thuê thiết bị của chúng tôi.</p>
        <p class="text-muted">Chúng tôi sẽ liên hệ với bạn trong vòng <strong>24 giờ</strong> để xác nhận.</p>

        <?php if (!empty($inquiry)): ?>
        <div class="inquiry-summary card" style="margin-top:24px;text-align:left;">
            <h3>📋 Thông tin đã đăng ký</h3>
            <table class="info-table" style="margin-top:12px">
                <tr>
                    <th>Họ tên</th>
                    <td><?= e($inquiry['name']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= e($inquiry['email']) ?></td>
                </tr>
                <tr>
                    <th>Điện thoại</th>
                    <td><?= e($inquiry['phone']) ?></td>
                </tr>
                <tr>
                    <th>Thiết bị</th>
                    <td><?= e($inquiry['equipment_name']) ?></td>
                </tr>
                <?php if (!empty($inquiry['note'])): ?>
                <tr>
                    <th>Ghi chú</th>
                    <td><?= e($inquiry['note']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Thời gian</th>
                    <td class="text-muted"><?= e($inquiry['submitted_at']) ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <div class="thank-you-actions">
            <a href="/public-rental/create" class="btn primary">📋 Đăng ký thêm</a>
            <a href="/" class="btn secondary">🏠 Về trang chủ</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
