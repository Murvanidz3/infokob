<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net https://unpkg.com \'unsafe-inline\'; style-src \'self\' https://unpkg.com https://fonts.googleapis.com \'unsafe-inline\'; font-src \'self\' https://unpkg.com https://fonts.gstatic.com data:; img-src \'self\' data: https: blob:; connect-src \'self\'; frame-ancestors \'self\'; base-uri \'self\'; form-action \'self\'');

ob_start();
session_name(SESSION_NAME);
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

Language::init();

$routes = require dirname(__DIR__) . '/config/admin_routes.php';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = Router::stripBasePath($uri, $_SERVER['SCRIPT_NAME'] ?? '/admin/index.php');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$match = Router::match($method, $uri, $routes);

if ($match === null) {
    if (!Auth::isLoggedIn()) {
        Helpers::redirect(PUBLIC_BASE_URL . '/login');
    }
    if (!Auth::isAdmin()) {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>404</title></head><body><p>Not found.</p></body></html>';
        exit;
    }
    http_response_code(404);
    $meta = ['title' => Helpers::__('error_404'), 'description' => ''];
    View::render('errors/404', ['meta' => $meta], 'admin');
    exit;
}

$handler = $match['handler'];
$params = $match['params'];
[$class, $action] = explode('@', $handler);
$file = dirname(__DIR__) . '/app/controllers/' . $class . '.php';
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
