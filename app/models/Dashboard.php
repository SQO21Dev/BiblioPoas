<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Modelo Dashboard
 * Consultas agregadas para KPIs, resumen de tiquetes y datos de gráficos.
 */
final class Dashboard extends Model
{
    /**
     * KPIs principales del dashboard.
     *
     * - libros: total de libros
     * - clientes: total de clientes
     * - activos: tiquetes con estado "En Prestamo"f
     * - vencidos: tiquetes con estado "Atrasado"
     */
    public static function kpis(): array
    {
        $db = static::db();

        // Total de libros
        $libros = (int)$db->query("SELECT COUNT(*) FROM libros")->fetchColumn();

        // Total de clientes
        $clientes = (int)$db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();

        // Tiquetes activos (En Prestamo)
        $activos = (int)$db
            ->query("SELECT COUNT(*) FROM tiquetes WHERE estado = 'En Prestamo'")
            ->fetchColumn();

        // Tiquetes vencidos / atrasados
        $vencidos = (int)$db
            ->query("SELECT COUNT(*) FROM tiquetes WHERE estado = 'Atrasado'")
            ->fetchColumn();

        return [
            'libros'   => $libros,
            'clientes' => $clientes,
            'activos'  => $activos,
            'vencidos' => $vencidos,
        ];
    }

    /**
     * Lista de tiquetes activos y atrasados para la tabla del dashboard.
     *
     * Opcionalmente filtra por fecha_prestamo entre $fromDate y $toDate (YYYY-MM-DD).
     * Ordena primero los atrasados y luego por fecha de devolución ascendente.
     */
    public static function tiquetesCriticos(
        int $limit = 10,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $db = static::db();

        $where  = ["t.estado IN ('En Prestamo', 'Atrasado')"];
        $params = [];

        if ($fromDate) {
            $where[]            = 't.fecha_prestamo >= :from';
            $params[':from']    = $fromDate . ' 00:00:00';
        }

        if ($toDate) {
            $where[]            = 't.fecha_prestamo <= :to';
            $params[':to']      = $toDate . ' 23:59:59';
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT
                t.id,
                t.codigo,
                t.titulo,
                t.nombre_cliente,
                t.fecha_devolucion,
                t.estado
            FROM tiquetes t
            WHERE {$whereSql}
            ORDER BY
                (t.estado = 'Atrasado') DESC,
                t.fecha_devolucion ASC
            LIMIT :lim
        ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Datos crudos para gráfico de categorías de edad.
     * Devuelve: [ ['categoria' => 'OP', 'total' => 5], ... ]
     *
     * Si no se envía $fromDate/$toDate, se toma por defecto el mes actual
     * basado en fecha_prestamo.
     */
    public static function categoriaEdadRaw(?string $fromDate, ?string $toDate): array
    {
        $db     = static::db();
        $where  = [];
        $params = [];

        if ($fromDate || $toDate) {
            if ($fromDate) {
                $where[]         = 't.fecha_prestamo >= :from';
                $params[':from'] = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $where[]       = 't.fecha_prestamo <= :to';
                $params[':to'] = $toDate . ' 23:59:59';
            }
        } else {
            // Mes actual por defecto
            $firstDay = date('Y-m-01 00:00:00');
            $lastDay  = date('Y-m-t 23:59:59');
            $where[]  = 't.fecha_prestamo BETWEEN :from AND :to';
            $params[':from'] = $firstDay;
            $params[':to']   = $lastDay;
        }

        $whereSql = $where ? implode(' AND ', $where) : '1=1';

        $sql = "
            SELECT
                t.categoria_edad AS categoria,
                COUNT(*)         AS total
            FROM tiquetes t
            WHERE {$whereSql}
            GROUP BY t.categoria_edad
            ORDER BY t.categoria_edad
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Datos crudos para gráfico de estados de tiquetes.
     * Devuelve: [ ['estado' => 'En Prestamo', 'total' => 10], ... ]
     * Usa mismo criterio de fechas (fecha_prestamo) que categoriaEdadRaw.
     */
    public static function estadosRaw(?string $fromDate, ?string $toDate): array
    {
        $db     = static::db();
        $where  = [];
        $params = [];

        if ($fromDate || $toDate) {
            if ($fromDate) {
                $where[]         = 't.fecha_prestamo >= :from';
                $params[':from'] = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $where[]       = 't.fecha_prestamo <= :to';
                $params[':to'] = $toDate . ' 23:59:59';
            }
        } else {
            // Mismo comportamiento que el gráfico anterior: mes actual por defecto
            $firstDay = date('Y-m-01 00:00:00');
            $lastDay  = date('Y-m-t 23:59:59');
            $where[]  = 't.fecha_prestamo BETWEEN :from AND :to';
            $params[':from'] = $firstDay;
            $params[':to']   = $lastDay;
        }

        $whereSql = $where ? implode(' AND ', $where) : '1=1';

        $sql = "
            SELECT
                t.estado AS estado,
                COUNT(*) AS total
            FROM tiquetes t
            WHERE {$whereSql}
            GROUP BY t.estado
            ORDER BY t.estado
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
