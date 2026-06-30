<?php
// app/Controllers/PublicRentalController.php

class PublicRentalController
{
    private function service(): PublicRentalService
    {
        return new PublicRentalService();
    }

    // ─── GET /public-rental/create ────────────────────────────────────────────

    public function create(): void
    {
        // Form công khai — không yêu cầu đăng nhập
        render('public/create', [
            'title'  => 'Đăng ký thuê thiết bị — RentalCRM',
            'errors' => [],
            'old'    => [],
        ]);
    }

    // ─── POST /public-rental ──────────────────────────────────────────────────

    public function store(): void
    {
        $svc = $this->service();

        // ── Checklist T09a: Kiểm tra honeypot ────────────────────────────────
        // Nếu field ẩn "website" có dữ liệu → bot đang submit → im lặng reject
        if ($svc->isHoneypotTriggered($_POST)) {
            // Redirect như thành công để bot không biết bị chặn
            flash('success', '✅ Đăng ký của bạn đã được ghi nhận! Chúng tôi sẽ liên hệ sớm.');
            redirect('/public-rental/create');
            return;
        }

        // ── Checklist T09b: Rate limit — tối thiểu 5 giây giữa 2 lần submit ─
        if ($svc->isRateLimited()) {
            render('public/create', [
                'title'  => 'Đăng ký thuê thiết bị — RentalCRM',
                'errors' => ['general' => 'Bạn đang gửi quá nhanh. Vui lòng chờ vài giây rồi thử lại.'],
                'old'    => $_POST,
            ]);
            return;
        }

        // ── Checklist T07: Validate server-side ──────────────────────────────
        $result = $svc->validate($_POST);

        if (!empty($result['errors'])) {
            // Giữ old input, hiện lỗi cạnh field
            render('public/create', [
                'title'  => 'Đăng ký thuê thiết bị — RentalCRM',
                'errors' => $result['errors'],
                'old'    => $result['values'],
            ]);
            return;
        }

        // ── Lưu dữ liệu + ghi lại thời điểm submit ───────────────────────────
        $svc->save($result['values']);
        $svc->recordSubmit();

        // ── Checklist T08: PRG Pattern ────────────────────────────────────────
        // POST thành công → redirect trang cảm ơn
        // → F5 trên trang cảm ơn KHÔNG gửi lại form
        redirect('/public-rental/thank-you');
    }

    // ─── GET /public-rental/thank-you ─────────────────────────────────────────

    public function thankYou(): void
    {
        // Lấy thông tin đăng ký từ session (đã lưu ở store())
        $inquiry = $_SESSION['public_inquiry'] ?? null;

        // Xóa khỏi session sau khi hiển thị (chỉ hiện 1 lần)
        unset($_SESSION['public_inquiry']);

        render('public/thank_you', [
            'title'   => 'Đăng ký thành công — RentalCRM',
            'inquiry' => $inquiry,
        ]);
    }
}
