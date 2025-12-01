<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Gestión de autenticación sencilla por sesión
 */
final class Auth
{
    public static function attempt(string $usuario, string $password): bool
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :u LIMIT 1');
        $stmt->execute([':u' => $usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['contrasena'])) {
            $_SESSION['uid'] = (int)$user['id'];
            $_SESSION['uname'] = $user['nombre'];
            $_SESSION['urole'] = $user['rol'];
            return true;
        }
        return false;
    }

    public static function userId(): ?int
    {
        return $_SESSION['uid'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['urole'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['uid']);
    }

    public static function requireRole(array $roles): void
    {
        if (!self::check() || !in_array(self::role(), $roles, true)) {
            Response::redirect('/login');
        }
    }

    public static function logout(): void
    {
        session_destroy();
    }
}
