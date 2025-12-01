<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Cargador de configuración (array) con archivo local que sobrescribe valores
 */
final class Config
{
    private static array $data = [];

    public static function load(string $main, ?string $local = null): void
    {
        $cfg = [];
        if (file_exists($main)) $cfg = require $main;
        if ($local && file_exists($local)) {
            $override = require $local;
            $cfg = array_replace_recursive($cfg, $override);
        }
        self::$data = $cfg;
    }

    public static function get(string $key, $default = null)
    {
        $ref = self::$data;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($ref) || !array_key_exists($segment, $ref)) return $default;
            $ref = $ref[$segment];
        }
        return $ref;
    }
}
