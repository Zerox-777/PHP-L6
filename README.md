# Equipment Rental CRM — Lab06 Final

> PHP Secure MVC Mini CRM — Quản lý khách thuê thiết bị & phiếu thuê

---

## 🎯 Mô tả bài toán

Hệ thống quản lý nội bộ cho dịch vụ cho thuê thiết bị quay phim/chụp ảnh chuyên nghiệp:

- **Module A — Customers**: quản lý khách thuê, mã khách hàng (`customer_code`) không trùng.
- **Module B — Rentals**: quản lý phiếu thuê thiết bị, mã phiếu (`rental_code`) không trùng, theo dõi trạng thái và hạn trả.
- **Form công khai**: trang đăng ký tư vấn thuê thiết bị (`/public-rental/create`), không cần đăng nhập, có honeypot + rate limit chống spam.

---

## 🚀 Cách chạy project

### Yêu cầu môi trường

| Công cụ | Phiên bản |
|---|---|
| PHP | ≥ 8.1 |
| MySQL / MariaDB | ≥ 5.7 / 10.3 |
| Git | Bất kỳ |

### Cài đặt

```bash
# 1. Clone project
git clone https://github.com/Zerox-777/PHP-L6.git
cd PHP-L6

# 2. Tạo database + bảng
mysql -u root -p < database/schema.sql

# 3. Seed dữ liệu mẫu (tài khoản demo + 20 customers + 20 rentals)
mysql -u root -p equipment_crm_db < database/seed.sql

# 4. (Bonus) Tạo thêm 100+ bản ghi để test pagination/EXPLAIN
php database/seed_data.php

# 5. Đảm bảo thư mục log có quyền ghi
chmod -R 775 storage/

# 6. Kiểm tra / chỉnh thông tin kết nối DB nếu cần
nano config/database.php

# 7. Chạy server
php -S localhost:8000 -t public
```

Mở trình duyệt: **http://localhost:8000**

> ⚠️ **Lưu ý quan trọng:** `config/database.php` được commit thẳng vào repo (không nằm trong `.gitignore`) **có chủ đích** — đây là project học thuật, giảng viên cần chạy/chấm bài trực tiếp mà không phải tự cấu hình thêm. Thông tin trong file chỉ là `root` / mật khẩu rỗng cho môi trường local, không chứa secret thật.

### Tài khoản demo

| Email | Password | Role |
|---|---|---|
| `admin@rentalcrm.com` | `Admin@123` | admin |
| `staff@rentalcrm.com` | `Staff@123` | staff |

---

## 📁 Cấu trúc project

```
PHP-L6/
├── public/
│   ├── index.php                  # Front Controller (entry point)
│   └── assets/style.css
├── config/
│   ├── app.php
│   └── database.php
├── app/
│   ├── Core/
│   │   ├── Database.php           # PDO chuẩn (utf8mb4, EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false)
│   │   ├── Router.php             # Front Router (404/405)
│   │   ├── helpers.php            # e(), redirect(), render(), flash(), require_login()...
│   │   └── DuplicateRecordException.php
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── HealthController.php
│   │   ├── PublicRentalController.php
│   │   ├── CustomerController.php
│   │   └── RentalController.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── CustomerService.php
│   │   ├── RentalService.php
│   │   └── PublicRentalService.php
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── CustomerRepository.php
│   │   └── RentalRepository.php
│   └── Views/
│       ├── layouts/main.php
│       ├── partials/nav.php, flash.php
│       ├── auth/login.php
│       ├── dashboard/index.php
│       ├── public/create.php, thank_you.php
│       ├── customers/index.php, create.php, edit.php
│       ├── rentals/index.php, create.php, edit.php
│       └── errors/404.php, 405.php, 500.php
├── database/
│   ├── schema.sql
│   ├── seed.sql
│   └── seed_data.php              # bonus: 100+ rows tự động
└── storage/logs/app.log
```

---

## 🗺️ Route Map

| Method | URL | Controller@Action | Mô tả |
|---|---|---|---|
| GET | `/` | HomeController@index | Redirect → `/dashboard` (đã login) hoặc `/login` |
| GET | `/login` | AuthController@login | Form đăng nhập |
| POST | `/login` | AuthController@handleLogin | Validate + verify password + session regenerate + redirect |
| POST | `/logout` | AuthController@logout | Logout sạch + destroy session + redirect login |
| GET | `/dashboard` | DashboardController@index | Trang tổng quan (yêu cầu đăng nhập) |
| GET | `/health` | HealthController@index | JSON kiểm tra app/database |
| GET | `/public-rental/create` | PublicRentalController@create | Form công khai (honeypot + rate limit) |
| POST | `/public-rental` | PublicRentalController@store | Validate + anti-spam + PRG |
| GET | `/public-rental/thank-you` | PublicRentalController@thankYou | Trang cảm ơn sau khi đăng ký (PRG) |
| GET | `/customers` | CustomerController@index | List + search + pagination + sort safe |
| GET | `/customers/create` | CustomerController@create | Form thêm khách thuê |
| POST | `/customers/store` | CustomerController@store | Validate + create + duplicate handling + PRG |
| GET | `/customers/edit?id=1` | CustomerController@edit | Form sửa, lấy dữ liệu cũ theo id |
| POST | `/customers/update` | CustomerController@update | Validate + update + PRG |
| POST | `/customers/delete` | CustomerController@delete | Xóa bằng POST + PRG |
| GET | `/rentals` | RentalController@index | List + search + pagination + sort safe |
| GET | `/rentals/create` | RentalController@create | Form tạo phiếu thuê, tự gợi ý `rental_code` |
| POST | `/rentals/store` | RentalController@store | Validate + create + duplicate handling + PRG |
| GET | `/rentals/edit?id=1` | RentalController@edit | Form sửa, lấy dữ liệu cũ theo id |
| POST | `/rentals/update` | RentalController@update | Validate + update + PRG |
| POST | `/rentals/delete` | RentalController@delete | Xóa bằng POST + PRG |
| ANY | URL không tồn tại | Router | 404 Not Found |
| Sai method | Route có tồn tại | Router | 405 Method Not Allowed |

---

## 🗄️ Database Schema

### `users`
PK `id`, UNIQUE `email`, `role` ENUM('admin','staff'), `status` ENUM('active','inactive'), timestamps.

### `customers` (Module A)
PK `id`, UNIQUE `customer_code`, INDEX `email`/`status`/`created_at`, `status` ENUM('active','inactive','blacklist'), timestamps.

### `rentals` (Module B)
PK `id`, UNIQUE `rental_code`, FK `customer_id` → `customers(id)` (ON DELETE RESTRICT), INDEX `customer_id`/`status`/`rent_date`/`due_date`/`status+created_at`, `status` ENUM('active','returned','overdue','cancelled'), timestamps.

### `inquiries` (form công khai)
PK `id`, INDEX `email`/`status`/`created_at`, không yêu cầu FK bắt buộc (lead chưa qua xử lý).

---

## 🔒 Kỹ thuật bảo mật đã áp dụng

| Nhóm | Kỹ thuật |
|---|---|
| SQL Injection | Toàn bộ SQL dùng prepared statements (`prepare()` + `execute()`/`bindValue()`) |
| ORDER BY an toàn | Whitelist sort column + direction ở tầng Repository, không lấy thẳng từ `$_GET` |
| XSS | Mọi output qua `e()` = `htmlspecialchars()` |
| Duplicate key | UNIQUE constraint DB + bắt `PDOException` 1062 → `DuplicateRecordException` → thông báo thân thiện |
| PRG Pattern | POST thành công → redirect → GET, tránh submit trùng khi F5 |
| Anti-spam | Honeypot field ẩn (`website`) + rate limit 5 giây/session cho form công khai |
| Session | Cookie `HttpOnly`, `SameSite=Lax`, `Secure` theo môi trường; `session_regenerate_id(true)` sau login |
| Timeout | Idle timeout theo `config/app.php['session_timeout']` (mặc định 900s); kiểm tra User-Agent context |
| Logout sạch | Xóa `$_SESSION`, xóa cookie session, `session_destroy()` |
| Production safe error | Không hiện SQLSTATE/tên bảng/path; lỗi ghi vào `storage/logs/app.log` |
| Delete bằng POST | Không dùng GET link để xóa, có confirm dialog |
| 404/405 | Router phân biệt URL không tồn tại vs sai method |
| Pagination an toàn | Page âm/quá lớn được chuẩn hóa về 1 hoặc `totalPages` |

---

## 🧪 Test nhanh bằng curl

```bash
# TC21 - Health check
curl http://localhost:8000/health

# TC23 - 404
curl -I http://localhost:8000/khong-ton-tai

# TC22 - 405 (GET trên route chỉ nhận POST)
curl -X GET http://localhost:8000/customers/delete

# TC20 - Sort nguy hiểm
curl "http://localhost:8000/customers?sort=id+DESC;+DROP+TABLE+customers"

# TC18 - Page âm
curl "http://localhost:8000/customers?page=-99"

# TC08 - Honeypot (field website có dữ liệu → bị chặn ngầm)
curl -X POST http://localhost:8000/public-rental \
  -d "name=Bot&email=bot@example.com&phone=0901234567&equipment_name=Camera&website=http://spam.com"
```

---

## 📊 EXPLAIN Query mẫu

```sql
USE equipment_crm_db;

-- List customers theo status, sort theo created_at
EXPLAIN SELECT id, customer_code, name, email, phone, status, created_at
FROM customers
WHERE status = 'active'
ORDER BY created_at DESC
LIMIT 10 OFFSET 0;

-- List rentals JOIN customers, filter theo status
EXPLAIN SELECT r.id, r.rental_code, r.status, c.name
FROM rentals r
JOIN customers c ON r.customer_id = c.id
WHERE r.status = 'active'
ORDER BY r.created_at DESC
LIMIT 10 OFFSET 0;
```

*(Dán kết quả EXPLAIN thực tế + nhận xét cột `key` vào báo cáo PDF, mục T29/TC25.)*

---

## ⚙️ Đổi sang production

Trong `config/app.php`:
```php
'environment' => 'production',
'debug'       => false,
```

Trong `config/database.php`: thay bằng thông tin kết nối server thật (host/user/password riêng, không dùng `root`/rỗng).

---

## 📝 Ghi chú dev

- File log lỗi: `storage/logs/app.log` — không hiển thị ra ngoài, chỉ phục vụ debug nội bộ.