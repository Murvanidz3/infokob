<?php

declare(strict_types=1);

/**
 * Application configuration — Hostinger-compatible (no Composer).
 */

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDir = str_replace('\\', '/', $scriptDir);
if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') {
    $scriptDir = '';
}

/** Public site base URL (parent of /admin when entry script is under admin/) */
$publicPath = $scriptDir;
if ($publicPath !== '' && preg_match('#/admin$#i', $publicPath)) {
    $parent = str_replace('\\', '/', dirname($publicPath));
    if ($parent === '/' || $parent === '.' || $parent === '') {
        $publicPath = '';
    } else {
        $publicPath = $parent;
    }
}
$publicBaseUrl = rtrim($scheme . '://' . $host . $publicPath, '/');
$baseUrl = rtrim($scheme . '://' . $host . $scriptDir, '/');

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

define('BASE_URL', $baseUrl);
define('PUBLIC_BASE_URL', $publicBaseUrl);
define('APP_URL', $publicBaseUrl);
define('PUBLIC_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'public');
define('UPLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'uploads');
define('UPLOAD_URL', $publicBaseUrl . '/uploads');
define('LANG_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'lang');
define('VIEWS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'views');
define('CONFIG_PATH', __DIR__);

define('SESSION_NAME', 'infokobuleti');
define('SESSION_LIFETIME', 60 * 60 * 24 * 7);
define('LANG_COOKIE', 'lang');
define('LANG_COOKIE_DAYS', 30);
define('CSRF_TOKEN_KEY', 'csrf_token');

define('DEFAULT_LANG', 'ka');
define('SUPPORTED_LANGS', ['ka', 'ru', 'en']);

define('IMAGE_MAX_BYTES', 5 * 1024 * 1024);
define('IMAGE_ORIGINAL_MAX_W', 1920);
define('IMAGE_ORIGINAL_MAX_H', 1280);
define('IMAGE_MEDIUM_W', 800);
define('IMAGE_MEDIUM_H', 600);
define('IMAGE_THUMB_W', 400);
define('IMAGE_THUMB_H', 300);

define('RATE_LIMIT_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW_SEC', 600);

define('HOMEPAGE_CACHE_FILE', BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'home.cache');
define('HOMEPAGE_CACHE_TTL_SEC', 1800);
