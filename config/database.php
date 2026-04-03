<?php

declare(strict_types=1);

/**
 * PDO singleton — UTF-8, prepared statements only via callers.
 */

require_once __DIR__ . '/config.php';

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $path = CONFIG_PATH . '/db.local.php';
        if (is_readable($path)) {
            $cfg = require $path;
        } else {
            $cfg = [
                'host' => '127.0.0.1',
                'dbname' => 'infokobuleti',
                'user' => 'root',
                'pass' => '',
                'charset' => 'utf8mb4',
            ];
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $cfg['host'],
            $cfg['dbname'],
            $cfg['charset'] ?? 'utf8mb4'
        );

        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        self::$instance = $pdo;
        return self::$instance;
    }

    public static function resetForTesting(): void
    {
        self::$instance = null;
    }
}
