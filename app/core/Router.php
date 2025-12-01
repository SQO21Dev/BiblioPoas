<?php
namespace App\Core;

final class Router
{
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path   = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path   = $this->normalize($path);

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo "Ruta no encontrada: {$method} {$path}";
            return;
        }

        if (is_array($handler)) {
            // ['App\Controllers\AuthController', 'loginForm']
            [$class, $action] = $handler;
            if (!class_exists($class)) {
                http_response_code(500);
                echo "Controlador no existe: {$class}";
                return;
            }
            $controller = new $class(); // Controller base arranca sesión
            if (!method_exists($controller, $action)) {
                http_response_code(500);
                echo "Método no existe: {$class}::{$action}";
                return;
            }
            $controller->{$action}();
            return;
        }

        // Closure/callable
        call_user_func($handler);
    }

    private function normalize(string $path): string
    {
        if ($path === '') $path = '/';
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        return $path;
    }
}
