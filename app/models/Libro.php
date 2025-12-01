<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

final class Libro
{
    /* ==========================
       Lecturas / Listados
       ========================== */

    /** Lista de libros para la tabla */
    public static function all(): array
    {
        $sql = "SELECT
                    id,
                    titulo,
                    volumen,
                    isbn,
                    clasificacion_dewey,
                    autor,
                    anio_publicacion,
                    categoria_id,
                    etiquetas,
                    cantidad,
                    estado,
                    creado_en,
                    modificado_en
                FROM libros
                ORDER BY creado_en DESC";

        return DB::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** KPIs para la vista */
    public static function stats(): array
    {
        $pdo = DB::pdo();

        $total      = (int)$pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
        $disponible = (int)$pdo->query("SELECT COUNT(*) FROM libros WHERE estado = 'Disponible'")->fetchColumn();
        $prestado   = (int)$pdo->query("SELECT COUNT(*) FROM libros WHERE estado = 'Prestado'")->fetchColumn();

        return [
            'total'      => $total,
            'disponible' => $disponible,
            'prestado'   => $prestado,
        ];
    }

    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare(
            "SELECT
                id,
                titulo,
                volumen,
                isbn,
                clasificacion_dewey,
                autor,
                anio_publicacion,
                categoria_id,
                etiquetas,
                cantidad,
                estado,
                creado_en,
                modificado_en
             FROM libros
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ==========================
       Escrituras
       ========================== */

    public static function create(array $data): int
    {
        $stmt = DB::pdo()->prepare(
            "INSERT INTO libros (
                titulo,
                volumen,
                isbn,
                clasificacion_dewey,
                autor,
                anio_publicacion,
                categoria_id,
                etiquetas,
                cantidad,
                estado
            ) VALUES (
                :titulo,
                :volumen,
                :isbn,
                :clasificacion_dewey,
                :autor,
                :anio_publicacion,
                :categoria_id,
                :etiquetas,
                :cantidad,
                :estado
            )"
        );

        $stmt->execute([
            ':titulo'            => $data['titulo'],
            ':volumen'           => $data['volumen'] ?? null,
            ':isbn'              => $data['isbn'] ?? null,
            ':clasificacion_dewey' => $data['clasificacion_dewey'] ?? null,
            ':autor'             => $data['autor'] ?? null,
            ':anio_publicacion'  => $data['anio_publicacion'] ?? null,
            ':categoria_id'      => $data['categoria_id'] ?? null,
            ':etiquetas'         => $data['etiquetas'] ?? null,
            ':cantidad'          => $data['cantidad'],
            ':estado'            => $data['estado'],  // 'Disponible' | 'Prestado'
        ]);

        return (int)DB::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ([
            'titulo',
            'volumen',
            'isbn',
            'clasificacion_dewey',
            'autor',
            'anio_publicacion',
            'categoria_id',
            'etiquetas',
            'cantidad',
            'estado',
        ] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[]      = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }

        if (empty($fields)) {
            return;
        }

        $sql = 'UPDATE libros SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM libros WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /* ==========================
       Export
       ========================== */

    public static function allForExport(): array
    {
        // Mismo que all(), pero puedes ajustar columnas si quieres
        return self::all();
    }
}
