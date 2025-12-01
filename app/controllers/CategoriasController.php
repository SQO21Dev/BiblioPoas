<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Models\Categoria;
use App\Models\Log;

class CategoriasController extends Controller
{
    /**
     * Listado principal de categorías.
     */
    public function index(): void
    {
        $this->requireAuth();

        $categorias = Categoria::all();
        $total      = count($categorias);
        $csrf       = CSRF::token();

        $this->render('categorias/index', compact('categorias', 'total', 'csrf'));
    }

    /**
     * Crear nueva categoría (POST /categorias/create)
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/categorias');

        $nombre = trim($this->input('nombre', $this->input('catNombre')));
        $desc   = trim($this->input('descripcion', $this->input('catDesc')));

        if ($nombre === '') {
            $this->flashError('El nombre de la categoría es obligatorio.');
            $this->redirect('/categorias');
        }

        if (strlen($nombre) > 120) {
            $this->flashError('El nombre excede el máximo permitido (120 caracteres).');
            $this->redirect('/categorias');
        }

        if (strlen($desc) > 255) {
            $this->flashError('La descripción excede el máximo permitido (255 caracteres).');
            $this->redirect('/categorias');
        }

        // Evitar duplicados por nombre
        if (Categoria::existsNombre($nombre)) {
            $this->flashError('Ya existe una categoría con ese nombre.');
            $this->redirect('/categorias');
        }

        $catId = Categoria::create([
            'nombre'      => $nombre,
            'descripcion' => $desc !== '' ? $desc : null,
        ]);

        // Datos del usuario que realiza la acción (usando misma lógica que Clientes / Logs)
        $usuarioActor = (string)(
            $_SESSION['usuario_nombre']
            ?? $_SESSION['user_name']
            ?? $_SESSION['usuario']
            ?? $_SESSION['user_email']
            ?? 'sistema'
        );

        $rol = (string)(
            $_SESSION['usuario_rol']
            ?? $_SESSION['user_role']
            ?? $_SESSION['rol']
            ?? 'sin_rol'
        );

        // Registrar log
        Log::registrar(
            $usuarioActor,
            $rol,
            'crear',           // acción
            'categoria',       // entidad
            "Crea categoría ID {$catId} – {$nombre}",
            'ok'
        );

        $this->flashSuccess('Categoría creada correctamente.');
        $this->redirect('/categorias');
    }

    /**
     * Actualizar categoría existente (POST /categorias/update)
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/categorias');

        $id     = (int)$this->input('id', $this->input('catId'));
        $nombre = trim($this->input('nombre', $this->input('catNombre')));
        $desc   = trim($this->input('descripcion', $this->input('catDesc')));

        if ($id <= 0) {
            $this->flashError('ID inválido.');
            $this->redirect('/categorias');
        }

        if ($nombre === '') {
            $this->flashError('El nombre de la categoría es obligatorio.');
            $this->redirect('/categorias');
        }

        if (strlen($nombre) > 120) {
            $this->flashError('El nombre excede el máximo permitido (120 caracteres).');
            $this->redirect('/categorias');
        }

        if (strlen($desc) > 255) {
            $this->flashError('La descripción excede el máximo permitido (255 caracteres).');
            $this->redirect('/categorias');
        }

        // Evitar duplicados (ignorando la propia categoría)
        if (Categoria::existsNombre($nombre, $id)) {
            $this->flashError('Ya existe otra categoría con ese nombre.');
            $this->redirect('/categorias');
        }

        Categoria::update($id, [
            'nombre'      => $nombre,
            'descripcion' => $desc !== '' ? $desc : null,
        ]);

        $usuarioActor = (string)(
            $_SESSION['usuario_nombre']
            ?? $_SESSION['user_name']
            ?? $_SESSION['usuario']
            ?? $_SESSION['user_email']
            ?? 'sistema'
        );

        $rol = (string)(
            $_SESSION['usuario_rol']
            ?? $_SESSION['user_role']
            ?? $_SESSION['rol']
            ?? 'sin_rol'
        );

        Log::registrar(
            $usuarioActor,
            $rol,
            'editar',
            'categoria',
            "Actualiza categoría ID {$id} – {$nombre}",
            'ok'
        );

        $this->flashSuccess('Categoría actualizada correctamente.');
        $this->redirect('/categorias');
    }

    /**
     * Eliminar categoría (POST /categorias/delete)
     */
    public function delete(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/categorias');

        $id = (int)$this->input('id');
        if ($id <= 0) {
            $this->flashError('ID requerido.');
            $this->redirect('/categorias');
        }

        Categoria::delete($id);

        $usuarioActor = (string)(
            $_SESSION['usuario_nombre']
            ?? $_SESSION['user_name']
            ?? $_SESSION['usuario']
            ?? $_SESSION['user_email']
            ?? 'sistema'
        );

        $rol = (string)(
            $_SESSION['usuario_rol']
            ?? $_SESSION['user_role']
            ?? $_SESSION['rol']
            ?? 'sin_rol'
        );

        Log::registrar(
            $usuarioActor,
            $rol,
            'eliminar',
            'categoria',
            "Elimina categoría ID {$id}",
            'ok'
        );

        $this->flashSuccess('Categoría eliminada.');
        $this->redirect('/categorias');
    }

    /**
     * Exportar CSV.
     */
    public function exportCsv(): void
    {
        $this->requireAuth();

        $rows = Categoria::allForExport();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=categorias.csv');

        $out = fopen('php://output', 'w');

        fputcsv($out, ['ID', 'Nombre', 'Descripción', 'Creado en', 'Modificado en']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['nombre'] ?? '',
                $r['descripcion'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Exportar Excel simple (CSV con extensión .xlsx).
     */
    public function exportXlsx(): void
    {
        $this->requireAuth();

        $rows = Categoria::allForExport();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=categorias.xlsx');

        $out = fopen('php://output', 'w');

        fputcsv($out, ['ID', 'Nombre', 'Descripción', 'Creado en', 'Modificado en']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['nombre'] ?? '',
                $r['descripcion'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }
}
