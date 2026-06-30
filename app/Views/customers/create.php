<?php
// app/Views/customers/create.php

ob_start();
$statusOptions = [
    'active'    => 'Đang hoạt động',
    'inactive'  => 'Không hoạt động',
    'blacklist' => 'Blacklist',
];
?>

<div class="page-header">
    <div>
        <h1>➕ Thêm khách thuê mới</h1>
        <p class="text-muted">Mã khách hàng phải là duy nhất trong hệ thống.</p>
    </div>
    <a class="btn secondary" href="/customers">← Quay lại</a>
</div>

<div class="form-layout">
    <form method="post" action="/customers/store" class="card form-card" novalidate>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="customer_code">Mã khách hàng <span class="required">*</span></label>
                <input type="text" id="customer_code" name="customer_code"
                       value="<?= e($old['customer_code'] ?? '') ?>"
                       placeholder="VD: KH-001"
                       style="text-transform:uppercase;font-weight:bold"
                       class="<?= !empty($errors['customer_code']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['customer_code'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['customer_code']) ?></div>
                <?php else: ?>
                    <div class="hint-text">Chữ hoa, số, dấu gạch ngang (3–20 ký tự). VD: KH-001</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái <span class="required">*</span></label>
                <select id="status" name="status"
                        class="<?= !empty($errors['status']) ? 'is-invalid' : '' ?>">
                    <?php foreach ($statusOptions as $val => $lbl): ?>
                        <option value="<?= e($val) ?>"
                                <?= ($old['status'] ?? 'active') === $val ? 'selected' : '' ?>>
                            <?= e($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['status'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['status']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="name">Họ tên <span class="required">*</span></label>
            <input type="text" id="name" name="name"
                   value="<?= e($old['name'] ?? '') ?>"
                   placeholder="VD: Nguyễn Văn Minh"
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
                       placeholder="VD: nguyen@gmail.com"
                       class="<?= !empty($errors['email']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone"
                       value="<?= e($old['phone'] ?? '') ?>"
                       placeholder="VD: 0901234567"
                       class="<?= !empty($errors['phone']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['phone']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="note">Ghi chú nội bộ</label>
            <textarea id="note" name="note" rows="2"
                      placeholder="Mục đích thuê, loại thiết bị hay dùng..."><?= e($old['note'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">💾 Lưu khách hàng</button>
            <a href="/customers" class="btn secondary">Hủy</a>
            <span class="text-muted">Trường <span class="required">*</span> là bắt buộc</span>
        </div>
    </form>

    <div class="card info-panel">
        <h3>💡 Lưu ý</h3>
        <ul class="info-list">
            <li><strong>Mã KH</strong> phải là duy nhất — hệ thống báo lỗi nếu trùng</li>
            <li>Quy tắc mã: <code>KH-[SỐ]</code>, VD: <code>KH-001</code></li>
            <li>Trạng thái <strong>Blacklist</strong> vẫn giữ lịch sử, chỉ cảnh báo khi tạo phiếu mới</li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';