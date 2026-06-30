<?php
// app/Views/customers/index.php

/** @var array  $customers   Danh sách khách hàng trang hiện tại */
/** @var int    $total       Tổng số bản ghi */
/** @var int    $page        Trang hiện tại */
/** @var int    $totalPages  Tổng số trang */
/** @var string $keyword     Từ khóa tìm kiếm */
/** @var string $status      Bộ lọc trạng thái */
/** @var string $sort        Cột đang sort */
/** @var string $direction   Chiều sort (asc/desc) */

ob_start();
$statusLabels = [
    'active'    => ['label' => 'Hoạt động',      'class' => 'badge-success'],
    'inactive'  => ['label' => 'Không HĐ',       'class' => 'badge-muted'],
    'blacklist' => ['label' => 'Blacklist',       'class' => 'badge-danger'],
];
?>

<div class="page-header">
    <div>
        <h1>👥 Quản lý khách thuê</h1>
        <p class="text-muted">Tổng cộng <strong><?= e((string)$total) ?></strong> khách hàng</p>
    </div>
    <a class="btn primary" href="/customers/create">+ Thêm khách thuê</a>
</div>

<!-- ─── Search & Filter ──────────────────────────────────────── -->
<div class="card toolbar">
    <form method="get" action="/customers" class="toolbar-form">
        <input type="hidden" name="page" value="1">
        <input type="text" name="q" value="<?= e($keyword) ?>"
               placeholder="🔍 Tìm tên, mã, email, SĐT..."
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
            <a href="/customers" class="btn secondary">✕ Xóa lọc</a>
        <?php endif; ?>
    </form>
</div>

<!-- ─── Table ───────────────────────────────────────────────── -->
<div class="card">
    <?php if (empty($customers)): ?>
        <div class="empty-state">
            <p style="font-size:48px">📭</p>
            <h3>Không tìm thấy khách hàng nào</h3>
            <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc.</p>
            <a href="/customers/create" class="btn primary">+ Thêm khách thuê đầu tiên</a>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <a href="/customers?<?= e(query_string(['sort' => 'id', 'direction' => ($sort==='id' && $direction==='asc') ? 'desc' : 'asc', 'page' => 1])) ?>">
                            # <?= $sort === 'id' ? ($direction === 'asc' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="/customers?<?= e(query_string(['sort' => 'customer_code', 'direction' => ($sort==='customer_code' && $direction==='asc') ? 'desc' : 'asc', 'page' => 1])) ?>">
                            Mã KH <?= $sort === 'customer_code' ? ($direction === 'asc' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="/customers?<?= e(query_string(['sort' => 'name', 'direction' => ($sort==='name' && $direction==='asc') ? 'desc' : 'asc', 'page' => 1])) ?>">
                            Họ tên <?= $sort === 'name' ? ($direction === 'asc' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>Email / SĐT</th>
                    <th>
                        <a href="/customers?<?= e(query_string(['sort' => 'status', 'direction' => ($sort==='status' && $direction==='asc') ? 'desc' : 'asc', 'page' => 1])) ?>">
                            Trạng thái <?= $sort === 'status' ? ($direction === 'asc' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="/customers?<?= e(query_string(['sort' => 'created_at', 'direction' => ($sort==='created_at' && $direction==='asc') ? 'desc' : 'asc', 'page' => 1])) ?>">
                            Ngày thêm <?= $sort === 'created_at' ? ($direction === 'asc' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td><code>#<?= e($c['id']) ?></code></td>
                    <td><code class="code-tag"><?= e($c['customer_code']) ?></code></td>
                    <td><strong><?= e($c['name']) ?></strong></td>
                    <td>
                        <div><?= e($c['email']) ?></div>
                        <?php if ($c['phone']): ?>
                            <div class="text-muted small">📞 <?= e($c['phone']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $st = $statusLabels[$c['status']] ?? ['label' => $c['status'], 'class' => 'badge-muted']; ?>
                        <span class="badge <?= $st['class'] ?>"><?= e($st['label']) ?></span>
                    </td>
                    <td class="text-muted"><?= e(substr($c['created_at'], 0, 10)) ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="/customers/edit?id=<?= e($c['id']) ?>" class="btn btn-sm secondary">✏️ Sửa</a>
                            <form method="post" action="/customers/delete" class="inline-form"
                                  onsubmit="return confirm('Xóa khách hàng «<?= e(addslashes($c['name'])) ?>»?\nThao tác này không thể hoàn tác!')">
                                <input type="hidden" name="id" value="<?= e($c['id']) ?>">
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
            <a href="/customers?<?= e(query_string(['page' => 1])) ?>" class="btn btn-sm secondary">«</a>
            <a href="/customers?<?= e(query_string(['page' => $page - 1])) ?>" class="btn btn-sm secondary">‹ Trước</a>
        <?php endif; ?>

        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
            <a href="/customers?<?= e(query_string(['page' => $p])) ?>"
               class="btn btn-sm <?= $p === $page ? 'primary' : 'secondary' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/customers?<?= e(query_string(['page' => $page + 1])) ?>" class="btn btn-sm secondary">Sau ›</a>
            <a href="/customers?<?= e(query_string(['page' => $totalPages])) ?>" class="btn btn-sm secondary">»</a>
        <?php endif; ?>

        <span class="pagination-info">Trang <?= $page ?> / <?= $totalPages ?> &nbsp;(<?= $total ?> bản ghi)</span>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
