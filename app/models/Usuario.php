<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

final class Usuario
{
    /* ==========================
       Lecturas / Listados
       ========================== */

    /** Lista para tabla (sin hashes) */
    public static function all(): array
    {
        $sql = "SELECT id, usuario, nombre, correo, rol, estado, creado_en
                  FROM usuarios
              ORDER BY creado_en DESC";
        return DB::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** KPIs básicos de usuarios */
    public static function stats(): array
    {
        $pdo = DB::pdo();

        $tot   = (int)$pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $admin = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'")->fetchColumn();
        $emp   = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'empleado'")->fetchColumn();
        $act   = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'")->fetchColumn();
        $ina   = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'inactivo'")->fetchColumn();

        return [
            'total'     => $tot,
            'admins'    => $admin,
            'empleados' => $emp,
            'activos'   => $act,
            'inactivos' => $ina,
        ];
    }

    /* ==========================
       Búsquedas
       ========================== */

    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findByUsuario(string $usuario): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM usuarios WHERE usuario = :u LIMIT 1');
        $stmt->execute([':u' => $usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findByCorreo(string $correo): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM usuarios WHERE correo = :c LIMIT 1');
        $stmt->execute([':c' => $correo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function existsUsuario(string $usuario, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM usuarios WHERE usuario = :u AND id <> :id'
            );
            $stmt->execute([':u' => $usuario, ':id' => $excludeId]);
        } else {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM usuarios WHERE usuario = :u'
            );
            $stmt->execute([':u' => $usuario]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existsCorreo(string $correo, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM usuarios WHERE correo = :c AND id <> :id'
            );
            $stmt->execute([':c' => $correo, ':id' => $excludeId]);
        } else {
            $stmt = DB::pdo()->prepare(
                'SELECT COUNT(*) FROM usuarios WHERE correo = :c'
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
            'INSERT INTO usuarios (usuario, nombre, correo, password_hash, rol, estado)
             VALUES (:usuario, :nombre, :correo, :password_hash, :rol, :estado)'
        );
        $stmt->execute([
            ':usuario'       => $data['usuario'],
            ':nombre'        => $data['nombre'],
            ':correo'        => $data['correo'],
            ':password_hash' => $data['password_hash'], // ya debe venir con password_hash(...)
            ':rol'           => $data['rol']    ?? 'empleado',
            ':estado'        => $data['estado'] ?? 'activo',
        ]);
        return (int)DB::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['usuario','nombre','correo','rol','estado'] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[]      = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }

        if (isset($data['password_hash']) && $data['password_hash'] !== '') {
            $fields[]                 = 'password_hash = :password_hash';
            $params[':password_hash'] = $data['password_hash'];
        }

        if (empty($fields)) {
            return;
        }

        $sql = 'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($params);
    }

    /** Actualizar sólo la contraseña (flujo reset) */
    public static function updatePassword(int $id, string $hash): void
    {
        $stmt = DB::pdo()->prepare(
            'UPDATE usuarios SET password_hash = :h WHERE id = :id'
        );
        $stmt->execute([
            ':h'  => $hash,
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /* ==========================
       Password helper
       ========================== */

    /** Verifica contra password_hash (solo contraseña normal). */
    public static function verifyPassword(array $user, string $password): bool
    {
        return !empty($user['password_hash']) && password_verify($password, $user['password_hash']);
    }
}
