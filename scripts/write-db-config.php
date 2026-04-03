<?php

declare(strict_types=1);

/**
 * Build config/db.local.php from environment variables (GitHub Actions → Hostinger).
 *
 * Required: DB_HOST, DB_NAME, DB_USER
 * Optional: DB_PASS (empty string if not set)
 *
 * Usage:
 *   DB_HOST=127.0.0.1 DB_NAME=u123_db DB_USER=u123_user DB_PASS=... php scripts/write-db-config.php
 */
$required = ['DB_HOST', 'DB_NAME', 'DB_USER'];
foreach ($required as $key) {
    $v = getenv($key);
    if ($v === false || $v === '') {
        fwrite(STDERR, "write-db-config: missing environment variable {$key}\n");
        exit(1);
    }
}

$pass = getenv('DB_PASS');
$config = [
    'host' => getenv('DB_HOST'),
    'dbname' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'pass' => $pass !== false ? $pass : '',
    'charset' => 'utf8mb4',
];

$target = dirname(__DIR__) . '/config/db.local.php';
$php = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($config, true) . ";\n";
if (file_put_contents($target, $php) === false) {
    fwrite(STDERR, "write-db-config: could not write {$target}\n");
    exit(1);
}

echo "Wrote {$target}\n";
