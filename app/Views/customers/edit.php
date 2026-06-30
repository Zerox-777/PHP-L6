<?php
// app/Views/customers/edit.php

ob_start();
$statusOptions = [
    'active'    => 'Đang hoạt động',
    'inactive'  => 'Không hoạt động',
    'blacklist' => 'Blacklist',
];
?>

<div class="page-header">
    <div>
        <h1>✏️ Sửa khách thuê <code class="code-tag"><?= e($item['customer_code'] ?? '') ?></code></h1>
        <p class="text-muted">Cập nhật thông tin khách hàng. ID: #<?= e($item['id'] ?? '') ?></p>
    </div>
    <a class="btn secondary" href="/customers">← Quay lại</a>
</div>

<div class="form-layout">
    <form method="post" action="/customers/update" class="card form-card" novalidate>
        <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">

        <div class="form-grid-2">
            <div class="form-group">
                <label for="customer_code">Mã khách hàng <span class="required">*</span></label>
                <input type="text" id="customer_code" name="customer_code"
                       value="<?= e($old['customer_code'] ?? '') ?>"
                       style="text-transform:uppercase;font-weight:bold"
                       class="<?= !empty($errors['customer_code']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['customer_code'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['customer_code']) ?></div>
                <?php else: ?>
                    <div class="hint-text">⚠️ Đổi mã sẽ ảnh hưởng đến tất cả phiếu thuê liên quan</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái <span class="required">*</span></label>
                <select id="status" name="status">
                    <?php foreach ($statusOptions as $val => $lbl): ?>
                        <option value="<?= e($val) ?>"
                                <?= ($old['status'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= e($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="name">Họ tên <span class="required">*</span></label>
            <input type="text" id="name" name="name"
                   value="<?= e($old['name'] ?? '') ?>"
                   class="<?= !empty($errors['name']) ? 'is-invalid' : '' ?>">
            <?php if (!empty($errors['name'])): ?>
                <div class="error-text">⚠️ <?= e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="text" id="email" name="email"
                       value="<?= e($old['email'] ?? '') ?>"
                       class="<?= !empty($errors['email']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone"
                       value="<?= e($old['phone'] ?? '') ?>"
                       class="<?= !empty($errors['phone']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['phone']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="note">Ghi chú nội bộ</label>
            <textarea id="note" name="note" rows="2"><?= e($old['note'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">💾 Cập nhật</button>
            <a href="/customers" class="btn secondary">Hủy</a>
        </div>
    </form>

    <div class="card info-panel">
        <h3>📋 Thông tin hiện tại</h3>
        <table class="info-table">
            <tr><th>ID</th><td>#<?= e($item['id'] ?? '') ?></td></tr>
            <tr><th>Mã KH</th><td><code><?= e($item['customer_code'] ?? '') ?></code></td></tr>
            <tr><th>Ngày thêm</th><td><?= e(substr($item['created_at'] ?? '', 0, 10)) ?></td></tr>
            <tr><th>Cập nhật</th><td><?= e(substr($item['updated_at'] ?? '—', 0, 10)) ?></td></tr>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';