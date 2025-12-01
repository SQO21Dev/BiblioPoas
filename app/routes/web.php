<?php

use App\Core\Router;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UsuariosController;
use App\Controllers\ClientesController;
use App\Controllers\LibrosController;
use App\Controllers\TiquetesController;
use App\Controllers\CategoriasController;
use App\Controllers\LogsController;

/** @var Router $router */

// Home ⇒ redirige al login si no hay sesión, al dashboard si la hay
$router->get('/', function () {
    if (empty($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    header('Location: /dashboard');
    exit;
});

/* ===== Auth ===== */
$router->get('/login',  [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/forgot',  [AuthController::class, 'forgotForm']);
$router->post('/forgot', [AuthController::class, 'sendTempPassword']);

$router->get('/reset',  [AuthController::class, 'resetForm']);
$router->post('/reset', [AuthController::class, 'doReset']);

/* ===== Dashboard ===== */
$router->get('/dashboard', [DashboardController::class, 'index']);

/* ===== Usuarios (admin) ===== */
$router->get('/usuarios', [UsuariosController::class, 'index']);
$router->post('/usuarios/create', [UsuariosController::class, 'create']);
$router->post('/usuarios/update', [UsuariosController::class, 'update']);
$router->post('/usuarios/delete', [UsuariosController::class, 'delete']);

// Exportar usuarios
$router->get('/usuarios/export/csv',  [UsuariosController::class, 'exportCsv']);
$router->get('/usuarios/export/xlsx', [UsuariosController::class, 'exportXlsx']);

/* ===== Clientes ===== */
$router->get('/clientes', [ClientesController::class, 'index']);
$router->post('/clientes/create', [ClientesController::class, 'create']);
$router->post('/clientes/update', [ClientesController::class, 'update']);
$router->post('/clientes/delete', [ClientesController::class, 'delete']);

/* ===== Libros ===== */
$router->get('/libros', [LibrosController::class, 'index']);
$router->post('/libros/create', [LibrosController::class, 'create']);
$router->post('/libros/update', [LibrosController::class, 'update']);
$router->post('/libros/delete', [LibrosController::class, 'delete']);
$router->get('/libros/export/csv',  [LibrosController::class, 'exportCsv']);
$router->get('/libros/export/xlsx', [LibrosController::class, 'exportXlsx']);

/* ===== Tiquetes ===== */
$router->get('/tiquetes', [TiquetesController::class, 'index']);
$router->post('/tiquetes/create', [TiquetesController::class, 'create']);
$router->post('/tiquetes/update', [TiquetesController::class, 'update']);
$router->post('/tiquetes/delete', [TiquetesController::class, 'delete']);
$router->get('/tiquetes/export/csv',  [TiquetesController::class, 'exportCsv']);
$router->get('/tiquetes/export/xlsx', [TiquetesController::class, 'exportXlsx']);

// 🔹 Nueva ruta para el modal rápido del Dashboard
$router->post('/tiquetes/dashboard-update', [TiquetesController::class, 'dashboardUpdate']);

/* ===== Categorías ===== */
$router->get('/categorias', [CategoriasController::class, 'index']);
$router->post('/categorias/create', [CategoriasController::class, 'create']);
$router->post('/categorias/update', [CategoriasController::class, 'update']);
$router->post('/categorias/delete', [CategoriasController::class, 'delete']);
$router->get('/categorias/export/csv',  [CategoriasController::class, 'exportCsv']);
$router->get('/categorias/export/xlsx', [CategoriasController::class, 'exportXlsx']);

/* ===== Logs (admin) ===== */
$router->get('/logs', [LogsController::class, 'index']);
$router->get('/logs/export/csv',  [LogsController::class, 'exportCsv']);
$router->get('/logs/export/xlsx', [LogsController::class, 'exportXlsx']);

$router->get('/_mailtest', function () {
    \App\Services\Mailer::send('sebasqo21@outlook.com', 'Prueba SMTP', '<b>Hola</b> desde PHPMailer.');
    echo 'Intento de envío realizado. Revisa tu correo y /storage/mail_debug.log';
});
