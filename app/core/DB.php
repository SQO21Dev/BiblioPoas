<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class DB
{
    private static ?PDO $pdo = null;
    private static array $cfg = [];

    /**
     * Acepta:
     *  - init($config['db'])  => array con host, database, user, password, port
     *  - init($config)        => array completo con clave 'db' dentro
     */
    public static function init(array $config): void
    {
        // Si viene el árbol completo, tomar la rama 'db'
        if (isset($config['db']) && is_array($config['db'])) {
            $config = $config['db'];
        }

        // Normalizar claves requeridas
        $required = ['host','database','user','password'];
        foreach ($required as $k) {
            if (!array_key_exists($k, $config) || $config[$k] === '') {
                throw new \RuntimeException("DB config missing key: {$k}");
            }
        }

        self::$cfg = [
            'host'     => (string)$config['host'],
            'database' => (string)$config['database'],
            'user'     => (string)$config['user'],
            'password' => (string)$config['password'],
            'port'     => isset($config['port']) && $config['port'] !== '' ? (string)$config['port'] : '3306',
        ];
    }

    public static function pdo(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        if (empty(self::$cfg)) {
            // Si no se inicializó, intentamos auto-cargar config para ayudar en dev
            $root = dirname(__DIR__, 1); // app/core -> app
            $cfgFile = $root . '/config/config.php';
            $cfgLocal = $root . '/config/config.local.php';
            $base = file_exists($cfgFile) ? require $cfgFile : [];
            $local = file_exists($cfgLocal) ? require $cfgLocal : [];
            $merged = array_replace_recursive($base, $local);
            if (!isset($merged['db'])) {
                throw new \RuntimeException('DB not initialized and config[db] not found.');
            }
            self::init($merged['db']);
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%s;charset=utf8mb4',
            self::$cfg['host'],
            self::$cfg['database'],
            self::$cfg['port']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                self::$cfg['user'],
                self::$cfg['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            // En producción: log del error
            exit('Error de conexión a la base de datos.');
        }

        return self::$pdo;
    }
}
