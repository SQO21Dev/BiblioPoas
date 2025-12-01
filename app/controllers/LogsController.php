<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Log;

class LogsController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin']); // o requireAuth() si querés abrirlo más

        $filters = [
            'fini'    => trim($this->input('fini')),
            'ffin'    => trim($this->input('ffin')),
            'entidad' => trim($this->input('entidad')),
            'accion'  => trim($this->input('accion')),
        ];

        $logs  = Log::buscar($filters);
        $total = Log::contar($filters);

        $this->render('logs/index', compact('logs', 'total', 'filters'));
    }

    public function exportCsv(): void
    {
        $this->requireRole(['admin']);

        $filters = [
            'fini'    => trim($_GET['fini'] ?? ''),
            'ffin'    => trim($_GET['ffin'] ?? ''),
            'entidad' => trim($_GET['entidad'] ?? ''),
            'accion'  => trim($_GET['accion'] ?? ''),
        ];

        $rows = Log::exportar($filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=logs.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Fecha','Usuario','Rol','Acción','Entidad','Descripción','Resultado']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['fecha_evento']   ?? '',
                $r['usuario_actor']  ?? '',
                $r['rol']            ?? '',
                $r['accion']         ?? '',
                $r['entidad']        ?? '',
                $r['descripcion']    ?? '',
                $r['resultado']      ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    public function exportXlsx(): void
    {
        $this->requireRole(['admin']);

        $filters = [
            'fini'    => trim($_GET['fini'] ?? ''),
            'ffin'    => trim($_GET['ffin'] ?? ''),
            'entidad' => trim($_GET['entidad'] ?? ''),
            'accion'  => trim($_GET['accion'] ?? ''),
        ];

        $rows = Log::exportar($filters);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=logs.xlsx');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Fecha','Usuario','Rol','Acción','Entidad','Descripción','Resultado']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['fecha_evento']   ?? '',
                $r['usuario_actor']  ?? '',
                $r['rol']            ?? '',
                $r['accion']         ?? '',
                $r['entidad']        ?? '',
                $r['descripcion']    ?? '',
                $r['resultado']      ?? '',
            ]);
        }

        fclose($out);
        exit;
    }
}
