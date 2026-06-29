<?php
/**
 * database/seed_data.php
 * Tạo tự động 100+ bản ghi để test pagination và EXPLAIN
 *
 * Chạy: php database/seed_data.php
 *
 * CẢNH BÁO: Script này XÓA dữ liệu generated cũ (id > 20 cho customers,
 * id > 20 cho rentals) trước khi insert mới.
 * Dữ liệu gốc trong seed.sql (id 1–20) được giữ nguyên.
 */

$config = require __DIR__ . '/../config/database.php';

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $config['host'],
    $config['database'],
    $config['charset']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

echo "=== Equipment Rental CRM — Seed Data Generator ===\n";
echo "Database: {$config['database']}\n\n";

// ─── 1. Xóa dữ liệu generated cũ ────────────────────────────────────────────
$pdo->exec("DELETE FROM rentals   WHERE id > 20");
$pdo->exec("DELETE FROM customers WHERE id > 20");
echo "✓ Cleared old generated records (kept seed.sql data)\n";

// ─── 2. Generate thêm 80 customers (id 21–100) ───────────────────────────────
$firstNames = [
    'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đặng', 'Bùi',
    'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Phan', 'Trịnh',
    'Cao', 'Mai', 'Tạ', 'Lưu',
];
$middleNames = ['Văn', 'Thị', 'Hoàng', 'Minh', 'Quang', 'Ngọc', 'Thanh', 'Đức'];
$lastNames   = [
    'An', 'Bình', 'Cường', 'Dũng', 'Em', 'Giang', 'Hùng', 'Khoa',
    'Linh', 'Minh', 'Nam', 'Oanh', 'Phong', 'Quyên', 'Sơn', 'Thảo',
    'Uyên', 'Vinh', 'Xuân', 'Yến',
];
$domains     = ['gmail.com', 'yahoo.com', 'outlook.com', 'studio.vn', 'photo.vn', 'media.vn'];
$statusPool  = ['active', 'active', 'active', 'active', 'inactive'];
$notePool    = [
    'Nhiếp ảnh gia tự do', 'Studio ảnh cưới', 'YouTuber', 'Công ty sự kiện',
    'Agency quảng cáo', 'Đài truyền hình', 'Phóng viên báo', 'Nhà sản xuất video',
    'Trường học', 'Doanh nghiệp', 'Freelancer video', 'Blogger du lịch',
];

$stmtC = $pdo->prepare(
    "INSERT INTO customers (customer_code, name, email, phone, status, note)
     VALUES (:customer_code, :name, :email, :phone, :status, :note)"
);

$custInserted = 0;
for ($i = 21; $i <= 100; $i++) {
    $fn   = $firstNames[array_rand($firstNames)];
    $mn   = $middleNames[array_rand($middleNames)];
    $ln   = $lastNames[array_rand($lastNames)];
    $name = "$fn $mn $ln";
    $slug = strtolower(
        preg_replace('/\s+/', '.', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name))
    );
    $num  = str_pad((string)$i, 3, '0', STR_PAD_LEFT);

    try {
        $stmtC->execute([
            'customer_code' => "KH-{$num}",
            'name'          => $name,
            'email'         => "{$slug}.{$i}@{$domains[array_rand($domains)]}",
            'phone'         => '09' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            'status'        => $statusPool[array_rand($statusPool)],
            'note'          => $notePool[array_rand($notePool)],
        ]);
        $custInserted++;
    } catch (PDOException $e) {
        echo "  Skip duplicate customer KH-{$num}\n";
    }
}
echo "✓ Inserted {$custInserted} customer records\n";

// ─── 3. Generate 100 rentals (id 21–120) ─────────────────────────────────────
$allCustIds   = $pdo->query("SELECT id FROM customers")->fetchAll(PDO::FETCH_COLUMN);
$statusPool2  = ['active', 'active', 'returned', 'returned', 'returned', 'overdue', 'cancelled'];
$equipSamples = [
    ['Máy ảnh Sony A7III',       'EQ-CAM-001', 350000],
    ['Máy ảnh Canon EOS R5',     'EQ-CAM-002', 500000],
    ['Máy ảnh Nikon Z6 II',      'EQ-CAM-003', 300000],
    ['Drone DJI Mavic 3 Pro',    'EQ-DRN-001', 600000],
    ['Drone DJI Air 3',          'EQ-DRN-002', 400000],
    ['Bộ đèn LED Aputure 300D',  'EQ-LGT-001', 250000],
    ['Đèn Flash Godox AD600 Pro','EQ-LGT-002', 200000],
    ['Ring Light 18 inch',       'EQ-LGT-003',  80000],
    ['Mic Rode NTG5',            'EQ-AUD-001', 150000],
    ['Bộ thu âm Zoom H6',        'EQ-AUD-002', 120000],
    ['Gimbal DJI RS 3 Pro',      'EQ-VID-001', 180000],
    ['Gimbal Zhiyun Crane 4',    'EQ-VID-002', 150000],
    ['Lens Sony 24-70 f/2.8 GM', 'EQ-LEN-001', 280000],
    ['Lens Canon RF 50mm f/1.2L','EQ-LEN-002', 320000],
    ['Lens Sigma 85mm f/1.4 Art','EQ-LEN-003', 200000],
    ['Chân máy Manfrotto 055',   'EQ-TRP-001', 120000],
    ['Monopod Sirui P-326S',     'EQ-TRP-002',  60000],
    ['Máy chiếu Epson EB-2250U', 'EQ-PRJ-001', 400000],
    ['Màn chiếu 100 inch',       'EQ-PRJ-002',  80000],
];
$notePool2 = [
    'Chụp tiệc cưới outdoor', 'Bay quay bất động sản', 'Quay TVC sản phẩm',
    'Phỏng vấn nhân vật', 'Livestream sự kiện', 'Chụp lookbook thời trang',
    'Review thiết bị', 'Quay phim tài liệu', 'Chụp ảnh thương mại',
    'Dự án phim ngắn', 'Hội nghị trực tuyến', 'Đào tạo nội bộ',
    'Chụp ảnh e-commerce', 'Quay vlog du lịch', 'Sự kiện âm nhạc',
    'Chụp ảnh chân dung', 'Quay clip MXH', 'Ghi âm podcast',
    'Quay giáo trình online', 'Chụp ảnh ẩm thực',
];

$stmtR = $pdo->prepare(
    "INSERT INTO rentals
        (rental_code, customer_id, equipment_name, equipment_code,
         rent_date, due_date, return_date, status,
         daily_rate, total_amount, note)
     VALUES
        (:rental_code, :customer_id, :equipment_name, :equipment_code,
         :rent_date, :due_date, :return_date, :status,
         :daily_rate, :total_amount, :note)"
);

$rentInserted = 0;
$year         = date('Y');

for ($i = 21; $i <= 120; $i++) {
    $code     = 'RNT-' . $year . '-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
    $custId   = $allCustIds[array_rand($allCustIds)];
    $equip    = $equipSamples[array_rand($equipSamples)];
    $status   = $statusPool2[array_rand($statusPool2)];
    $daysBack = rand(1, 180);
    $rentDate = date('Y-m-d', strtotime("-{$daysBack} days"));
    $duration = rand(1, 7);
    $dueDate  = date('Y-m-d', strtotime("{$rentDate} +{$duration} days"));
    $retDate  = null;

    if ($status === 'returned') {
        // Trả sớm hoặc đúng hạn
        $retDate = date('Y-m-d', strtotime("{$dueDate} -" . rand(0, 1) . " days"));
    } elseif ($status === 'overdue') {
        // Due date đã qua, chưa trả
        $dueDate = date('Y-m-d', strtotime("-" . rand(1, 30) . " days"));
    }

    $dailyRate   = $equip[2];
    $totalAmount = $status === 'cancelled' ? 0 : $dailyRate * $duration;

    try {
        $stmtR->execute([
            'rental_code'    => $code,
            'customer_id'    => $custId,
            'equipment_name' => $equip[0],
            'equipment_code' => $equip[1],
            'rent_date'      => $rentDate,
            'due_date'       => $dueDate,
            'return_date'    => $retDate,
            'status'         => $status,
            'daily_rate'     => $dailyRate,
            'total_amount'   => $totalAmount,
            'note'           => $notePool2[array_rand($notePool2)],
        ]);
        $rentInserted++;
    } catch (PDOException $e) {
        echo "  Skip rental {$code}: " . $e->getMessage() . "\n";
    }
}
echo "✓ Inserted {$rentInserted} rental records\n\n";

// ─── 4. Summary ──────────────────────────────────────────────────────────────
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalRentals  = $pdo->query("SELECT COUNT(*) FROM rentals")->fetchColumn();

echo "=== Final Count ===\n";
echo "Users     : {$totalUsers} records\n";
echo "Customers : {$totalCustomers} records\n";
echo "Rentals   : {$totalRentals} records\n";
echo "\nDone! Run the app:\n";
echo "  php -S localhost:8000 -t public\n";
echo "  http://localhost:8000/login\n";
echo "  admin@rentalcrm.com / password (xem seed.sql để đổi hash thật)\n";
