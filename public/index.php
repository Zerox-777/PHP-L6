<?php
// public/index.php — Front Controller
// Flow: Browser → public/index.php → Router → Controller → Service → Repository → PDO → MySQL
//       → View/Redirect → Browser

// ─── Session Cookie Setup (PHẢI TRƯỚC session_start()) ────────────────────────
// Checklist T04: HttpOnly=true, SameSite=Lax, Secure theo môi trường
session_set_cookie_params([
    'lifetime' => 0,                                // session cookie (xóa khi đóng browser)
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']),          // true nếu HTTPS
    'httponly' => true,                              // JavaScript không đọc được cookie
    'samesite' => 'Lax',                             // chống CSRF cơ bản
]);

session_start();

// ─── Autoload Core ────────────────────────────────────────────────────────────
require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/DuplicateRecordException.php';

// ─── Autoload Repositories ────────────────────────────────────────────────────
require __DIR__ . '/../app/Repositories/UserRepository.php';
require __DIR__ . '/../app/Repositories/CustomerRepository.php';
require __DIR__ . '/../app/Repositories/RentalRepository.php';

// ─── Autoload Services ────────────────────────────────────────────────────────
require __DIR__ . '/../app/Services/AuthService.php';
require __DIR__ . '/../app/Services/CustomerService.php';
require __DIR__ . '/../app/Services/RentalService.php';
require __DIR__ . '/../app/Services/PublicRentalService.php';

// ─── Autoload Controllers ─────────────────────────────────────────────────────
require __DIR__ . '/../app/Controllers/HomeController.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/DashboardController.php';
require __DIR__ . '/../app/Controllers/HealthController.php';
require __DIR__ . '/../app/Controllers/CustomerController.php';
require __DIR__ . '/../app/Controllers/RentalController.php';
require __DIR__ . '/../app/Controllers/PublicRentalController.php';

// ─── Config ───────────────────────────────────────────────────────────────────
$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Ho_Chi_Minh');

// ─── Session Security Checks ──────────────────────────────────────────────────
// Checklist T04, T12: kiểm tra timeout và context sau mỗi request
check_session_timeout();
check_session_context();

// ─── Route Table ──────────────────────────────────────────────────────────────
$router = new Router();

// Public
$router->get('/',                  [HomeController::class,         'index']);
$router->get('/health',            [HealthController::class,       'index']);

// Auth
$router->get('/login',             [AuthController::class,         'login']);
$router->post('/login',            [AuthController::class,         'handleLogin']);
$router->post('/logout',           [AuthController::class,         'logout']);

// Dashboard (yêu cầu đăng nhập)
$router->get('/dashboard',         [DashboardController::class,    'index']);

// Public rental form (không cần đăng nhập, có honeypot/rate limit)
$router->get('/public-rental/create', [PublicRentalController::class, 'create']);
$router->post('/public-rental',       [PublicRentalController::class, 'store']);
$router->get('/public-rental/thank-you', [PublicRentalController::class, 'thankYou']);

// Module A: Customers (yêu cầu đăng nhập)
$router->get('/customers',         [CustomerController::class,     'index']);
$router->get('/customers/create',  [CustomerController::class,     'create']);
$router->post('/customers/store',  [CustomerController::class,     'store']);
$router->get('/customers/edit',    [CustomerController::class,     'edit']);
$router->post('/customers/update', [CustomerController::class,     'update']);
$router->post('/customers/delete', [CustomerController::class,     'delete']);

// Module B: Rentals (yêu cầu đăng nhập)
$router->get('/rentals',           [RentalController::class,       'index']);
$router->get('/rentals/create',    [RentalController::class,       'create']);
$router->post('/rentals/store',    [RentalController::class,       'store']);
$router->get('/rentals/edit',      [RentalController::class,       'edit']);
$router->post('/rentals/update',   [RentalController::class,       'update']);
$router->post('/rentals/delete',   [RentalController::class,       'delete']);

// ─── Dispatch ─────────────────────────────────────────────────────────────────
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
