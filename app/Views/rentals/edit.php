<?php
// app/Views/rentals/edit.php

/** @var array  $customers */

ob_start();
$statusOptions = [
    'active'    => '⏳ Đang thuê',
    'returned'  => '✅ Đã trả',
    'overdue'   => '🚨 Quá hạn',
    'cancelled' => '❌ Hủy',
];
?>

<div class="page-header">
    <div>
        <h1>✏️ Sửa phiếu thuê <code class="code-tag"><?= e($item['rental_code'] ?? '') ?></code></h1>
        <p class="text-muted">Cập nhật thông tin phiếu thuê. ID: #<?= e($item['id'] ?? '') ?></p>
    </div>
    <a class="btn secondary" href="/rentals">← Quay lại</a>
</div>

<div class="form-layout">
    <form method="post" action="/rentals/update" class="card form-card" novalidate>
        <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">

        <!-- ─── Mã phiếu + Khách hàng ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="rental_code">Mã phiếu thuê <span class="required">*</span></label>
                <input type="text" id="rental_code" name="rental_code"
                       value="<?= e($old['rental_code'] ?? '') ?>"
                       style="text-transform:uppercase;font-weight:bold"
                       class="<?= !empty($errors['rental_code']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['rental_code'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['rental_code']) ?></div>
                <?php else: ?>
                    <div class="hint-text">⚠️ Đổi mã sẽ ảnh hưởng đến lịch sử hệ thống</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="customer_id">Khách hàng <span class="required">*</span></label>
                <select id="customer_id" name="customer_id"
                        class="<?= !empty($errors['customer_id']) ? 'is-invalid' : '' ?>">
                    <!-- Khách đang gắn với phiếu này (kể cả inactive) -->
                    <option value="<?= e($item['customer_id'] ?? '') ?>" selected>
                        <?= e(($item['customer_code'] ?? '') . ' — ' . ($item['customer_name'] ?? '')) ?>
                        (hiện tại)
                    </option>
                    <?php foreach ($customers as $cust): ?>
                        <?php if ((string)$cust['id'] !== (string)($item['customer_id'] ?? '')): ?>
                            <option value="<?= e($cust['id']) ?>"
                                    <?= (string)($old['customer_id'] ?? '') === (string)$cust['id'] ? 'selected' : '' ?>>
                                <?= e($cust['customer_code'] . ' — ' . $cust['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['customer_id'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['customer_id']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Thiết bị ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="equipment_name">Tên thiết bị <span class="required">*</span></label>
                <input type="text" id="equipment_name" name="equipment_name"
                       value="<?= e($old['equipment_name'] ?? '') ?>"
                       class="<?= !empty($errors['equipment_name']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['equipment_name'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['equipment_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="equipment_code">Mã thiết bị</label>
                <input type="text" id="equipment_code" name="equipment_code"
                       value="<?= e($old['equipment_code'] ?? '') ?>"
                       style="text-transform:uppercase">
            </div>
        </div>

        <!-- ─── Ngày tháng ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="rent_date">Ngày bắt đầu thuê <span class="required">*</span></label>
                <input type="date" id="rent_date" name="rent_date"
                       value="<?= e($old['rent_date'] ?? '') ?>"
                       class="<?= !empty($errors['rent_date']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['rent_date'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['rent_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="due_date">Ngày hẹn trả <span class="required">*</span></label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= e($old['due_date'] ?? '') ?>"
                       class="<?= !empty($errors['due_date']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['due_date'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['due_date']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Ngày trả thực tế + Trạng thái ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="return_date">Ngày trả thực tế</label>
                <input type="date" id="return_date" name="return_date"
                       value="<?= e($old['return_date'] ?? '') ?>">
                <div class="hint-text">Để trống nếu chưa trả</div>
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

        <!-- ─── Giá & Tổng tiền ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="daily_rate">Giá thuê / ngày (₫)</label>
                <input type="number" id="daily_rate" name="daily_rate"
                       value="<?= e((string)($old['daily_rate'] ?? '0')) ?>"
                       min="0" step="1000"
                       class="<?= !empty($errors['daily_rate']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['daily_rate'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['daily_rate']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="total_amount">Tổng tiền thuê (₫)</label>
                <input type="number" id="total_amount" name="total_amount"
                       value="<?= e((string)($old['total_amount'] ?? '0')) ?>"
                       min="0" step="1000"
                       class="<?= !empty($errors['total_amount']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['total_amount'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['total_amount']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Ghi chú ── -->
        <div class="form-group">
            <label for="note">Ghi chú</label>
            <textarea id="note" name="note" rows="2"><?= e($old['note'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">💾 Cập nhật</button>
            <a href="/rentals" class="btn secondary">Hủy</a>
        </div>
    </form>

    <!-- ─── Panel thông tin ── -->
    <div class="card info-panel">
        <h3>📋 Thông tin phiếu</h3>
        <table class="info-table">
            <tr><th>ID</th><td>#<?= e($item['id'] ?? '') ?></td></tr>
            <tr><th>Mã phiếu</th><td><code><?= e($item['rental_code'] ?? '') ?></code></td></tr>
            <tr><th>Khách thuê</th><td><?= e($item['customer_name'] ?? '') ?></td></tr>
            <tr><th>Thiết bị</th><td><?= e($item['equipment_name'] ?? '') ?></td></tr>
            <tr><th>Ngày tạo</th><td><?= e(substr($item['created_at'] ?? '', 0, 10)) ?></td></tr>
            <tr><th>Cập nhật</th><td><?= e(substr($item['updated_at'] ?? '—', 0, 10)) ?></td></tr>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
