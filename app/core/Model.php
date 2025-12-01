<?php
namespace App\Core;

use App\Core\DB;
use PDO;

/**
 * Clase base para todos los modelos.
 * Proporciona conexión DB y métodos genéricos.
 */
abstract class Model
{
    /**
     * Nombre de la tabla. Cada modelo debe definirlo.
     */
    protected static string $table = '';

    /**
     * Retorna la conexión PDO usando DB::pdo()
     */
    public static function db(): PDO
    {
        return DB::pdo();
    }

    /**
     * Obtener todos los registros de la tabla asociada.
     */
    public static function all(): array
    {
        $db    = static::db();
        $table = static::$table;

        $stmt = $db->query("SELECT * FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
