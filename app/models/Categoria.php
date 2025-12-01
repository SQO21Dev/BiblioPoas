<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Modelo Categoria
 * Tabla: categorias
 */
final class Categoria extends Model
{
    protected static string $table = 'categorias';

    /**
     * Obtener todas las categorías ordenadas por nombre
     */
    public static function all(): array
    {
        $db  = static::db();
        $sql = "
            SELECT id, nombre, descripcion, creado_en, modificado_en
              FROM categorias
          ORDER BY nombre ASC
        ";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Crear una nueva categoría.
     * Retorna el ID insertado.
     */
    public static function create(array $data): int
    {
        $db = static::db();

        $stmt = $db->prepare("
            INSERT INTO categorias (nombre, descripcion)
            VALUES (:nombre, :descripcion)
        ");

        $stmt->execute([
            ':nombre'      => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Actualizar categoría existente.
     */
    public static function update(int $id, array $data): void
    {
        $db = static::db();

        $stmt = $db->prepare("
            UPDATE categorias
               SET nombre = :nombre,
                   descripcion = :descripcion
             WHERE id = :id
             LIMIT 1
        ");

        $stmt->execute([
            ':id'          => $id,
            ':nombre'      => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
        ]);
    }

    /**
     * Eliminar categoría.
     */
    public static function delete(int $id): void
    {
        $db = static::db();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
    }

    /**
     * ¿Existe una categoría con ese nombre?
     * Si $excludeId se pasa, ignora ese ID (útil en edición).
     */
    public static function existsNombre(string $nombre, ?int $excludeId = null): bool
    {
        $db = static::db();

        if ($excludeId !== null) {
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                  FROM categorias
                 WHERE nombre = :nombre
                   AND id <> :id
            ");
            $stmt->execute([
                ':nombre' => $nombre,
                ':id'     => $excludeId,
            ]);
        } else {
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                  FROM categorias
                 WHERE nombre = :nombre
            ");
            $stmt->execute([':nombre' => $nombre]);
        }

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Listado para exportar (CSV / Excel simple).
     */
    public static function allForExport(): array
    {
        $db  = static::db();
        $sql = "
            SELECT id, nombre, descripcion, creado_en, modificado_en
              FROM categorias
          ORDER BY nombre ASC
        ";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
