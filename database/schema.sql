-- database/schema.sql

CREATE DATABASE IF NOT EXISTS equipment_crm_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE equipment_crm_db;

-- ─── Bảng users ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    status        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_email (email)
);

-- ─── Bảng customers (Module A — Khách thuê thiết bị) ─────────────────────────
CREATE TABLE IF NOT EXISTS customers (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_code   VARCHAR(50)  NOT NULL,           -- Mã khách (unique)
    name            VARCHAR(100) NOT NULL,            -- Họ tên
    email           VARCHAR(150) NOT NULL,            -- Email liên hệ
    phone           VARCHAR(30)  NULL,               -- Số điện thoại
    status          ENUM('active', 'inactive', 'blacklist') NOT NULL DEFAULT 'active',
    note            TEXT         NULL,               -- Ghi chú nội bộ
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_customer_code  (customer_code),
    INDEX  idx_customers_email      (email),
    INDEX  idx_customers_status     (status),
    INDEX  idx_customers_created_at (created_at)
);

-- ─── Bảng rentals (Module B — Phiếu thuê thiết bị) ──────────────────────────
CREATE TABLE IF NOT EXISTS rentals (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    rental_code     VARCHAR(50)  NOT NULL,           -- Mã phiếu thuê (unique)
    customer_id     INT          NOT NULL,            -- FK → customers
    equipment_name  VARCHAR(150) NOT NULL,            -- Tên thiết bị thuê
    equipment_code  VARCHAR(50)  NULL,               -- Mã thiết bị (tham khảo)
    rent_date       DATE         NOT NULL,            -- Ngày bắt đầu thuê
    due_date        DATE         NOT NULL,            -- Ngày hẹn trả
    return_date     DATE         NULL,               -- Ngày trả thực tế
    status          ENUM('active', 'returned', 'overdue', 'cancelled')
                        NOT NULL DEFAULT 'active',
    daily_rate      DECIMAL(12,2) NOT NULL DEFAULT 0.00, -- Giá thuê/ngày
    total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0.00, -- Tổng tiền thuê
    note            TEXT         NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rental_code          (rental_code),
    INDEX  idx_rentals_customer_id        (customer_id),
    INDEX  idx_rentals_status             (status),
    INDEX  idx_rentals_rent_date          (rent_date),
    INDEX  idx_rentals_due_date           (due_date),
    INDEX  idx_rentals_status_created_at  (status, created_at),
    CONSTRAINT fk_rental_customer
        FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ─── Bảng inquiries (Form công khai — Bước 5) ────────────────────────────────
-- Lưu đăng ký từ form công khai /public-rental/create
-- Không yêu cầu login, không FK bắt buộc
CREATE TABLE IF NOT EXISTS inquiries (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    phone           VARCHAR(30)  NOT NULL,
    equipment_name  VARCHAR(150) NOT NULL,
    note            TEXT         NULL,
    status          ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_inquiries_email      (email),
    INDEX idx_inquiries_status     (status),
    INDEX idx_inquiries_created_at (created_at)
);
