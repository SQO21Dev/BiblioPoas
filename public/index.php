<?php
// Front Controller. Docroot = carpeta "public".
// Carga autoload (composer), define constantes, sesión, config, DB, rutas y despacha.

// === BASE_PATH ===
define('BASE_PATH', dirname(__DIR__));

// === Composer Autoload (PHPMailer, etc.) ===
$vendor = BASE_PATH . '/vendor/autoload.php';
if (is_file($vendor)) {
    require $vendor;
}

// === Autoloader PSR-4 simple para App\ (si algo no entra por composer) ===
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

// === Sesión ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === Cargar config base + local y definir CONFIG global ===
$baseCfg  = is_file(BASE_PATH . '/app/config/config.php')       ? include BASE_PATH . '/app/config/config.php'       : [];
$localCfg = is_file(BASE_PATH . '/app/config/config.local.php') ? include BASE_PATH . '/app/config/config.local.php' : [];
$CONFIG   = array_replace_recursive($baseCfg, $localCfg);
define('CONFIG', $CONFIG);

// === Zona horaria ===
if (!empty($CONFIG['app']['timezone'])) {
    date_default_timezone_set($CONFIG['app']['timezone']);
}

// === Inicializar DB (pasa SOLO el bloque 'db') ===
use App\Core\DB;
if (!empty($CONFIG['db']) && is_array($CONFIG['db'])) {
    DB::init($CONFIG['db']);
}

// === Rutas ===
use App\Core\Router;

$router = new Router();
require BASE_PATH . '/app/routes/web.php';

// === Servir assets estáticos en php -S (solo DEV con: php -S localhost:8000 -t public) ===
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicFile = __DIR__ . $uri;
if ($uri !== '/' && is_file($publicFile)) {
    // deja que el servidor embebido entregue css/js/img
    return false;
}

// === Dispatch ===
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
