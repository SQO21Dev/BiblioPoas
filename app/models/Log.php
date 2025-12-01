<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

/**
 * Modelo Log
 * Gestiona la tabla "logs" para auditoría.
 *
 * Tabla:
 *  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *  fecha_evento  DATETIME NOT NULL,
 *  usuario_actor VARCHAR(120) NOT NULL,
 *  rol           VARCHAR(50)  NOT NULL,
 *  accion        VARCHAR(50)  NOT NULL,
 *  entidad       VARCHAR(50)  NOT NULL,
 *  descripcion   VARCHAR(255) DEFAULT NULL,
 *  resultado     VARCHAR(50)  DEFAULT NULL
 */
final class Log
{
    /**
     * Registrar un evento en la tabla logs.
     */
    public static function registrar(
        string $usuarioActor,
        string $rol,
        string $accion,
        string $entidad,
        ?string $descripcion = null,
        ?string $resultado = 'ok'
    ): void {
        $pdo = DB::pdo();

        $stmt = $pdo->prepare("
            INSERT INTO logs (
                fecha_evento,
                usuario_actor,
                rol,
                accion,
                entidad,
                descripcion,
                resultado
            ) VALUES (
                NOW(),
                :usuario_actor,
                :rol,
                :accion,
                :entidad,
                :descripcion,
                :resultado
            )
        ");

        $stmt->execute([
            ':usuario_actor' => $usuarioActor,
            ':rol'           => $rol,
            ':accion'        => $accion,
            ':entidad'       => $entidad,
            ':descripcion'   => $descripcion,
            ':resultado'     => $resultado,
        ]);
    }

    /**
     * Búsqueda con filtros simples para la vista de logs.
     */
    public static function buscar(array $filters = []): array
    {
        $pdo    = DB::pdo();
        $where  = [];
        $params = [];

        if (!empty($filters['fini'])) {
            $where[]              = 'fecha_evento >= :fini';
            $params[':fini']      = $filters['fini'] . ' 00:00:00';
        }
        if (!empty($filters['ffin'])) {
            $where[]              = 'fecha_evento <= :ffin';
            $params[':ffin']      = $filters['ffin'] . ' 23:59:59';
        }
        if (!empty($filters['entidad'])) {
            $where[]              = 'entidad = :entidad';
            $params[':entidad']   = $filters['entidad'];
        }
        if (!empty($filters['accion'])) {
            $where[]              = 'accion = :accion';
            $params[':accion']    = $filters['accion'];
        }

        $sql = "SELECT * FROM logs";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY fecha_evento DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Contar resultados para los filtros dados.
     */
    public static function contar(array $filters = []): int
    {
        $pdo    = DB::pdo();
        $where  = [];
        $params = [];

        if (!empty($filters['fini'])) {
            $where[]              = 'fecha_evento >= :fini';
            $params[':fini']      = $filters['fini'] . ' 00:00:00';
        }
        if (!empty($filters['ffin'])) {
            $where[]              = 'fecha_evento <= :ffin';
            $params[':ffin']      = $filters['ffin'] . ' 23:59:59';
        }
        if (!empty($filters['entidad'])) {
            $where[]              = 'entidad = :entidad';
            $params[':entidad']   = $filters['entidad'];
        }
        if (!empty($filters['accion'])) {
            $where[]              = 'accion = :accion';
            $params[':accion']    = $filters['accion'];
        }

        $sql = "SELECT COUNT(*) FROM logs";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Listado crudo para exportación (respeta filtros).
     */
    public static function exportar(array $filters = []): array
    {
        return self::buscar($filters);
    }
}
