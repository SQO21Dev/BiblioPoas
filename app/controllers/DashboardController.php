<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Models\Dashboard as DashboardModel;
use App\Models\Tiquete;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // KPIs
        $stats = DashboardModel::kpis();

        // Tiquetes activos + atrasados para la tabla
        $tiquetes = DashboardModel::tiquetesCriticos(10);

        // Libros disponibles para el modal rápido de creación
        $libros = Tiquete::disponiblesForSelect();

        // CSRF para formularios en dashboard
        $csrf = CSRF::token();

        $this->render('dashboard/index', [
            'titulo'   => 'Panel principal',
            'stats'    => $stats,
            'tiquetes' => $tiquetes,
            'libros'   => $libros,
            'csrf'     => $csrf,
        ]);
    }
}
