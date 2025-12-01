<?php
namespace App\Core;

/**
 * Controller base unificado
 * Incluye:
 * - Sesión (asegura session_start)
 * - Autenticación y roles
 * - CSRF check
 * - Flash messages (para SweetAlert2)
 * - Redirecciones y helpers
 */
abstract class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Render rápido con el token CSRF disponible en $csrf */
    protected function render(string $view, array $params = []): void
    {
        $params['csrf'] = CSRF::token();
        View::render($view, $params);
    }

    /** Exigir sesión activa */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $this->flashError('Debes iniciar sesión.');
            $this->redirect('/login');
        }
    }

    /** Exigir rol/es (p.ej. ['admin']) */
    protected function requireRole(array $roles): void
    {
        $this->requireAuth();
        $rol = $_SESSION['user']['rol'] ?? '';
        if (!in_array($rol, $roles, true)) {
            $this->flashError('No tienes permisos para esta acción.');
            $this->redirect('/dashboard');
        }
    }

    /** Verifica CSRF y redirige si es inválido */
    protected function checkCsrf(?string $token, string $backUrl): void
    {
        if (!CSRF::verify($token)) {
            $this->flashError('Token CSRF inválido.');
            $this->redirect($backUrl);
        }
    }

    /** Redirección segura */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /** ---- Sistema de Flash con soporte para SweetAlert ---- */
    protected function flash(string $type, string $message, string $title = '', ?string $redirect = null): void
    {
        $_SESSION['flash'] = [
            'type'     => $type,
            'msg'      => $message,
            'title'    => $title ?: ucfirst($type),
            'redirect' => $redirect
        ];
    }

    protected function flashSuccess(string $message, string $title = 'Éxito', ?string $redirect = null): void
    {
        $this->flash('success', $message, $title, $redirect);
    }

    protected function flashError(string $message, string $title = 'Error', ?string $redirect = null): void
    {
        $this->flash('error', $message, $title, $redirect);
    }

    protected function flashInfo(string $message, string $title = 'Información', ?string $redirect = null): void
    {
        $this->flash('info', $message, $title, $redirect);
    }

    /** ---- Helper inputs ---- */
    protected function input(string $key, $default = '')
    {
        return isset($_POST[$key]) ? (is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key]) : $default;
    }
}
