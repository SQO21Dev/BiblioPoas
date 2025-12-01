<?php 
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Libro;
use App\Models\Categoria;

class LibrosController extends Controller
{
    /**
     * Listado principal de libros.
     * Carga libros, KPIs y categorías (para el <select> del modal).
     */
    public function index(): void
    {
        $this->requireAuth();

        // Libros (incluye categoria_nombre si el modelo la trae via JOIN)
        $libros = Libro::all();

        // KPIs (totales, disponibles, prestados)
        $stats  = Libro::stats();

        // Categorías para el dropdown del modal
        $categorias = Categoria::all();

        $this->render('libros/index', compact('libros', 'stats', 'categorias'));
    }

    /**
     * Crear nuevo libro.
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/libros');

        $titulo      = trim($this->input('titulo'));
        $volumen     = trim($this->input('volumen'));
        $isbn        = trim($this->input('isbn'));
        $dewey       = trim($this->input('clasificacion_dewey', $this->input('dewey')));
        $autor       = trim($this->input('autor'));
        $anio        = (int)$this->input('anio', 0);
        $etiquetas   = trim($this->input('etiquetas'));
        $cantidad    = (int)$this->input('cantidad', 0);
        $estadoIn    = strtolower(trim($this->input('estado')));
        $categoriaId = (int)$this->input('categoria_id', 0); // opcional

        if (
            $titulo === '' ||
            $autor === '' ||
            $cantidad < 1 ||
            !in_array($estadoIn, ['disponible', 'prestado'], true)
        ) {
            $this->flashError('Datos inválidos. Verifica título, autor, cantidad y estado.');
            $this->redirect('/libros');
        }

        // Año opcional, pero si viene debe estar en rango razonable
        $anioDb = null;
        if ($anio > 0) {
            if ($anio < 1000 || $anio > 9999) {
                $this->flashError('El año de publicación es inválido.');
                $this->redirect('/libros');
            }
            $anioDb = $anio;
        }

        // Mapear al ENUM de la BD
        $estadoDb = $estadoIn === 'prestado' ? 'Prestado' : 'Disponible';

        // Categoría opcional
        $categoriaDb = $categoriaId > 0 ? $categoriaId : null;

        Libro::create([
            'titulo'              => $titulo,
            'volumen'             => $volumen !== '' ? $volumen : null,
            'isbn'                => $isbn !== '' ? $isbn : null,
            'clasificacion_dewey' => $dewey !== '' ? $dewey : null,
            'autor'               => $autor,
            'anio_publicacion'    => $anioDb,
            'categoria_id'        => $categoriaDb,
            'etiquetas'           => $etiquetas !== '' ? $etiquetas : null,
            'cantidad'            => $cantidad,
            'estado'              => $estadoDb,
        ]);

        $this->flashSuccess('Libro creado correctamente.');
        $this->redirect('/libros');
    }

    /**
     * Actualizar libro existente.
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/libros');

        $id          = (int)$this->input('id', $this->input('bookId'));
        $titulo      = trim($this->input('titulo'));
        $volumen     = trim($this->input('volumen'));
        $isbn        = trim($this->input('isbn'));
        $dewey       = trim($this->input('clasificacion_dewey', $this->input('dewey')));
        $autor       = trim($this->input('autor'));
        $anio        = (int)$this->input('anio', 0);
        $etiquetas   = trim($this->input('etiquetas'));
        $cantidad    = (int)$this->input('cantidad', 0);
        $estadoIn    = strtolower(trim($this->input('estado')));
        $categoriaId = (int)$this->input('categoria_id', 0);

        if ($id <= 0) {
            $this->flashError('ID inválido.');
            $this->redirect('/libros');
        }

        if (
            $titulo === '' ||
            $autor === '' ||
            $cantidad < 1 ||
            !in_array($estadoIn, ['disponible', 'prestado'], true)
        ) {
            $this->flashError('Datos inválidos. Verifica título, autor, cantidad y estado.');
            $this->redirect('/libros');
        }

        $anioDb = null;
        if ($anio > 0) {
            if ($anio < 1000 || $anio > 9999) {
                $this->flashError('El año de publicación es inválido.');
                $this->redirect('/libros');
            }
            $anioDb = $anio;
        }

        $estadoDb    = $estadoIn === 'prestado' ? 'Prestado' : 'Disponible';
        $categoriaDb = $categoriaId > 0 ? $categoriaId : null;

        Libro::update($id, [
            'titulo'              => $titulo,
            'volumen'             => $volumen !== '' ? $volumen : null,
            'isbn'                => $isbn !== '' ? $isbn : null,
            'clasificacion_dewey' => $dewey !== '' ? $dewey : null,
            'autor'               => $autor,
            'anio_publicacion'    => $anioDb,
            'categoria_id'        => $categoriaDb,
            'etiquetas'           => $etiquetas !== '' ? $etiquetas : null,
            'cantidad'            => $cantidad,
            'estado'              => $estadoDb,
        ]);

        $this->flashSuccess('Libro actualizado correctamente.');
        $this->redirect('/libros');
    }

    /**
     * Eliminar libro.
     */
    public function delete(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/libros');

        $id = (int)$this->input('id');
        if ($id <= 0) {
            $this->flashError('ID requerido.');
            $this->redirect('/libros');
        }

        Libro::delete($id);

        $this->flashSuccess('Libro eliminado.');
        $this->redirect('/libros');
    }

    /**
     * Exportar libros a CSV.
     */
    public function exportCsv(): void
    {
        $this->requireAuth();

        $rows = Libro::allForExport();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=libros.csv');

        $out = fopen('php://output', 'w');

        // Encabezados
        fputcsv($out, [
            'ID',
            'Título',
            'Volumen',
            'ISBN',
            'Clasificación DEWEY',
            'Autor',
            'Año',
            'Cantidad',
            'Estado',
            'Etiquetas',
            'Creado en',
            'Modificado en',
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['titulo'] ?? '',
                $r['volumen'] ?? '',
                $r['isbn'] ?? '',
                $r['clasificacion_dewey'] ?? '',
                $r['autor'] ?? '',
                $r['anio_publicacion'] ?? '',
                $r['cantidad'] ?? '',
                $r['estado'] ?? '',
                $r['etiquetas'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Exportar libros a un “Excel” sencillo (CSV con extensión .xlsx).
     */
    public function exportXlsx(): void
    {
        $this->requireAuth();

        $rows = Libro::allForExport();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=libros.xlsx');

        $out = fopen('php://output', 'w');

        fputcsv($out, [
            'ID',
            'Título',
            'Volumen',
            'ISBN',
            'Clasificación DEWEY',
            'Autor',
            'Año',
            'Cantidad',
            'Estado',
            'Etiquetas',
            'Creado en',
            'Modificado en',
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['titulo'] ?? '',
                $r['volumen'] ?? '',
                $r['isbn'] ?? '',
                $r['clasificacion_dewey'] ?? '',
                $r['autor'] ?? '',
                $r['anio_publicacion'] ?? '',
                $r['cantidad'] ?? '',
                $r['estado'] ?? '',
                $r['etiquetas'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }
}
