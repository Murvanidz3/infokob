<?php

declare(strict_types=1);

class Setting
{
    /** @var array<string, string>|null */
    private static ?array $cache = null;

    public static function get(string $key, string $default = ''): string
    {
        self::load();
        return (string) (self::$cache[$key] ?? $default);
    }

    private static function load(): void
    {
        if (self::$cache !== null) {
            return;
        }
        $pdo = Database::getInstance();
        $st = $pdo->query('SELECT key_name, value FROM settings');
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        self::$cache = [];
        foreach ($rows as $row) {
            self::$cache[(string) $row['key_name']] = (string) $row['value'];
        }
    }

    public static function resetCache(): void
    {
        self::$cache = null;
    }

    public static function set(string $key, string $value): void
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'INSERT INTO settings (key_name, value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = CURRENT_TIMESTAMP'
        );
        $st->execute([$key, $value]);
        self::$cache = null;
    }
}
