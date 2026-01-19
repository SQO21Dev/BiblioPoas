<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tiquete;

class TiquetesController extends Controller
{
    /**
     * Listado principal de tiquetes.
     */
    public function index(): void
    {
        $this->requireAuth();

        /**
         * OPCION 1 (lazy update):
         * Cada vez que se carga /tiquetes, sincronizamos los vencidos:
         * - Si está "En Prestamo" y fecha_devolucion ya pasó => "Atrasado"
         *
         * Nota: Este método debe existir en el modelo Tiquete.
         */
        Tiquete::marcarAtrasados();

        $tiquetes = Tiquete::all();
        $stats    = Tiquete::stats();

        // Listas para los autocompletes del modal
        $libros   = Tiquete::disponiblesForSelect();
        $clientes = Tiquete::clientesForSelect();

        $this->render('tiquetes/index', compact('tiquetes', 'stats', 'libros', 'clientes'));
    }

    /**
     * Crear nuevo tiquete (préstamo).
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/tiquetes');

        // Campos del formulario
        $nombreCliente = trim($this->input('cliente'));
        $clienteId     = (int)$this->input('cliente_id', 0);

        // Datos de contacto
        $telefono      = trim($this->input('telefono'));
        $direccion     = trim($this->input('direccion'));

        // Libro
        $tituloLibro   = trim($this->input('libro'));
        $libroId       = (int)$this->input('libro_id', 0);
        $autor         = trim($this->input('autor'));

        $estadoIn      = trim($this->input('estado')) ?: 'En Prestamo';
        $categoriaEdad = trim($this->input('categoria_edad'));
        $f1            = trim($this->input('fecha_prestamo'));
        $f2            = trim($this->input('fecha_devolucion'));
        $observaciones = trim($this->input('observaciones'));

        // Si viene solo el ID del libro (por ejemplo desde un <select>), obtenemos el título desde la BD
        if ($tituloLibro === '' && $libroId > 0) {
            $libroRow    = Tiquete::findLibroById($libroId);
            $tituloLibro = $libroRow['titulo'] ?? '';
            if ($autor === '' && !empty($libroRow['autor'])) {
                $autor = $libroRow['autor'];
            }
        }

        if (
            $nombreCliente === '' ||
            $tituloLibro   === '' ||
            $libroId <= 0 ||
            $f1 === '' || $f2 === '' ||
            $estadoIn === '' ||
            $categoriaEdad === ''
        ) {
            $this->flashError('Completa los campos requeridos (cliente, libro, fechas, estado y categoría de edad).');
            $this->redirect('/tiquetes');
        }

        // Normalizar estado: debe coincidir con ENUM de la BD
        $estadosValidos = ['En Prestamo', 'Devuelto', 'Atrasado'];
        if (!in_array($estadoIn, $estadosValidos, true)) {
            $this->flashError('Estado inválido.');
            $this->redirect('/tiquetes');
        }

        // Categorías de edad válidas
        $edadesValidas = [
            'OP', 'AP',   // 0–5
            'O',  'A',    // 6–12
            'HJ', 'MJ',   // 13–17
            'HJU','MJU',  // 18–35
            'HA', 'MA',   // 36–64
            'HAM','NAM',  // 65+
        ];
        if (!in_array($categoriaEdad, $edadesValidas, true)) {
            $this->flashError('Categoría de edad inválida.');
            $this->redirect('/tiquetes');
        }

        // Verificar disponibilidad del libro:
        if (Tiquete::libroEnPrestamo($libroId)) {
            http_response_code(409);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => 'error',
                'message' => 'El libro seleccionado ya tiene un préstamo activo y no está disponible.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Formato de fechas desde <input type="datetime-local">: 2025-10-10T09:00
        $fechaPrestamo   = $this->normalizeDatetimeLocal($f1);
        $fechaDevolucion = $this->normalizeDatetimeLocal($f2);

        if ($fechaPrestamo === null || $fechaDevolucion === null) {
            $this->flashError('Formato de fecha/hora inválido.');
            $this->redirect('/tiquetes');
        }

        // Usuario que registra
        $usuarioId = (int)($_SESSION['user_id'] ?? 1);

        // Crear tiquete
        Tiquete::create([
            'cliente_id'          => $clienteId > 0 ? $clienteId : null,
            'nombre_cliente'      => $nombreCliente,
            'telefono'            => $telefono !== '' ? $telefono : null,
            'direccion'           => $direccion !== '' ? $direccion : null,
            'libro_id'            => $libroId,
            'titulo'              => $tituloLibro,
            'autor'               => $autor !== '' ? $autor : null,
            'signatura'           => null,
            'categoria_edad'      => $categoriaEdad,
            'estado'              => $estadoIn,
            'fecha_prestamo'      => $fechaPrestamo,
            'fecha_devolucion'    => $fechaDevolucion,
            'usuario_registra_id' => $usuarioId,
            'observaciones'       => $observaciones !== '' ? $observaciones : null,
            'nombre_biblioteca'   => 'Biblioteca Pública Semioficial de San Rafael de San Rafael de Poás',
        ]);

        // Marcar el libro como Prestado
        if ($estadoIn === 'En Prestamo') {
            Tiquete::marcarLibroPrestado($libroId);
        }

        $this->flashSuccess('Tiquete creado correctamente.');
        $this->redirect('/dashboard');
    }

    /**
     * Actualizar un tiquete existente.
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/tiquetes');

        $id = (int)$this->input('id', $this->input('ticketId'));

        if ($id <= 0) {
            $this->flashError('ID inválido.');
            $this->redirect('/tiquetes');
        }

        // Traemos el tiquete anterior para comparar estado/libro
        $tiqueteAnterior = Tiquete::findById($id);
        if (!$tiqueteAnterior) {
            $this->flashError('Tiquete no encontrado.');
            $this->redirect('/tiquetes');
        }

        $nombreCliente = trim($this->input('cliente'));
        $clienteId     = (int)$this->input('cliente_id', 0);

        // Datos de contacto
        $telefono      = trim($this->input('telefono'));
        $direccion     = trim($this->input('direccion'));

        $tituloLibro   = trim($this->input('libro'));
        $libroId       = (int)$this->input('libro_id', 0);
        $autor         = trim($this->input('autor'));
        $estadoIn      = trim($this->input('estado')) ?: 'En Prestamo';
        $categoriaEdad = trim($this->input('categoria_edad'));
        $f1            = trim($this->input('fecha_prestamo'));
        $f2            = trim($this->input('fecha_devolucion'));
        $observaciones = trim($this->input('observaciones'));

        // Completar título desde la BD si viene solo el ID
        if ($tituloLibro === '' && $libroId > 0) {
            $libroRow    = Tiquete::findLibroById($libroId);
            $tituloLibro = $libroRow['titulo'] ?? '';
            if ($autor === '' && !empty($libroRow['autor'])) {
                $autor = $libroRow['autor'];
            }
        }

        if (
            $nombreCliente === '' ||
            $tituloLibro   === '' ||
            $libroId <= 0 ||
            $f1 === '' || $f2 === '' ||
            $estadoIn === '' ||
            $categoriaEdad === ''
        ) {
            $this->flashError('Datos inválidos. Verifica cliente, libro, fechas, estado y categoría de edad.');
            $this->redirect('/tiquetes');
        }

        $estadosValidos = ['En Prestamo', 'Devuelto', 'Retrasado'];
        if (!in_array($estadoIn, $estadosValidos, true)) {
            $this->flashError('Estado inválido.');
            $this->redirect('/tiquetes');
        }

        $edadesValidas = [
            'OP', 'AP',
            'O',  'A',
            'HJ', 'MJ',
            'HJU','MJU',
            'HA', 'MA',
            'HAM','NAM',
        ];
        if (!in_array($categoriaEdad, $edadesValidas, true)) {
            $this->flashError('Categoría de edad inválida.');
            $this->redirect('/tiquetes');
        }

        $fechaPrestamo   = $this->normalizeDatetimeLocal($f1);
        $fechaDevolucion = $this->normalizeDatetimeLocal($f2);

        if ($fechaPrestamo === null || $fechaDevolucion === null) {
            $this->flashError('Formato de fecha/hora inválido.');
            $this->redirect('/tiquetes');
        }

        // Si queda en "En Prestamo", validamos que no haya OTRO tiquete activo con ese libro
        if ($estadoIn === 'En Prestamo') {
            if (Tiquete::libroEnPrestamo($libroId, $id)) {
                http_response_code(409);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'El libro seleccionado ya tiene un préstamo activo y no está disponible.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        // Actualizamos el tiquete
        Tiquete::update($id, [
            'cliente_id'       => $clienteId > 0 ? $clienteId : null,
            'nombre_cliente'   => $nombreCliente,
            'telefono'         => $telefono !== '' ? $telefono : null,
            'direccion'        => $direccion !== '' ? $direccion : null,
            'libro_id'         => $libroId,
            'titulo'           => $tituloLibro,
            'autor'            => $autor !== '' ? $autor : null,
            'estado'           => $estadoIn,
            'categoria_edad'   => $categoriaEdad,
            'fecha_prestamo'   => $fechaPrestamo,
            'fecha_devolucion' => $fechaDevolucion,
            'observaciones'    => $observaciones !== '' ? $observaciones : null,
            'nombre_biblioteca'=> $tiqueteAnterior['nombre_biblioteca'] ?? null,
        ]);

        // Sincronizar estado del libro según el estado del tiquete
        if ($estadoIn === 'Devuelto') {
            Tiquete::marcarLibroDisponible($libroId);
        } elseif ($estadoIn === 'En Prestamo') {
            Tiquete::marcarLibroPrestado($libroId);
        }
        // Si está "Retrasado" no tocamos libros (sigue prestado)

        $this->flashSuccess('Tiquete actualizado correctamente.');
        $this->redirect('/tiquetes');
    }

    /**
     * Actualización rápida desde el Dashboard (modal).
     * Permite cambiar fecha de vencimiento y opcionalmente cerrar el tiquete.
     * Responde en JSON.
     */
    public function dashboardUpdate(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/dashboard');

        $id            = (int)($this->input('id') ?? 0);
        $accion        = trim((string)$this->input('accion'));
        $fechaDevInput = trim((string)$this->input('fecha_devolucion'));

        header('Content-Type: application/json; charset=utf-8');

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'ID de tiquete inválido.']);
            exit;
        }

        if ($fechaDevInput === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Debes indicar la fecha de vencimiento.']);
            exit;
        }

        $cerrar = ($accion === 'cerrar');

        try {
            $ok = Tiquete::quickUpdateDesdeDashboard(
                $id,
                $fechaDevInput,
                $cerrar
            );

            if (!$ok) {
                throw new \RuntimeException('No se pudo actualizar el tiquete.');
            }

            $msg = $cerrar
                ? 'Tiquete cerrado correctamente.'
                : 'Fecha de vencimiento actualizada.';

            echo json_encode(['ok' => true, 'message' => $msg]);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode([
                'ok'      => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Eliminar tiquete.
     */
    public function delete(): void
    {
        $this->requireAuth();
        $this->checkCsrf($_POST['_csrf'] ?? '', '/tiquetes');

        $id = (int)$this->input('id');
        if ($id <= 0) {
            $this->flashError('ID requerido.');
            $this->redirect('/tiquetes');
        }

        Tiquete::delete($id);

        $this->flashSuccess('Tiquete eliminado.');
        $this->redirect('/tiquetes');
    }

    /**
     * Exportar CSV (respetando filtros de fecha opcionales: from / to).
     */
    public function exportCsv(): void
    {
        $this->requireAuth();

        $from = $this->normalizeDateFilter($this->input('from'));
        $to   = $this->normalizeDateFilter($this->input('to'));

        $rows = Tiquete::allForExport($from, $to);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tiquetes.csv');

        $out = fopen('php://output', 'w');

        // Encabezados
        fputcsv($out, [
            'ID',
            'Código',
            'Nombre cliente',
            'Teléfono',
            'Dirección',
            'Título',
            'Autor',
            'Signatura',
            'Categoría edad',
            'Estado',
            'Fecha préstamo',
            'Fecha devolución',
            'Usuario registra',
            'Observaciones',
            'Nombre biblioteca',
            'Creado en',
            'Modificado en',
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['codigo'] ?? '',
                $r['nombre_cliente'] ?? '',
                $r['telefono'] ?? '',
                $r['direccion'] ?? '',
                $r['titulo'] ?? '',
                $r['autor'] ?? '',
                $r['signatura'] ?? '',
                $r['categoria_edad'] ?? '',
                $r['estado'] ?? '',
                $r['fecha_prestamo'] ?? '',
                $r['fecha_devolucion'] ?? '',
                $r['usuario_registra_id'] ?? '',
                $r['observaciones'] ?? '',
                $r['nombre_biblioteca'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Exportar “Excel” sencillo (CSV con extensión .xlsx).
     */
    public function exportXlsx(): void
    {
        $this->requireAuth();

        $from = $this->normalizeDateFilter($this->input('from'));
        $to   = $this->normalizeDateFilter($this->input('to'));

        $rows = Tiquete::allForExport($from, $to);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=tiquetes.xlsx');

        $out = fopen('php://output', 'w');

        fputcsv($out, [
            'ID',
            'Código',
            'Nombre cliente',
            'Teléfono',
            'Dirección',
            'Título',
            'Autor',
            'Signatura',
            'Categoría edad',
            'Estado',
            'Fecha préstamo',
            'Fecha devolución',
            'Usuario registra',
            'Observaciones',
            'Nombre biblioteca',
            'Creado en',
            'Modificado en',
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['codigo'] ?? '',
                $r['nombre_cliente'] ?? '',
                $r['telefono'] ?? '',
                $r['direccion'] ?? '',
                $r['titulo'] ?? '',
                $r['autor'] ?? '',
                $r['signatura'] ?? '',
                $r['categoria_edad'] ?? '',
                $r['estado'] ?? '',
                $r['fecha_prestamo'] ?? '',
                $r['fecha_devolucion'] ?? '',
                $r['usuario_registra_id'] ?? '',
                $r['observaciones'] ?? '',
                $r['nombre_biblioteca'] ?? '',
                $r['creado_en'] ?? '',
                $r['modificado_en'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    /* ==========================
       Helpers privados
       ========================== */

    /**
     * Convierte un valor de <input type="datetime-local">
     * (ej: 2025-10-10T09:00) a 'Y-m-d H:i:s'.
     */
    private function normalizeDatetimeLocal(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Reemplazar 'T' por espacio y asegurar segundos
        $value = str_replace('T', ' ', $value);
        if (strlen($value) === 16) { // 2025-10-10 09:00
            $value .= ':00';
        }

        // Validación rápida con strtotime
        $ts = strtotime($value);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $ts);
    }

    /**
     * Normaliza un filtro de fecha (YYYY-MM-DD). Si no cumple formato, devuelve null.
     */
    private function normalizeDateFilter(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt || $dt->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }
}
