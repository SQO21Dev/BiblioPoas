<?php
namespace App\Core;

/**
 * Protección CSRF simple basada en sesión.
 */
final class CSRF
{
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verify(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /** opcional: regenerar después de un submit ok */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
}
