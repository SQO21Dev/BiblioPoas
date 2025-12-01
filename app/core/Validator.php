<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Validador mínimo para requests
 */
final class Validator
{
    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $f) {
            if (!isset($data[$f]) || trim((string)$data[$f]) === '') {
                $errors[$f] = 'Campo requerido';
            }
        }
        return $errors;
    }

    public static function email(string $value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
