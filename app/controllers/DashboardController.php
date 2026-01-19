<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Models\Dashboard as DashboardModel;
use App\Models\Tiquete;

class DashboardController extends Controller
{
    /**
     * Mapeo de códigos de categoría de edad a etiquetas legibles.
     */
    private const CATEGORIA_LABELS = [
        'OP'  => '0–5 años (Hombres)',
        'AP'  => '0–5 años (Mujeres)',
        'O'   => '6–12 años (Hombres)',
        'A'   => '6–12 años (Mujeres)',
        'HJ'  => '13–17 años (Hombres)',
        'MJ'  => '13–17 años (Mujeres)',
        'HJU' => '18–35 años (Hombres)',
        'MJU' => '18–35 años (Mujeres)',
        'HA'  => '36–64 años (Hombres)',
        'MA'  => '36–64 años (Mujeres)',
        'HAM' => '65+ años (Hombres)',
        'NAM' => '65+ años (Mujeres)',
    ];

    public function index(): void
    {
        $this->requireAuth();

        /**
         * OPCION 1 (lazy update):
         * Cada vez que se carga el Dashboard, sincronizamos los tiquetes vencidos:
         * - Si está "En Prestamo" y la fecha_devolucion ya pasó => "Atrasado"
         *
         * Nota: Este método debe existir en el modelo Tiquete.
         */
        Tiquete::marcarAtrasados();

        /**
         * IMPORTANTE:
         * El formulario del dashboard es method="get".
         * En muchos proyectos, $this->input() lee POST (o prioriza POST),
         * por eso los filtros "se limpian".
         */
        $fromRaw = $this->queryParam('from'); // viene como YYYY-MM-DD o null
        $toRaw   = $this->queryParam('to');

        // Filtros por fecha (YYYY-MM-DD) – se usan sobre fecha_prestamo
        $fromFilter = $this->normalizeDateFilter($fromRaw);
        $toFilter   = $this->normalizeDateFilter($toRaw);

        // Si ambas fechas existen y vienen invertidas, las intercambiamos
        if ($fromFilter !== null && $toFilter !== null && $fromFilter > $toFilter) {
            [$fromFilter, $toFilter] = [$toFilter, $fromFilter];
        }

        // KPIs globales (sin filtro)
        $stats = DashboardModel::kpis();

        // Tiquetes activos + atrasados para la tabla (filtrados por fecha si aplica)
        $tiquetes = DashboardModel::tiquetesCriticos(10, $fromFilter, $toFilter);

        // Libros disponibles para el modal rápido de creación
        $libros = Tiquete::disponiblesForSelect();

        // Datos para gráficos usando mismo filtro de fechas
        $rawCat     = DashboardModel::categoriaEdadRaw($fromFilter, $toFilter);
        $rawEstados = DashboardModel::estadosRaw($fromFilter, $toFilter);

        $chartCategoria       = [];
        $chartEstados         = [];
        $totalPeriodoTiquetes = 0;

        // Transformar categoría de edad a estructura amigable para Chart.js
        foreach ($rawCat as $row) {
            $codigo   = (string)($row['categoria'] ?? '');
            $cantidad = (int)($row['total'] ?? 0);

            if ($codigo === '' || $cantidad <= 0) {
                continue;
            }

            $totalPeriodoTiquetes += $cantidad;

            $chartCategoria[] = [
                'codigo'      => $codigo, // OP, A, etc.
                'label'       => self::CATEGORIA_LABELS[$codigo] ?? $codigo, // texto largo
                // Si tu JS usa "descripcion", puedes descomentar esto:
                // 'descripcion' => self::CATEGORIA_LABELS[$codigo] ?? $codigo,
                'cantidad'    => $cantidad,
            ];
        }

        // Transformar estados
        foreach ($rawEstados as $row) {
            $estado   = (string)($row['estado'] ?? '');
            $cantidad = (int)($row['total'] ?? 0);

            if ($estado === '' || $cantidad <= 0) {
                continue;
            }

            $label = match ($estado) {
                'En Prestamo' => 'En préstamo',
                'Atrasado'    => 'Atrasado',
                'Devuelto'    => 'Devuelto',
                default       => $estado,
            };

            $chartEstados[] = [
                'estado'   => $estado,
                'label'    => $label,
                'cantidad' => $cantidad,
            ];
        }

        $csrf = CSRF::token();

        $this->render('dashboard/index', [
            'titulo'               => 'Panel principal',
            'stats'                => $stats,
            'tiquetes'             => $tiquetes,
            'libros'               => $libros,
            'csrf'                 => $csrf,

            // OJO: para que el input type="date" mantenga valor, debe ser YYYY-MM-DD o ''
            'fromFilter'           => $fromFilter ?? '',
            'toFilter'             => $toFilter ?? '',

            'chartCategoria'       => $chartCategoria,
            'chartEstados'         => $chartEstados,
            'totalPeriodoTiquetes' => $totalPeriodoTiquetes,
        ]);
    }

    /**
     * Lee parámetros del querystring (GET) de forma segura.
     */
    private function queryParam(string $key): ?string
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        // Si viene como array (raro), ignorar.
        if (is_array($_GET[$key])) {
            return null;
        }

        $val = trim((string)$_GET[$key]);
        return $val === '' ? null : $val;
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
