<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

final class Cliente
{
    /* ==========================
       Lecturas / Listados
       ========================== */

    /** Lista para la tabla de clientes */
    public static function all(): array
    {
        $sql = "SELECT id, nombre, cedula, telefono, correo, direccion, estado, creado_en
                  FROM clientes
              ORDER BY creado_en DESC";
        return DB::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** KPIs básicos para la vista */
    public static function stats(): array
    {
        $pdo = DB::pdo();

        $tot = (int)$pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        $act = (int)$pdo->query("SELECT COUNT(*) FROM clientes WHERE estado = 'activo'")->fetchColumn();

        // Por ahora dejamos préstamos activos en 0 (se puede ligar luego a la tabla tiquetes)
        $conPrestamo = 0;

        return [
            'total'        => $tot,
            'activos'      => $act,
            'con_prestamo' => $conPrestamo,
        ];
    }

    /* ==========================
       Búsquedas / Existencia
       ========================== */

    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM clientes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function existsCedula(string $cedula, ?int $excludeId = null): bool
    {
        $cedula = trim($cedula);
        if ($cedula === '') {
            return false;
        }

        if ($excludeId) {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM clientes WHERE cedula = :c AND id <> :id'
            );
            $stmt->execute([':c' => $cedula, ':id' => $excludeId]);
        } else {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM clientes WHERE cedula = :c'
            );
            $stmt->execute([':c' => $cedula]);
        }

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existsCorreo(string $correo, ?int $excludeId = null): bool
    {
        $correo = trim($correo);
        if ($correo === '') {
            return false;
        }

        if ($excludeId) {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM clientes WHERE correo = :c AND id <> :id'
            );
            $stmt->execute([':c' => $correo, ':id' => $excludeId]);
        } else {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM clientes WHERE correo = :c'
            );
            $stmt->execute([':c' => $correo]);
        }

        return (int)$stmt->fetchColumn() > 0;
    }

    /* ==========================
       Escrituras
       ========================== */

    public static function create(array $data): int
    {
        $stmt = DB::pdo()->prepare(
            'INSERT INTO clientes (nombre, cedula, telefono, direccion, correo, estado)
             VALUES (:nombre, :cedula, :telefono, :direccion, :correo, :estado)'
        );

        $stmt->execute([
            ':nombre'    => $data['nombre'],
            ':cedula'    => $data['cedula'] ?? null,
            ':telefono'  => $data['telefono'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':correo'    => $data['correo'] ?? null,
            ':estado'    => $data['estado'] ?? 'activo',
        ]);

        return (int)DB::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['nombre', 'cedula', 'telefono', 'direccion', 'correo', 'estado'] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[]       = "$f = :$f";
                $params[":$f"]  = $data[$f];
            }
        }

        if (empty($fields)) {
            return;
        }

        $sql = 'UPDATE clientes SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM clientes WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
