<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Modelo Dashboard
 * Consultas agregadas para KPIs y resumen de tiquetes.
 */
final class Dashboard extends Model
{
    /**
     * KPIs principales del dashboard.
     *
     * - libros: total de libros
     * - clientes: total de clientes
     * - activos: tiquetes con estado "En Prestamo"
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
     * Ordena primero los atrasados y luego por fecha de devolución ascendente.
     */
    public static function tiquetesCriticos(int $limit = 10): array
    {
        $db = static::db();

        $sql = "
            SELECT
                t.id,
                t.codigo,
                t.titulo,
                t.nombre_cliente,
                t.fecha_devolucion,
                t.estado
            FROM tiquetes t
            WHERE t.estado IN ('En Prestamo', 'Atrasado')
            ORDER BY
                (t.estado = 'Atrasado') DESC,
                t.fecha_devolucion ASC
            LIMIT :lim
        ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
