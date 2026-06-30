<?php
// app/Controllers/HomeController.php

class HomeController
{
    public function index(): void
    {
        // Đã login → vào thẳng dashboard
        if (is_logged_in()) {
            redirect('/dashboard');
        }
 
        // Chưa login → hiển thị trang giới thiệu công khai
        render('home', [
            'title' => 'Equipment Rental CRM',
        ]);
    }
}