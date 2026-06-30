<?php
// app/Views/rentals/index.php

/** @var array  $rentals     Danh sách phiếu thuê trang hiện tại */
/** @var int    $total       Tổng số bản ghi */
/** @var int    $page        Trang hiện tại */
/** @var int    $totalPages  Tổng số trang */
/** @var string $keyword     Từ khóa tìm kiếm */
/** @var string $status      Bộ lọc trạng thái */
/** @var string $sort        Cột đang sort */
/** @var string $direction   Chiều sort (asc/desc) */

ob_start();
$statusLabels = [
    'active'    => ['label' => '⏳ Đang thuê', 'class' => 'badge-info'],
    'returned'  => ['label' => '✅ Đã trả',    'class' => 'badge-success'],
    'overdue'   => ['label' => '🚨 Quá hạn',   'class' => 'badge-danger'],
    'cancelled' => ['label' => '❌ Hủy',        'class' => 'badge-muted'],
];
?>

<div class="page-header">
    <div>
        <h1>📋 Quản lý phiếu thuê</h1>
        <p class="text-muted">Tổng cộng <strong><?= e((string)$total) ?></strong> phiếu thuê</p>
    </div>
    <a class="btn primary" href="/rentals/create">+ Tạo phiếu thuê</a>
</div>

<!-- ─── Search & Filter ──────────────────────────────────────── -->
<div class="card toolbar">
    <form method="get" action="/rentals" class="toolbar-form">
        <input type="hidden" name="page" value="1">
        <input type="text" name="q" value="<?= e($keyword) ?>"
               placeholder="🔍 Tìm mã phiếu, tên khách, thiết bị..."
               class="input-search">
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <?php foreach ($statusLabels as $val => $info): ?>
                <option value="<?= e($val) ?>" <?= $status === $val ? 'selected' : '' ?>>
                    <?= e($info['label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn primary">Tìm kiếm</button>
        <?php if ($keyword || $status): ?>
            <a href="/rentals" class="btn secondary">✕ Xóa lọc</a>
        <?php endif; ?>
    </form>
</div>

<!-- ─── Table ───────────────────────────────────────────────── -->
<div class="card">
    <?php if (empty($rentals)): ?>
        <div class="empty-state">
            <p style="font-size:48px">📭</p>
            <h3>Không có phiếu thuê nào</h3>
            <p class="text-muted">Tạo phiếu thuê đầu tiên cho hệ thống.</p>
            <a href="/rentals/create" class="btn primary">+ Tạo phiếu thuê</a>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <a href="/rentals?<?= e(query_string(['sort'=>'r.rental_code','direction'=>($sort==='r.rental_code'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
                            Mã phiếu <?= $sort==='r.rental_code' ? ($direction==='asc'?'↑':'↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="/rentals?<?= e(query_string(['sort'=>'c.name','direction'=>($sort==='c.name'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
                            Khách thuê <?= $sort==='c.name' ? ($direction==='asc'?'↑':'↓') : '' ?>
                        </a>
                    </th>
                    <th>Thiết bị</th>
                    <th>
                        <a href="/rentals?<?= e(query_string(['sort'=>'r.rent_date','direction'=>($sort==='r.rent_date'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
                            Ngày thuê <?= $sort==='r.rent_date' ? ($direction==='asc'?'↑':'↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="/rentals?<?= e(query_string(['sort'=>'r.due_date','direction'=>($sort==='r.due_date'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
                            Hạn trả <?= $sort==='r.due_date' ? ($direction==='asc'?'↑':'↓') : '' ?>
                        </a>
                    </th>
                    <th>Trạng thái</th>
                    <th>
                        <a href="/rentals?<?= e(query_string(['sort'=>'r.total_amount','direction'=>($sort==='r.total_amount'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
                            Tổng tiền <?= $sort==='r.total_amount' ? ($direction==='asc'?'↑':'↓') : '' ?>
                        </a>
                    </th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentals as $r): ?>
                <?php $overdue = is_overdue($r['due_date'], $r['status']); ?>
                <tr class="<?= $overdue ? 'row-danger' : '' ?>">
                    <td><code>#<?= e($r['id']) ?></code></td>
                    <td><code class="code-tag"><?= e($r['rental_code']) ?></code></td>
                    <td>
                        <div><strong><?= e($r['customer_name']) ?></strong></div>
                        <div class="text-muted small"><?= e($r['customer_code']) ?></div>
                    </td>
                    <td>
                        <div><?= e($r['equipment_name']) ?></div>
                        <?php if ($r['equipment_code']): ?>
                            <div class="text-muted small"><?= e($r['equipment_code']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= e(format_date($r['rent_date'])) ?></td>
                    <td class="<?= $overdue ? 'text-danger' : '' ?>">
                        <?= e(format_date($r['due_date'])) ?>
                        <?php if ($overdue): ?>
                            <br><span class="badge badge-danger">Quá hạn!</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $st = $statusLabels[$r['status']] ?? ['label' => $r['status'], 'class' => 'badge-muted']; ?>
                        <span class="badge <?= $st['class'] ?>"><?= e($st['label']) ?></span>
                        <?php if ($r['return_date']): ?>
                            <div class="text-muted small">Trả: <?= e(format_date($r['return_date'])) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><strong><?= e(format_money((float)$r['total_amount'])) ?></strong></td>
                    <td>
                        <div class="action-btns">
                            <a href="/rentals/edit?id=<?= e($r['id']) ?>" class="btn btn-sm secondary">✏️ Sửa</a>
                            <form method="post" action="/rentals/delete" class="inline-form"
                                  onsubmit="return confirm('Xóa phiếu thuê «<?= e(addslashes($r['rental_code'])) ?>»?\nThao tác này không thể hoàn tác!')">
                                <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                                <button type="submit" class="btn btn-sm danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ─── Pagination ──────────────────────────────────────── -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="/rentals?<?= e(query_string(['page' => 1])) ?>" class="btn btn-sm secondary">«</a>
            <a href="/rentals?<?= e(query_string(['page' => $page - 1])) ?>" class="btn btn-sm secondary">‹ Trước</a>
        <?php endif; ?>

        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
            <a href="/rentals?<?= e(query_string(['page' => $p])) ?>"
               class="btn btn-sm <?= $p === $page ? 'primary' : 'secondary' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/rentals?<?= e(query_string(['page' => $page + 1])) ?>" class="btn btn-sm secondary">Sau ›</a>
            <a href="/rentals?<?= e(query_string(['page' => $totalPages])) ?>" class="btn btn-sm secondary">»</a>
        <?php endif; ?>

        <span class="pagination-info">Trang <?= $page ?> / <?= $totalPages ?> &nbsp;(<?= $total ?> bản ghi)</span>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
