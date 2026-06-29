-- database/seed.sql
-- Cách chạy:
--   mysql -u root -p equipment_crm_db < database/seed.sql
--
-- Tài khoản demo:
--   admin@rentalcrm.com / Admin@123
--   staff@rentalcrm.com / Staff@123

USE equipment_crm_db;

-- ─── Users ───────────────────────────────────────────────────────────────────
-- password_hash được tạo bằng: password_hash('Admin@123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password_hash, role, status) VALUES
('Admin Hệ Thống', 'admin@rentalcrm.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 'active'),
('Nhân Viên Kho',  'staff@rentalcrm.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'staff', 'active');
-- Lưu ý: hash trên tương ứng password 'password' (demo).
-- Khi chạy thật, hãy tạo hash riêng bằng PHP:
--   php -r "echo password_hash('Admin@123', PASSWORD_DEFAULT);"

-- ─── Customers (20 khách thuê mẫu) ──────────────────────────────────────────
INSERT INTO customers (customer_code, name, email, phone, status, note) VALUES
('KH-001', 'Nguyễn Văn Minh',    'minh.nguyen@gmail.com',     '0901234567', 'active',    'Khách quen, hay thuê camera'),
('KH-002', 'Trần Thị Lan',       'lan.tran@outlook.com',      '0912345678', 'active',    'Nhiếp ảnh gia tự do'),
('KH-003', 'Lê Hoàng Nam',       'nam.le@company.vn',         '0923456789', 'active',    'Studio ảnh chuyên nghiệp'),
('KH-004', 'Phạm Thị Thu',       'thu.pham@studio.vn',        '0934567890', 'active',    'Quay phim TVC'),
('KH-005', 'Hoàng Văn Đức',      'duc.hoang@freelance.vn',    '0945678901', 'active',    'Freelancer video'),
('KH-006', 'Vũ Thị Hoa',         'hoa.vu@photostudio.vn',     '0956789012', 'active',    'Chủ studio ảnh cưới'),
('KH-007', 'Đặng Minh Tuấn',     'tuan.dang@youtube.com',     '0967890123', 'active',    'YouTuber, cần drone thường xuyên'),
('KH-008', 'Bùi Thị Yến',        'yen.bui@weddingphoto.vn',   '0978901234', 'active',    'Nhiếp ảnh ảnh cưới'),
('KH-009', 'Ngô Văn Thắng',      'thang.ngo@eventco.vn',      '0989012345', 'active',    'Công ty tổ chức sự kiện'),
('KH-010', 'Đinh Thị Mai',       'mai.dinh@broadcast.vn',     '0990123456', 'active',    'Đài truyền hình địa phương'),
('KH-011', 'Trương Văn Long',    'long.truong@podcast.vn',    '0901122334', 'active',    'Podcaster, cần mic tốt'),
('KH-012', 'Lý Thị Kim',         'kim.ly@filmmaker.vn',       '0912233445', 'active',    'Đạo diễn phim ngắn'),
('KH-013', 'Hà Văn Bình',        'binh.ha@commercial.vn',     '0923344556', 'active',    'Agency quảng cáo'),
('KH-014', 'Tạ Thị Ngọc',        'ngoc.ta@portrait.vn',       '0934455667', 'active',    'Nhiếp ảnh chân dung'),
('KH-015', 'Mai Văn Dũng',       'dung.mai@sports.vn',        '0945566778', 'active',    'Nhiếp ảnh thể thao'),
('KH-016', 'Cao Thị Liên',       'lien.cao@conference.vn',    '0956677889', 'active',    'Tổ chức hội nghị'),
('KH-017', 'Dương Văn Hải',      'hai.duong@training.vn',     '0967788990', 'active',    'Trung tâm đào tạo'),
('KH-018', 'Lưu Thị Phương',     'phuong.luu@magazine.vn',    '0978899001', 'active',    'Tạp chí thời trang'),
('KH-019', 'Phan Minh Khoa',     'khoa.phan@badclient.vn',    '0989900112', 'blacklist', 'Trả thiết bị hư hỏng, không bồi thường'),
('KH-020', 'Võ Thị Thu Hương',   'huong.vo@inactive.vn',      '0900011223', 'inactive',  'Đã nghỉ kinh doanh');

-- ─── Rentals (20 phiếu thuê mẫu) ────────────────────────────────────────────
INSERT INTO rentals
    (rental_code, customer_id, equipment_name, equipment_code,
     rent_date, due_date, return_date, status,
     daily_rate, total_amount, note)
VALUES
-- Đã trả (returned)
('RNT-2026-0001',  1, 'Máy ảnh Sony A7III',        'EQ-CAM-001', '2026-04-01', '2026-04-03', '2026-04-03', 'returned',  350000,  700000, 'Chụp tiệc cưới outdoor'),
('RNT-2026-0002',  2, 'Drone DJI Mavic 3 Pro',     'EQ-DRN-001', '2026-04-05', '2026-04-07', '2026-04-07', 'returned',  600000, 1200000, 'Bay chụp bất động sản'),
('RNT-2026-0003',  3, 'Bộ đèn LED Aputure 300D',   'EQ-LGT-001', '2026-04-10', '2026-04-12', '2026-04-11', 'returned',  250000,  500000, 'Quay TVC nội thất'),
('RNT-2026-0004',  8, 'Mic Rode NTG5',             'EQ-AUD-001', '2026-04-15', '2026-04-17', '2026-04-17', 'returned',  150000,  300000, 'Ghi âm phỏng vấn'),
('RNT-2026-0005',  9, 'Đèn Flash Godox AD600 Pro', 'EQ-LGT-002', '2026-04-20', '2026-04-21', '2026-04-21', 'returned',  200000,  200000, 'Sự kiện ngoài trời'),
('RNT-2026-0006', 10, 'Bộ thu âm Zoom H6',         'EQ-AUD-002', '2026-05-01', '2026-05-03', '2026-05-02', 'returned',  120000,  240000, 'Phỏng vấn nhân vật'),
('RNT-2026-0007', 14, 'Chân máy Manfrotto 055',    'EQ-TRP-001', '2026-05-05', '2026-05-06', '2026-05-06', 'returned',  120000,  120000, 'Chụp chân dung ngoài trời'),
('RNT-2026-0008', 15, 'Monopod Sirui P-326S',      'EQ-TRP-002', '2026-05-10', '2026-05-11', '2026-05-11', 'returned',   60000,   60000, 'Chụp thể thao'),
('RNT-2026-0009', 18, 'Lens Canon RF 50mm f/1.2L', 'EQ-LEN-002', '2026-05-15', '2026-05-17', '2026-05-17', 'returned',  320000,  640000, 'Chụp tạp chí thời trang'),
('RNT-2026-0010', 11, 'Gimbal DJI RS 3 Pro',       'EQ-VID-001', '2026-05-20', '2026-05-22', '2026-05-22', 'returned',  180000,  360000, 'Quay phim tài liệu'),

-- Đang thuê (active)
('RNT-2026-0011',  4, 'Máy ảnh Canon EOS R5',      'EQ-CAM-002', '2026-06-01', '2026-06-05', NULL,         'active',    500000, 2000000, 'Dự án phim ngắn'),
('RNT-2026-0012',  5, 'Ring Light 18 inch',         'EQ-LGT-003', '2026-06-02', '2026-06-04', NULL,         'active',     80000,  160000, 'Livestream sản phẩm'),
('RNT-2026-0013',  6, 'Gimbal Zhiyun Crane 4',      'EQ-VID-002', '2026-06-03', '2026-06-06', NULL,         'active',    150000,  450000, 'Chụp fashion lookbook'),
('RNT-2026-0014',  7, 'Lens Sigma 85mm f/1.4 Art',  'EQ-LEN-003', '2026-06-05', '2026-06-07', NULL,         'active',    200000,  400000, 'Review sản phẩm YouTube'),
('RNT-2026-0015', 12, 'Lens Sony 24-70 f/2.8 GM',  'EQ-LEN-001', '2026-06-08', '2026-06-10', NULL,         'active',    280000,  560000, 'Quảng cáo thương mại'),
('RNT-2026-0016', 13, 'Máy chiếu Epson EB-2250U',  'EQ-PRJ-001', '2026-06-10', '2026-06-12', NULL,         'active',    400000,  800000, 'Hội nghị doanh nghiệp'),
('RNT-2026-0017', 17, 'Màn chiếu 100 inch',         'EQ-PRJ-002', '2026-06-11', '2026-06-13', NULL,         'active',     80000,  160000, 'Đào tạo nhân viên'),

-- Quá hạn (overdue) — due_date đã qua nhưng chưa trả
('RNT-2026-0018',  8, 'Máy ảnh Nikon Z6 II',       'EQ-CAM-003', '2026-04-15', '2026-04-17', NULL,         'overdue',   300000,  900000, 'Chụp pre-wedding (quá hạn)'),
('RNT-2026-0019', 16, 'Drone DJI Air 3',            'EQ-DRN-002', '2026-05-01', '2026-05-04', NULL,         'overdue',   400000, 1200000, 'Quay sự kiện (chưa liên lạc được)'),

-- Hủy (cancelled)
('RNT-2026-0020',  3, 'Bộ Filter ND Kase 82mm',    'EQ-ACC-001', '2026-05-25', '2026-05-27', NULL,         'cancelled',  50000,       0, 'Khách hủy do thời tiết xấu');
