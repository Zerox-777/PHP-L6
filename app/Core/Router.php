<?php
// app/Core/Router.php

class Router
{
    private array $routes = [];

    /**
     * Đăng ký route GET
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Đăng ký route POST
     */
    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch request đến handler phù hợp
     * - Khớp method + path → thực thi controller@action
     * - Path có nhưng sai method → 405
     * - Hoàn toàn không tồn tại → 404
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Route khớp method + path → thực thi
        if (isset($this->routes[$method][$path])) {
            [$controller, $action] = $this->routes[$method][$path];
            (new $controller())->$action();
            return;
        }

        // Path tồn tại nhưng sai method → 405 Method Not Allowed
        foreach ($this->routes as $methodRoutes) {
            if (isset($methodRoutes[$path])) {
                http_response_code(405);
                render('errors/405', ['title' => '405 Method Not Allowed']);
                return;
            }
        }

        // Hoàn toàn không tồn tại → 404 Not Found
        http_response_code(404);
        render('errors/404', ['title' => '404 Not Found']);
    }
}
