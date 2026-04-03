<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net https://unpkg.com https://maps.googleapis.com \'unsafe-inline\'; style-src \'self\' https://unpkg.com \'unsafe-inline\'; font-src \'self\' https://unpkg.com data:; img-src \'self\' data: https: blob:; connect-src \'self\' https://maps.googleapis.com; frame-ancestors \'self\'; base-uri \'self\'; form-action \'self\'');

ob_start();
session_name(SESSION_NAME);
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

Language::init();

$routes = require __DIR__ . '/config/routes.php';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = Router::stripBasePath($uri, $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$match = Router::match($method, $uri, $routes);

if ($match === null) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="ka"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>404 — InfoKobuleti</title></head><body><p>404</p></body></html>';
    exit;
}

$handler = $match['handler'];
$params = $match['params'];
[$class, $action] = explode('@', $handler);
$file = __DIR__ . '/app/controllers/' . $class . '.php';
if (!is_readable($file)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Controller not found';
    exit;
}
require_once $file;
$controller = new $class();
if (!method_exists($controller, $action)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Action not found';
    exit;
}
$controller->{$action}($params);
