<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Models\Usuario;
use App\Models\Log;
use App\Services\Mailer;

class AuthController extends Controller
{
    /* ---------- VISTAS ---------- */

    public function loginForm(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/dashboard');
        }
        // No limpiamos flash aquí para permitir mostrar mensajes de éxito (p.ej. desde /forgot)
        $this->render('auth/login');
    }

    public function forgotForm(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/dashboard');
        }
        // Evitar que salten mensajes viejos (p.ej. "Credenciales inválidas")
        unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_redirect']);
        $this->render('auth/forgot');
    }

    public function resetForm(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/dashboard');
        }
        // Sólo permitir acceso si viene de login con contraseña temporal validada
        if (empty($_SESSION['reset_user_id'])) {
            $this->flashError('Primero solicita y usa tu contraseña temporal.');
            $this->redirect('/login');
        }
        $this->render('auth/reset', []);
    }

    /* ---------- ACCIONES ---------- */

    public function login(): void
    {
        $this->checkCsrf($_POST['_csrf'] ?? '', '/login');

        $usuario  = $this->input('usuario');
        $password = (string)$this->input('contrasena');

        if ($usuario === '' || $password === '') {
            $this->flashError('Usuario o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // Buscar usuario en DB
        $user = Usuario::findByUsuario($usuario);
        if (!$user) {
            $this->flashError('Usuario o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // 1) ¿Coincide contraseña normal (password_hash en DB)?
        $okNormal = Usuario::verifyPassword($user, $password);

        // 2) ¿Coincide con la contraseña temporal guardada en sesión?
        $okTemp = false;
        if (!empty($_SESSION['temp_pass_hash'])
            && !empty($_SESSION['temp_user_id'])
            && (int)$_SESSION['temp_user_id'] === (int)$user['id']
        ) {
            $okTemp = password_verify($password, $_SESSION['temp_pass_hash']);
        }

        // --- Caso: contraseña temporal válida → forzar reset ---
        if (!$okNormal && $okTemp) {
            $_SESSION['reset_user_id'] = (int)$user['id'];
            $this->flashSuccess('Contraseña temporal validada. Define una nueva contraseña.');
            $this->redirect('/reset');
        }

        // --- Ninguna coincide ---
        if (!$okNormal && !$okTemp) {
            $this->flashError('Usuario o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // --- Login normal OK ---

        // Guardar estructura original
        $_SESSION['user'] = [
            'id'      => (int)$user['id'],
            'usuario' => $user['usuario'],
            'rol'     => $user['rol'],
            'nombre'  => $user['nombre'],
            'correo'  => $user['correo'],
        ];

        // Variables “planas” para el resto del sistema (logs, helpers, etc.)
        $_SESSION['user_id']    = (int)$user['id'];
        $_SESSION['user_name']  = $user['nombre'] ?: $user['usuario'];
        $_SESSION['user_role']  = $user['rol'];
        $_SESSION['user_email'] = $user['correo'];

        // Alias de compatibilidad
        $_SESSION['usuario_nombre'] = $_SESSION['user_name'];
        $_SESSION['usuario_rol']    = $_SESSION['user_role'];
        $_SESSION['usuario']        = $user['usuario'];
        $_SESSION['rol']            = $user['rol'];

        // Limpiar estado de recuperación (por si quedó basura de antes)
        unset($_SESSION['temp_pass_hash'], $_SESSION['temp_user_id'], $_SESSION['reset_user_id']);

        // Registrar log de login
        Log::registrar(
            $_SESSION['user_name'],
            $_SESSION['user_role'],
            'login',
            'auth',
            'Inicio de sesión',
            'ok'
        );

        $this->flashSuccess('Sesión iniciada.');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        // Guardar datos antes de limpiar la sesión, para el log
        $usuarioActor = (string)(
            $_SESSION['usuario_nombre']
            ?? $_SESSION['user_name']
            ?? $_SESSION['usuario']
            ?? 'sistema'
        );

        $rol = (string)(
            $_SESSION['usuario_rol']
            ?? $_SESSION['user_role']
            ?? $_SESSION['rol']
            ?? 'sin_rol'
        );

        // Registrar log de logout
        Log::registrar(
            $usuarioActor,
            $rol,
            'logout',
            'auth',
            'Cierre de sesión',
            'ok'
        );

        // Limpiar toda la info relevante de sesión
        unset(
            $_SESSION['user'],
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_role'],
            $_SESSION['user_email'],
            $_SESSION['usuario_nombre'],
            $_SESSION['usuario_rol'],
            $_SESSION['usuario'],
            $_SESSION['rol'],
            $_SESSION['reset_user_id'],
            $_SESSION['temp_pass_hash'],
            $_SESSION['temp_user_id']
        );

        $this->flashSuccess('Sesión cerrada.');
        $this->redirect('/login');
    }

    /**
     * Genera una contraseña temporal, la guarda hasheada en SESIÓN
     * y envía el correo si el usuario existe.
     * (Ruta: POST /forgot)
     */
    public function sendTempPassword(): void
    {
        $this->checkCsrf($_POST['_csrf'] ?? '', '/forgot');

        $correo = $this->input('correo');
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Correo inválido.');
            $this->redirect('/forgot');
        }

        $user = Usuario::findByCorreo($correo);

        // Siempre respondemos genérico por seguridad
        $this->flashSuccess('Si el correo existe, te enviamos una contraseña temporal.');

        // Si no existe, no hacemos nada más
        if (!$user) {
            $this->redirect('/login');
        }

        // Generar contraseña temporal de 6 dígitos
        $temp = (string)random_int(100000, 999999);

        // Guardar hash en sesión, asociado a ese usuario
        $_SESSION['temp_pass_hash'] = password_hash($temp, PASSWORD_DEFAULT);
        $_SESSION['temp_user_id']   = (int)$user['id'];

        // Enviar correo (o volcar a storage/mail.log)
        $html = Mailer::buildTempPasswordEmail($user['nombre'] ?? $user['usuario'], $temp);
        Mailer::send($correo, 'Tu contraseña temporal - BiblioPoás', $html);

        $this->redirect('/login');
    }

    /**
     * Cambia la contraseña definitiva cuando el usuario llegó con temporal.
     * (Ruta: POST /reset)
     */
    public function doReset(): void
    {
        $this->checkCsrf($_POST['_csrf'] ?? '', '/reset');

        if (empty($_SESSION['reset_user_id'])) {
            $this->flashError('La sesión de restablecimiento expiró. Solicítala nuevamente.');
            $this->redirect('/login');
        }

        $p1 = (string)$this->input('contrasena');
        $p2 = (string)$this->input('contrasena2');

        if (strlen($p1) < 6 || $p1 !== $p2) {
            $this->flashError('Las contraseñas no coinciden o son muy cortas (mínimo 6).');
            $this->redirect('/reset');
        }

        $uid  = (int)$_SESSION['reset_user_id'];
        $hash = password_hash($p1, PASSWORD_DEFAULT);
        Usuario::updatePassword($uid, $hash);

        // Loguear y limpiar estado de reset
        $user = Usuario::findById($uid);
        unset($_SESSION['reset_user_id'], $_SESSION['temp_pass_hash'], $_SESSION['temp_user_id']);

        if ($user) {
            $_SESSION['user'] = [
                'id'      => (int)$user['id'],
                'usuario' => $user['usuario'],
                'rol'     => $user['rol'],
                'nombre'  => $user['nombre'],
                'correo'  => $user['correo'],
            ];

            // Variables planas + alias de compatibilidad
            $_SESSION['user_id']    = (int)$user['id'];
            $_SESSION['user_name']  = $user['nombre'] ?: $user['usuario'];
            $_SESSION['user_role']  = $user['rol'];
            $_SESSION['user_email'] = $user['correo'];

            $_SESSION['usuario_nombre'] = $_SESSION['user_name'];
            $_SESSION['usuario_rol']    = $_SESSION['user_role'];
            $_SESSION['usuario']        = $user['usuario'];
            $_SESSION['rol']            = $user['rol'];

            // Log de “login” tras reset
            Log::registrar(
                $_SESSION['user_name'],
                $_SESSION['user_role'],
                'login',
                'auth',
                'Inicio de sesión tras cambio de contraseña',
                'ok'
            );
        }

        $this->flashSuccess('Tu contraseña fue actualizada.');
        $this->redirect('/dashboard');
    }
}
