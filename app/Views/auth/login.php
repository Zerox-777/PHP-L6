<?php
// app/Views/auth/login.php

ob_start(); ?>

<div class="auth-wrapper">
    <div class="auth-card card">
        <div class="auth-header">
            <div class="auth-logo">🔧</div>
            <h1>RentalCRM</h1>
            <p class="text-muted">Equipment Rental Management</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert error">❌ <?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="post" action="/login" class="auth-form" novalidate>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= e($old['email'] ?? '') ?>"
                       placeholder="admin@rentalcrm.com"
                       class="<?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                       autocomplete="email"
                       autofocus>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu <span class="required">*</span></label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="••••••••"
                       class="<?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                       autocomplete="current-password">
                <?php if (!empty($errors['password'])): ?>
                    <div class="error-text">⚠️ <?= e($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn primary btn-block">🔑 Đăng nhập</button>
        </form>

        <div class="auth-footer">
            <hr>
            <p class="text-muted" style="margin-top:12px">
                <strong>Demo:</strong><br>
                Admin: <code>admin@rentalcrm.com</code> / <code>Admin@123</code><br>
                Staff: <code>staff@rentalcrm.com</code> / <code>Staff@123</code>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
