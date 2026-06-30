<?php
// app/Services/PublicRentalService.php

class PublicRentalService
{
    private const RATE_LIMIT_SECONDS = 5; // giây tối thiểu giữa 2 lần submit

    // ─── Validate input form công khai ───────────────────────────────────────

    public function validate(array $input): array
    {
        $values = [
            'name'           => trim($input['name']           ?? ''),
            'email'          => trim($input['email']          ?? ''),
            'phone'          => trim($input['phone']          ?? ''),
            'equipment_name' => trim($input['equipment_name'] ?? ''),
            'note'           => trim($input['note']           ?? ''),
        ];
        $errors = [];

        // name: required, 2–100 ký tự
        if ($values['name'] === '') {
            $errors['name'] = 'Vui lòng nhập họ tên.';
        } elseif (mb_strlen($values['name']) < 2) {
            $errors['name'] = 'Họ tên phải có ít nhất 2 ký tự.';
        } elseif (mb_strlen($values['name']) > 100) {
            $errors['name'] = 'Họ tên không vượt quá 100 ký tự.';
        }

        // email: required + format
        if ($values['email'] === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng. VD: example@gmail.com';
        }

        // phone: required, 9–15 ký tự số/+/-/space
        if ($values['phone'] === '') {
            $errors['phone'] = 'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^[0-9+\-\s]{9,15}$/', $values['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (9–15 chữ số). VD: 0901234567';
        }

        // equipment_name: required, max 150
        if ($values['equipment_name'] === '') {
            $errors['equipment_name'] = 'Vui lòng nhập tên thiết bị muốn thuê.';
        } elseif (mb_strlen($values['equipment_name']) > 150) {
            $errors['equipment_name'] = 'Tên thiết bị không vượt quá 150 ký tự.';
        }

        return ['values' => $values, 'errors' => $errors];
    }

    // ─── Checklist T09a: Kiểm tra honeypot ───────────────────────────────────

    /**
     * Field ẩn "website" — người thật không thấy nên không điền.
     * Bot tự động điền mọi field → bị phát hiện.
     * Trả về true nếu phát hiện bot.
     */
    public function isHoneypotTriggered(array $input): bool
    {
        return !empty($input['website']);
    }

    // ─── Checklist T09b: Rate limit theo session ──────────────────────────────

    /**
     * Giới hạn: tối thiểu RATE_LIMIT_SECONDS giữa 2 lần submit.
     * Trả về true nếu submit quá nhanh (bị chặn).
     */
    public function isRateLimited(): bool
    {
        $last = $_SESSION['last_public_submit'] ?? 0;
        return (time() - $last) < self::RATE_LIMIT_SECONDS;
    }

    /**
     * Ghi lại thời điểm submit thành công vào session.
     */
    public function recordSubmit(): void
    {
        $_SESSION['last_public_submit'] = time();
    }

    // ─── Checklist T08: Lưu đăng ký ─────────────────────────────────────────

    /**
     * Lưu đăng ký vào DB (bảng inquiries) + session để trang cảm ơn hiển thị.
     * Nếu DB không kết nối được thì fallback lưu session và log lỗi.
     */
    public function save(array $values): void
    {
        // Lưu vào session để trang cảm ơn dùng (hiện 1 lần)
        $_SESSION['public_inquiry'] = [
            'name'           => $values['name'],
            'email'          => $values['email'],
            'phone'          => $values['phone'],
            'equipment_name' => $values['equipment_name'],
            'note'           => $values['note'],
            'submitted_at'   => date('d/m/Y H:i:s'),
        ];

        // Lưu vào DB — dùng prepared statement
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $pdo    = (new Database($config))->getConnection();

            $stmt = $pdo->prepare(
                "INSERT INTO inquiries (name, email, phone, equipment_name, note)
                 VALUES (:name, :email, :phone, :equipment_name, :note)"
            );
            $stmt->execute([
                'name'           => $values['name'],
                'email'          => $values['email'],
                'phone'          => $values['phone'],
                'equipment_name' => $values['equipment_name'],
                'note'           => $values['note'] ?: null,
            ]);
        } catch (Exception $e) {
            // DB lỗi → log, không hiện ra user, data vẫn có trong session
            log_error('PublicRentalService::save – ' . $e->getMessage());
        }
    }
}
