<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

/**
 * Modelo Tiquete
 * Gestiona los préstamos de libros (tabla tiquetes).
 */
final class Tiquete
{
    /* ==========================
       Listados / KPIs
       ========================== */

    /**
     * Listado principal para la tabla de tiquetes.
     * Incluye algunos datos relacionados (cliente/libro/usuario) si existen.
     */
    public static function all(): array
    {
        $sql = "
            SELECT 
                t.*,
                c.nombre AS cliente_rel,
                l.titulo AS libro_rel,
                u.nombre AS usuario_registra_rel
            FROM tiquetes t
            LEFT JOIN clientes c ON c.id = t.cliente_id
            LEFT JOIN libros   l ON l.id = t.libro_id
            LEFT JOIN usuarios u ON u.id = t.usuario_registra_id
            ORDER BY t.creado_en DESC
        ";

        return DB::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPIs básicos para la vista (total, activos, vencidos).
     * Activos = estado = 'En Prestamo'
     * Vencidos = estado = 'Atrasado'
     */
    public static function stats(): array
    {
        $pdo = DB::pdo();

        $total    = (int)$pdo->query("SELECT COUNT(*) FROM tiquetes")->fetchColumn();
        $activos  = (int)$pdo->query("SELECT COUNT(*) FROM tiquetes WHERE estado = 'En Prestamo'")->fetchColumn();
        $vencidos = (int)$pdo->query("SELECT COUNT(*) FROM tiquetes WHERE estado = 'Atrasado'")->fetchColumn();

        return [
            'total'    => $total,
            'activos'  => $activos,
            'vencidos' => $vencidos,
        ];
    }

    /**
     * Buscar un tiquete por ID.
     */
    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare("SELECT * FROM tiquetes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Buscar un libro por ID (para completar título y autor si hace falta).
     */
    public static function findLibroById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = DB::pdo()->prepare("SELECT id, titulo, autor FROM libros WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Lista de libros disponibles para el select del modal de tiquetes.
     */
    public static function disponiblesForSelect(): array
    {
        $pdo = DB::pdo();
        $sql = "
            SELECT id, titulo, autor, volumen
            FROM libros
            WHERE estado = 'Disponible'
            ORDER BY titulo ASC
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lista de clientes para el datalist del modal.
     */
    public static function clientesForSelect(): array
    {
        $pdo = DB::pdo();
        $sql = "
            SELECT id, nombre, cedula
            FROM clientes
            ORDER BY nombre ASC
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Verifica si un libro ya tiene un préstamo activo (estado 'En Prestamo').
     * Si se pasa $excluirTiqueteId, no considera ese tiquete (útil en update).
     */
    public static function libroEnPrestamo(int $libroId, ?int $excluirTiqueteId = null): bool
    {
        if ($libroId <= 0) {
            return false;
        }

        if ($excluirTiqueteId !== null && $excluirTiqueteId > 0) {
            $stmt = DB::pdo()->prepare(
                "SELECT COUNT(*)
                   FROM tiquetes
                  WHERE libro_id = :id
                    AND estado = 'En Prestamo'
                    AND id <> :excluir"
            );
            $stmt->execute([
                ':id'      => $libroId,
                ':excluir' => $excluirTiqueteId,
            ]);
        } else {
            $stmt = DB::pdo()->prepare(
                "SELECT COUNT(*)
                   FROM tiquetes
                  WHERE libro_id = :id
                    AND estado = 'En Prestamo'"
            );
            $stmt->execute([':id' => $libroId]);
        }

        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Marca un libro como Prestado en la tabla libros.
     */
    public static function marcarLibroPrestado(int $libroId): void
    {
        if ($libroId <= 0) return;

        $stmt = DB::pdo()->prepare("UPDATE libros SET estado = 'Prestado' WHERE id = :id");
        $stmt->execute([':id' => $libroId]);
    }

    /**
     * Marca un libro como Disponible en la tabla libros.
     * (Usado cuando el tiquete pasa a Devuelto).
     */
    public static function marcarLibroDisponible(int $libroId): void
    {
        if ($libroId <= 0) return;

        $stmt = DB::pdo()->prepare("UPDATE libros SET estado = 'Disponible' WHERE id = :id");
        $stmt->execute([':id' => $libroId]);
    }

    /**
     * OPCION 1 (lazy update):
     * Marca automáticamente como "Atrasado" los tiquetes que:
     * - están en estado "En Prestamo"
     * - y su fecha_devolucion ya venció (<= NOW())
     *
     * @return int Cantidad de filas afectadas.
     */
    public static function marcarAtrasados(): int
    {
        $pdo = DB::pdo();

        $stmt = $pdo->prepare("
            UPDATE tiquetes
               SET estado = 'Atrasado',
                   modificado_en = NOW()
             WHERE estado = 'En Prestamo'
               AND fecha_devolucion IS NOT NULL
               AND fecha_devolucion <= NOW()
        ");

        $stmt->execute();
        return (int)$stmt->rowCount();
    }

    /* ==========================
       Actualización rápida (Dashboard)
       ========================== */

    /**
     * Actualización rápida desde el Dashboard.
     *
     * - Cambia fecha de vencimiento.
     * - Si $cerrar = true, pasa el tiquete a 'Devuelto' y libera el libro.
     *
     * Nota: si NO se cierra y la fecha ya venció, lo marca como Atrasado.
     * Si estaba Atrasado y la nueva fecha queda futura, lo regresa a En Prestamo.
     */
    public static function quickUpdateDesdeDashboard(int $id, string $fechaDevolucion, bool $cerrar): bool
    {
        $pdo = DB::pdo();

        $value = trim($fechaDevolucion);
        if ($value === '') {
            throw new \RuntimeException('Fecha de vencimiento vacía.');
        }

        $value = str_replace('T', ' ', $value);
        if (strlen($value) === 16) { // 2025-10-10 09:00
            $value .= ':00';
        }

        $ts = strtotime($value);
        if ($ts === false) {
            throw new \RuntimeException('Formato de fecha de vencimiento inválido.');
        }
        $fechaNorm = date('Y-m-d H:i:s', $ts);

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("SELECT id, libro_id, estado FROM tiquetes WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new \RuntimeException('Tiquete no encontrado.');
            }

            $libroId      = (int)$row['libro_id'];
            $estadoActual = (string)$row['estado'];

            $now = date('Y-m-d H:i:s');

            if ($cerrar) {
                $nuevoEstado = 'Devuelto';
            } else {
                $nuevoEstado = $estadoActual;

                if ($estadoActual === 'En Prestamo' && $fechaNorm <= $now) {
                    $nuevoEstado = 'Atrasado';
                }

                if ($estadoActual === 'Atrasado' && $fechaNorm > $now) {
                    $nuevoEstado = 'En Prestamo';
                }
            }

            $up = $pdo->prepare("
                UPDATE tiquetes
                   SET fecha_devolucion = :fec,
                       estado           = :estado,
                       modificado_en    = NOW()
                 WHERE id = :id
            ");
            $up->execute([
                ':fec'    => $fechaNorm,
                ':estado' => $nuevoEstado,
                ':id'     => $id,
            ]);

            if ($cerrar && $libroId > 0) {
                $upLibro = $pdo->prepare("
                    UPDATE libros
                       SET estado = 'Disponible',
                           modificado_en = NOW()
                     WHERE id = :libro_id
                ");
                $upLibro->execute([':libro_id' => $libroId]);
            }

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /* ==========================
       Helpers
       ========================== */

    /**
     * Generar el siguiente código tipo: BBPO-0001, BBPO-0002, ...
     */
    public static function nextCodigo(): string
    {
        $pdo  = DB::pdo();
        $next = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) + 1 FROM tiquetes")->fetchColumn();

        return 'BBPO-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    /* ==========================
       Escrituras
       ========================== */

    /**
     * Crear un nuevo tiquete.
     */
    public static function create(array $data): int
    {
        $pdo = DB::pdo();

        if (empty($data['codigo'])) {
            $data['codigo'] = self::nextCodigo();
        }

        if (empty($data['categoria_edad'])) {
            $data['categoria_edad'] = 'HA';
        }

        $stmt = $pdo->prepare(
            "INSERT INTO tiquetes (
                codigo,
                cliente_id, nombre_cliente, telefono, direccion,
                libro_id, titulo, autor, signatura,
                categoria_edad,
                estado,
                fecha_prestamo, fecha_devolucion,
                usuario_registra_id,
                observaciones,
                nombre_biblioteca
            ) VALUES (
                :codigo,
                :cliente_id, :nombre_cliente, :telefono, :direccion,
                :libro_id, :titulo, :autor, :signatura,
                :categoria_edad,
                :estado,
                :fecha_prestamo, :fecha_devolucion,
                :usuario_registra_id,
                :observaciones,
                :nombre_biblioteca
            )"
        );

        $stmt->execute([
            ':codigo'              => $data['codigo'],
            ':cliente_id'          => $data['cliente_id'] ?? null,
            ':nombre_cliente'      => $data['nombre_cliente'],
            ':telefono'            => $data['telefono'] ?? null,
            ':direccion'           => $data['direccion'] ?? null,
            ':libro_id'            => $data['libro_id'] ?? null,
            ':titulo'              => $data['titulo'],
            ':autor'               => $data['autor'] ?? null,
            ':signatura'           => $data['signatura'] ?? null,
            ':categoria_edad'      => $data['categoria_edad'],
            ':estado'              => $data['estado'],
            ':fecha_prestamo'      => $data['fecha_prestamo'],
            ':fecha_devolucion'    => $data['fecha_devolucion'],
            ':usuario_registra_id' => $data['usuario_registra_id'],
            ':observaciones'       => $data['observaciones'] ?? null,
            ':nombre_biblioteca'   => $data['nombre_biblioteca']
                ?? 'Biblioteca Pública Semioficial de San Rafael de Poás',
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Actualizar un tiquete existente.
     * No tocamos el campo 'codigo' ni 'usuario_registra_id' por simplicidad.
     */
    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (
            [
                'cliente_id',
                'nombre_cliente',
                'telefono',
                'direccion',
                'libro_id',
                'titulo',
                'autor',
                'signatura',
                'categoria_edad',
                'estado',
                'fecha_prestamo',
                'fecha_devolucion',
                'observaciones',
                'nombre_biblioteca',
            ] as $f
        ) {
            if (array_key_exists($f, $data)) {
                $fields[]      = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }

        if (empty($fields)) {
            return;
        }

        $fields[] = "modificado_en = NOW()";

        $sql  = "UPDATE tiquetes SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Borrar tiquete.
     */
    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare("DELETE FROM tiquetes WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    /**
     * Listado crudo para exportar (CSV / Excel simple).
     * Puede filtrar opcionalmente por fecha_prestamo entre $fromDate y $toDate (YYYY-MM-DD).
     */
    public static function allForExport(?string $fromDate = null, ?string $toDate = null): array
    {
        $pdo    = DB::pdo();
        $sql    = "SELECT * FROM tiquetes";
        $where  = [];
        $params = [];

        if ($fromDate) {
            $where[]         = 'fecha_prestamo >= :from';
            $params[':from'] = $fromDate . ' 00:00:00';
        }

        if ($toDate) {
            $where[]       = 'fecha_prestamo <= :to';
            $params[':to'] = $toDate . ' 23:59:59';
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY creado_en DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
