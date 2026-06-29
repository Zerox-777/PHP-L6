<?php
// app/Views/partials/flash.php
// Flash message tự xóa sau khi hiển thị (chỉ hiện 1 lần sau redirect)

$successMsg = get_flash('success');
$errorMsg   = get_flash('error');
$infoMsg    = get_flash('info');
?>
<?php if ($successMsg): ?>
    <div class="alert success" role="alert">✅ <?= e($successMsg) ?></div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert error" role="alert">❌ <?= e($errorMsg) ?></div>
<?php endif; ?>

<?php if ($infoMsg): ?>
    <div class="alert info" role="alert">ℹ️ <?= e($infoMsg) ?></div>
<?php endif; ?>
