<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Log;

class ClientesController extends Controller
{
    /**
     * Listado principal de clientes.
     */
    public function index(): void
    {
        // Cualquier usuario logueado puede ver clientes
        $this->requireAuth();

        $clientes = Cliente::all();
        $stats    = Cliente::stats();

        $this->render('clientes/index', compact('clientes', 'stats'));
    }

    /**
     * Crear nuevo cliente (POST /clientes/create)
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/clientes');

        $nombre    = trim($this->input('nombre', $this->input('clienteNombre')));
        $cedula    = trim($this->input('cedula', $this->input('clienteCedula')));
        $telefono  = trim($this->input('telefono', $this->input('clienteTelefono')));
        $correo    = trim($this->input('correo', $this->input('clienteCorreo')));
        $direccion = trim($this->input('direccion', $this->input('clienteDireccion')));
        $estado    = trim($this->input('estado', $this->input('clienteEstado')));

        // Validaciones básicas
        if (
            $nombre === '' ||
            $cedula === '' ||
            $telefono === '' ||
            $direccion === '' ||
            !in_array($estado, ['activo', 'inactivo'], true) ||
            ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL))
        ) {
            $this->flashError('Campos obligatorios faltantes o correo inválido.');
            $this->redirect('/clientes');
        }

        // Validación de longitud según tu tabla
        if (strlen($cedula) > 10) {
            $this->flashError('La cédula excede el máximo permitido (10 caracteres).');
            $this->redirect('/clientes');
        }
        if (strlen($telefono) > 30) {
            $this->flashError('El teléfono excede el máximo permitido (30 caracteres).');
            $this->redirect('/clientes');
        }

        // Evitar duplicados lógicos (opcional)
        if (Cliente::existsCedula($cedula)) {
            $this->flashError('Ya existe un cliente con esa cédula.');
            $this->redirect('/clientes');
        }
        if ($correo !== '' && Cliente::existsCorreo($correo)) {
            $this->flashError('Ya existe un cliente con ese correo.');
            $this->redirect('/clientes');
        }

        // Crear cliente y obtener ID
        $clienteId = Cliente::create([
            'nombre'    => $nombre,
            'cedula'    => $cedula,
            'telefono'  => $telefono,
            'direccion' => $direccion,
            'correo'    => $correo !== '' ? $correo : null,
            'estado'    => $estado,
        ]);

        // Datos del usuario que realiza la acción
        $usuarioActor = $this->getUsuarioActor();
        $rol          = $this->getRolActor();

        // Registrar log de creación
        Log::registrar(
            $usuarioActor,
            $rol,
            'crear',           // acción
            'cliente',         // entidad
            "Alta de cliente ID {$clienteId} – {$nombre}",
            'ok'
        );

        $this->flashSuccess('Cliente creado correctamente.');
        $this->redirect('/clientes');
    }

    /**
     * Actualizar cliente existente (POST /clientes/update)
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/clientes');

        $id        = (int)$this->input('id', $this->input('clientId'));
        $nombre    = trim($this->input('nombre', $this->input('clienteNombre')));
        $cedula    = trim($this->input('cedula', $this->input('clienteCedula')));
        $telefono  = trim($this->input('telefono', $this->input('clienteTelefono')));
        $correo    = trim($this->input('correo', $this->input('clienteCorreo')));
        $direccion = trim($this->input('direccion', $this->input('clienteDireccion')));
        $estado    = trim($this->input('estado', $this->input('clienteEstado')));

        if ($id <= 0) {
            $this->flashError('ID inválido.');
            $this->redirect('/clientes');
        }

        if (
            $nombre === '' ||
            $cedula === '' ||
            $telefono === '' ||
            $direccion === '' ||
            !in_array($estado, ['activo', 'inactivo'], true) ||
            ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL))
        ) {
            $this->flashError('Campos obligatorios faltantes o correo inválido.');
            $this->redirect('/clientes');
        }

        if (strlen($cedula) > 10) {
            $this->flashError('La cédula excede el máximo permitido (10 caracteres).');
            $this->redirect('/clientes');
        }
        if (strlen($telefono) > 30) {
            $this->flashError('El teléfono excede el máximo permitido (30 caracteres).');
            $this->redirect('/clientes');
        }

        if (Cliente::existsCedula($cedula, $id)) {
            $this->flashError('Ya existe otro cliente con esa cédula.');
            $this->redirect('/clientes');
        }
        if ($correo !== '' && Cliente::existsCorreo($correo, $id)) {
            $this->flashError('Ya existe otro cliente con ese correo.');
            $this->redirect('/clientes');
        }

        Cliente::update($id, [
            'nombre'    => $nombre,
            'cedula'    => $cedula,
            'telefono'  => $telefono,
            'direccion' => $direccion,
            'correo'    => $correo !== '' ? $correo : null,
            'estado'    => $estado,
        ]);

        // Datos del usuario que realiza la acción
        $usuarioActor = $this->getUsuarioActor();
        $rol          = $this->getRolActor();

        // Registrar log de edición
        Log::registrar(
            $usuarioActor,
            $rol,
            'editar',          // acción
            'cliente',         // entidad
            "Actualiza cliente ID {$id} – {$nombre}",
            'ok'
        );

        $this->flashSuccess('Cliente actualizado correctamente.');
        $this->redirect('/clientes');
    }

    /**
     * Eliminar cliente (POST /clientes/delete)
     */
    public function delete(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/clientes');

        $id = (int)$this->input('id');
        if ($id <= 0) {
            $this->flashError('ID requerido.');
            $this->redirect('/clientes');
        }

        Cliente::delete($id);

        // Datos del usuario que realiza la acción
        $usuarioActor = $this->getUsuarioActor();
        $rol          = $this->getRolActor();

        // Registrar log de eliminación
        Log::registrar(
            $usuarioActor,
            $rol,
            'eliminar',        // acción
            'cliente',         // entidad
            "Elimina cliente ID {$id}",
            'ok'
        );

        $this->flashSuccess('Cliente eliminado.');
        $this->redirect('/clientes');
    }

    /* ==========================
       Helpers privados para logs
       ========================== */

    /**
     * Obtiene el nombre/usuario que se usará en los logs.
     */
    private function getUsuarioActor(): string
    {
        return (string)(
            $_SESSION['usuario_nombre']
            ?? $_SESSION['user_name']
            ?? $_SESSION['usuario']
            ?? $_SESSION['user_email']
            ?? 'sistema'
        );
    }

    /**
     * Obtiene el rol que se usará en los logs.
     */
    private function getRolActor(): string
    {
        return (string)(
            $_SESSION['usuario_rol']
            ?? $_SESSION['user_role']
            ?? $_SESSION['rol']
            ?? 'sin_rol'
        );
    }
}
