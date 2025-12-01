<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Clase App
 * - Administra la inicialización del framework MVC-lite
 * - Carga autoload, config, sesión, y objetos compartidos (Router, DB, etc.)
 */
final class App
{
    public static Router $router;

    public static function boot(string $basePath): self
    {
        // Autoload sencillo por PSR-4 "manual"
        spl_autoload_register(function ($class) use ($basePath) {
            $prefix = 'App\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) return;
            $relative = substr($class, $len);
            $file = $basePath . 'app/' . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) require $file;
        });

        // Inicia sesión (necesaria para Auth/CSRF)
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Carga configuración
        Config::load($basePath . 'app/config/config.php',
                     $basePath . 'app/config/config.local.php'); // override opcional

        // Configura zona horaria
        date_default_timezone_set(Config::get('app.timezone', 'America/Costa_Rica'));

        // Inicializa conexión PDO (lazy dentro de DB)
        DB::init(Config::get('db'));

        // Crea Router
        self::$router = new Router();

        return new self();
    }

    public function dispatch(): void
    {
        self::$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}
