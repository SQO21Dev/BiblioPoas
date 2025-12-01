<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class UsuariosController extends Controller
{
    /**
     * Listado de usuarios + KPIs
     */
    public function index(): void
    {
        $this->requireRole(['admin']);

        $usuarios = Usuario::all();
        $stats    = Usuario::stats();

        $this->render('usuarios/index', compact('usuarios', 'stats'));
    }

    /**
     * Crear nuevo usuario
     * Ruta: POST /usuarios/create
     */
    public function create(): void
    {
        $this->requireRole(['admin']);
        $this->checkCsrf($_POST['_csrf'] ?? '', '/usuarios');

        $usuario = $this->input('usuario');
        $nombre  = $this->input('nombre');
        $correo  = $this->input('correo');
        $pass    = (string)$this->input('contrasena');
        $rol     = $this->input('rol');
        $estado  = $this->input('estado');

        if (
            $usuario === '' ||
            $nombre  === '' ||
            !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            strlen($pass) < 6 ||
            !in_array($rol, ['admin', 'empleado'], true) ||
            !in_array($estado, ['activo', 'inactivo'], true)
        ) {
            $this->flashError('Datos inválidos.');
            $this->redirect('/usuarios');
        }

        if (Usuario::existsUsuario($usuario)) {
            $this->flashError('El nombre de usuario ya existe.');
            $this->redirect('/usuarios');
        }

        if (Usuario::existsCorreo($correo)) {
            $this->flashError('El correo ya existe.');
            $this->redirect('/usuarios');
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        Usuario::create([
            'usuario'       => $usuario,
            'nombre'        => $nombre,
            'correo'        => $correo,
            'password_hash' => $hash,
            'rol'           => $rol,
            'estado'        => $estado,
        ]);

        $this->flashSuccess('Usuario creado correctamente.');
        $this->redirect('/usuarios');
    }

    /**
     * Actualizar usuario
     * Ruta: POST /usuarios/update
     */
    public function update(): void
    {
        $this->requireRole(['admin']);
        $this->checkCsrf($_POST['_csrf'] ?? '', '/usuarios');

        $id      = (int)$this->input('id');
        $usuario = $this->input('usuario');
        $nombre  = $this->input('nombre');
        $correo  = $this->input('correo');
        $rol     = $this->input('rol');
        $estado  = $this->input('estado');
        $pass    = (string)$this->input('contrasena'); // opcional

        if ($id <= 0) {
            $this->flashError('ID inválido.');
            $this->redirect('/usuarios');
        }

        if (
            $usuario === '' ||
            $nombre  === '' ||
            !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            !in_array($rol, ['admin', 'empleado'], true) ||
            !in_array($estado, ['activo', 'inactivo'], true)
        ) {
            $this->flashError('Datos inválidos.');
            $this->redirect('/usuarios');
        }

        if (Usuario::existsUsuario($usuario, $id)) {
            $this->flashError('El nombre de usuario ya se encuentra en uso.');
            $this->redirect('/usuarios');
        }

        if (Usuario::existsCorreo($correo, $id)) {
            $this->flashError('El correo ya se encuentra en uso.');
            $this->redirect('/usuarios');
        }

        $data = [
            'usuario' => $usuario,
            'nombre'  => $nombre,
            'correo'  => $correo,
            'rol'     => $rol,
            'estado'  => $estado,
        ];

        if ($pass !== '') {
            if (strlen($pass) < 6) {
                $this->flashError('La nueva contraseña es demasiado corta (mínimo 6).');
                $this->redirect('/usuarios');
            }
            $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        Usuario::update($id, $data);

        $this->flashSuccess('Usuario actualizado correctamente.');
        $this->redirect('/usuarios');
    }

    /**
     * Eliminar usuario
     * Ruta: POST /usuarios/delete
     */
    public function delete(): void
    {
        $this->requireRole(['admin']);
        $this->checkCsrf($_POST['_csrf'] ?? '', '/usuarios');

        $id = (int)$this->input('id');
        if ($id <= 0) {
            $this->flashError('ID requerido.');
            $this->redirect('/usuarios');
        }

        // (Opcional) evitar que el mismo admin se borre a sí mismo
        if (!empty($_SESSION['user']['id']) && (int)$_SESSION['user']['id'] === $id) {
            $this->flashError('No puedes eliminar tu propio usuario.');
            $this->redirect('/usuarios');
        }

        Usuario::delete($id);

        $this->flashSuccess('Usuario eliminado.');
        $this->redirect('/usuarios');
    }

    /**
     * Exportar usuarios a CSV
     * Ruta: GET /usuarios/export/csv
     */
    public function exportCsv(): void
    {
        $this->requireRole(['admin']);

        $rows = Usuario::all();
        $filename = 'usuarios_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');

        // Cabeceras
        fputcsv($out, ['ID', 'Usuario', 'Nombre', 'Correo', 'Rol', 'Estado', 'Creado en'], ';');

        foreach ($rows as $u) {
            fputcsv($out, [
                $u['id'],
                $u['usuario'],
                $u['nombre'],
                $u['correo'],
                $u['rol'],
                $u['estado'],
                $u['creado_en'],
            ], ';');
        }

        fclose($out);
        exit;
    }

    /**
     * Exportar usuarios en formato que abre Excel
     * Ruta: GET /usuarios/export/xlsx
     */
    public function exportXlsx(): void
    {
        $this->requireRole(['admin']);

        $rows = Usuario::all();
        $filename = 'usuarios_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $cols = ['ID', 'Usuario', 'Nombre', 'Correo', 'Rol', 'Estado', 'Creado en'];

        echo implode("\t", $cols) . "\r\n";

        foreach ($rows as $u) {
            $line = [
                $u['id'],
                $u['usuario'],
                $u['nombre'],
                $u['correo'],
                $u['rol'],
                $u['estado'],
                $u['creado_en'],
            ];
            echo implode("\t", $line) . "\r\n";
        }

        exit;
    }
}
