<?php
// app/Controllers/HomeController.php

class HomeController
{
    public function index(): void
    {
        // Đã login → redirect dashboard
        if (is_logged_in()) {
            redirect('/dashboard');
        }

        // Chưa login → redirect trang login
        redirect('/login');
    }
}
