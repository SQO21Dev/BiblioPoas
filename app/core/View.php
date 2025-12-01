<?php
namespace App\Core;

final class View
{
    /**
     * Renderiza una vista PHP ubicada en app/views/{ruta}.php
     * Ej: View::render('auth/login', ['titulo' => 'Login'])
     */
    public static function render(string $view, array $params = []): void
    {
        // BASE_PATH = raíz del proyecto (la definimos en public/index.php)
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $viewFile = $basePath . '/app/views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: {$view}";
            exit;
        }

        // Disponibiliza $params como variables en la vista
        extract($params, EXTR_SKIP);

        // token CSRF disponible como $csrf
        if (!isset($csrf) && class_exists(\App\Core\CSRF::class)) {
            $csrf = \App\Core\CSRF::token();
        }

        // Permite a las vistas usar una variable $viewPath si lo requieren
        $viewPath = $viewFile;

        require $viewFile;
    }
}
