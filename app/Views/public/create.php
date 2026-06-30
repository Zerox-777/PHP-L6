<?php
// app/Views/public/create.php

ob_start(); ?>

<div class="page-header">
    <div>
        <h1>📋 Đăng ký thuê thiết bị</h1>
        <p class="text-muted">Điền thông tin bên dưới. Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ.</p>
    </div>
</div>

<div class="public-form-wrap">
    <div class="card form-card">

        <?php if (!empty($errors['general'])): ?>
            <div class="alert error">⏱️ <?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="post" action="/public-rental" novalidate>

            <!-- ═══ HONEYPOT — ẨN HOÀN TOÀN BẰNG CSS, KHÔNG DÙNG type="hidden" ═══ -->
            <!-- Bot tự động điền mọi field → bị chặn. Người thật không thấy → không điền. -->
            <div class="honeypot" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input type="text"
                       id="website"
                       name="website"
                       value=""
                       tabindex="-1"
                       autocomplete="off">
            </div>
            <!-- ═══════════════════════════════════════════════════════════════════ -->

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="name">Họ tên <span class="required">*</span></label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="<?= e($old['name'] ?? '') ?>"
                           placeholder="VD: Nguyễn Văn Minh"
                           class="<?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                           autocomplete="name">
                    <?php if (!empty($errors['name'])): ?>
                        <div class="error-text">⚠️ <?= e($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?= e($old['email'] ?? '') ?>"
                           placeholder="VD: example@gmail.com"
                           class="<?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                           autocomplete="email">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error-text">⚠️ <?= e($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="phone">Số điện thoại <span class="required">*</span></label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="<?= e($old['phone'] ?? '') ?>"
                           placeholder="VD: 0901234567"
                           class="<?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                           autocomplete="tel">
                    <?php if (!empty($errors['phone'])): ?>
                        <div class="error-text">⚠️ <?= e($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="equipment_name">Thiết bị muốn thuê <span class="required">*</span></label>
                    <input type="text"
                           id="equipment_name"
                           name="equipment_name"
                           value="<?= e($old['equipment_name'] ?? '') ?>"
                           placeholder="VD: Máy ảnh Sony A7III, Drone DJI..."
                           class="<?= !empty($errors['equipment_name']) ? 'is-invalid' : '' ?>">
                    <?php if (!empty($errors['equipment_name'])): ?>
                        <div class="error-text">⚠️ <?= e($errors['equipment_name']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Ghi chú / Mục đích sử dụng</label>
                <textarea id="note"
                          name="note"
                          rows="3"
                          placeholder="Mô tả mục đích sử dụng, thời gian cần thuê, địa điểm..."><?= e($old['note'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn primary">📤 Gửi đăng ký</button>
                <span class="text-muted">Trường <span class="required">*</span> là bắt buộc</span>
            </div>
        </form>
    </div>

    <div class="card info-panel">
        <h3>ℹ️ Thông tin liên hệ</h3>
        <ul class="info-list">
            <li>📞 <strong>Hotline:</strong> 0901 234 567</li>
            <li>📧 <strong>Email:</strong> contact@rentalcrm.vn</li>
            <li>🕐 <strong>Giờ làm việc:</strong> 8:00 – 18:00 (T2–T7)</li>
            <li>📍 <strong>Địa chỉ:</strong> 123 Nguyễn Huệ, Q.1, TP.HCM</li>
        </ul>
        <hr>
        <h3>📦 Thiết bị cho thuê</h3>
        <ul class="info-list">
            <li>📷 Camera, Lens các loại</li>
            <li>🚁 Drone DJI, Autel</li>
            <li>💡 Đèn LED, Flash</li>
            <li>🎙️ Micro, thiết bị âm thanh</li>
            <li>🎬 Gimbal, Stabilizer</li>
            <li>📐 Tripod, Monopod</li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
