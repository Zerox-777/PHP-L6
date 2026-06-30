<?php
// app/Views/rentals/create.php

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
        <h1>➕ Tạo phiếu thuê mới</h1>
        <p class="text-muted">Mã phiếu thuê phải là duy nhất. Hệ thống đã gợi ý mã tiếp theo.</p>
    </div>
    <a class="btn secondary" href="/rentals">← Quay lại</a>
</div>

<div class="form-layout">
    <form method="post" action="/rentals/store" class="card form-card" novalidate>

        <!-- ─── Mã phiếu + Khách hàng ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="rental_code">Mã phiếu thuê <span class="required">*</span></label>
                <input type="text" id="rental_code" name="rental_code"
                       value="<?= e($old['rental_code'] ?? '') ?>"
                       placeholder="VD: RNT-2026-0001"
                       style="text-transform:uppercase;font-weight:bold"
                       class="<?= !empty($errors['rental_code']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['rental_code'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['rental_code']) ?></div>
                <?php else: ?>
                    <div class="hint-text">Mã unique — hệ thống đã đề xuất mã tiếp theo</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="customer_id">Khách hàng <span class="required">*</span></label>
                <select id="customer_id" name="customer_id"
                        class="<?= !empty($errors['customer_id']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $cust): ?>
                        <option value="<?= e($cust['id']) ?>"
                                <?= (string)($old['customer_id'] ?? '') === (string)$cust['id'] ? 'selected' : '' ?>>
                            <?= e($cust['customer_code'] . ' — ' . $cust['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['customer_id'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['customer_id']) ?></div>
                <?php else: ?>
                    <div class="hint-text">Chỉ hiển thị khách hàng đang <strong>hoạt động</strong></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Thiết bị ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="equipment_name">Tên thiết bị <span class="required">*</span></label>
                <input type="text" id="equipment_name" name="equipment_name"
                       value="<?= e($old['equipment_name'] ?? '') ?>"
                       placeholder="VD: Máy ảnh Sony A7III"
                       class="<?= !empty($errors['equipment_name']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['equipment_name'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['equipment_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="equipment_code">Mã thiết bị</label>
                <input type="text" id="equipment_code" name="equipment_code"
                       value="<?= e($old['equipment_code'] ?? '') ?>"
                       placeholder="VD: EQ-CAM-001"
                       style="text-transform:uppercase">
                <div class="hint-text">Không bắt buộc — điền để tham chiếu</div>
            </div>
        </div>

        <!-- ─── Ngày tháng ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="rent_date">Ngày bắt đầu thuê <span class="required">*</span></label>
                <input type="date" id="rent_date" name="rent_date"
                       value="<?= e($old['rent_date'] ?? date('Y-m-d')) ?>"
                       class="<?= !empty($errors['rent_date']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['rent_date'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['rent_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="due_date">Ngày hẹn trả <span class="required">*</span></label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= e($old['due_date'] ?? date('Y-m-d', strtotime('+3 days'))) ?>"
                       class="<?= !empty($errors['due_date']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['due_date'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['due_date']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Giá & Tổng tiền ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="daily_rate">Giá thuê / ngày (₫)</label>
                <input type="number" id="daily_rate" name="daily_rate"
                       value="<?= e((string)($old['daily_rate'] ?? '')) ?>"
                       placeholder="VD: 350000"
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
                       placeholder="Tự tính hoặc nhập tay"
                       min="0" step="1000"
                       class="<?= !empty($errors['total_amount']) ? 'is-invalid' : '' ?>">
                <div class="hint-text" id="amount-hint">Chọn ngày + nhập giá để tính tự động</div>
                <?php if (!empty($errors['total_amount'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['total_amount']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── Trạng thái ── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label for="status">Trạng thái <span class="required">*</span></label>
                <select id="status" name="status">
                    <?php foreach ($statusOptions as $val => $lbl): ?>
                        <option value="<?= e($val) ?>"
                                <?= ($old['status'] ?? 'active') === $val ? 'selected' : '' ?>>
                            <?= e($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="return_date">Ngày trả thực tế</label>
                <input type="date" id="return_date" name="return_date"
                       value="<?= e($old['return_date'] ?? '') ?>">
                <div class="hint-text">Để trống nếu chưa trả</div>
            </div>
        </div>

        <!-- ─── Ghi chú ── -->
        <div class="form-group">
            <label for="note">Ghi chú</label>
            <textarea id="note" name="note" rows="2"
                      placeholder="Mục đích sử dụng, yêu cầu đặc biệt..."><?= e($old['note'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">💾 Tạo phiếu thuê</button>
            <a href="/rentals" class="btn secondary">Hủy</a>
            <span class="text-muted">Trường <span class="required">*</span> là bắt buộc</span>
        </div>
    </form>

    <!-- ─── Panel thông tin ── -->
    <div class="card info-panel">
        <h3>💡 Lưu ý</h3>
        <ul class="info-list">
            <li><strong>Mã phiếu</strong> phải là duy nhất — hệ thống báo nếu trùng</li>
            <li>Quy tắc mã: <code>RNT-[NĂM]-[SỐ]</code></li>
            <li>Ngày hẹn trả phải <strong>sau</strong> ngày bắt đầu thuê</li>
            <li>Tổng tiền tự tính khi nhập giá/ngày và chọn ngày</li>
            <li>Chỉ khách <strong>Hoạt động</strong> mới xuất hiện trong danh sách</li>
        </ul>
    </div>
</div>

<script>
// Tự động tính tổng tiền = daily_rate × số ngày
function calcTotal() {
    const rentDate  = document.getElementById('rent_date').value;
    const dueDate   = document.getElementById('due_date').value;
    const dailyRate = parseFloat(document.getElementById('daily_rate').value) || 0;
    const hint      = document.getElementById('amount-hint');

    if (!rentDate || !dueDate || dailyRate <= 0) return;

    const days  = Math.max(1, Math.round((new Date(dueDate) - new Date(rentDate)) / 86400000));
    const total = dailyRate * days;

    document.getElementById('total_amount').value = total;
    hint.textContent = days + ' ngày × ' + dailyRate.toLocaleString('vi-VN')
                     + ' ₫ = ' + total.toLocaleString('vi-VN') + ' ₫';
}

document.getElementById('rent_date').addEventListener('change',  calcTotal);
document.getElementById('due_date').addEventListener('change',   calcTotal);
document.getElementById('daily_rate').addEventListener('input',  calcTotal);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
