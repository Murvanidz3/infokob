<?php
/**
 * InfoKobuleti — Front Controller
 * All public requests are routed through this file
 */

// ─── Error Reporting ───────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// ─── Output Buffering ──────────────────────────────────────
ob_start();

// ─── Load Configuration ────────────────────────────────────
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// ─── Load Helpers ──────────────────────────────────────────
require_once __DIR__ . '/app/helpers/Helpers.php';
require_once __DIR__ . '/app/helpers/Language.php';
require_once __DIR__ . '/app/helpers/Auth.php';
require_once __DIR__ . '/app/helpers/Image.php';
require_once __DIR__ . '/app/helpers/SEO.php';

// ─── Start Session ─────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Load Language ─────────────────────────────────────────
Language::load();

// ─── Security Headers ─────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ─── Parse Request ─────────────────────────────────────────
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Remove base path from URI
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
$uri = parse_url($requestUri, PHP_URL_PATH);

if ($basePath && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

$uri = '/' . trim($uri, '/');
if ($uri === '/') {
    $uri = '/';
}

// ─── Check for AJAX requests ──────────────────────────────
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ─── Load Routes ───────────────────────────────────────────
$routes = require __DIR__ . '/config/routes.php';

// ─── Route Matching ────────────────────────────────────────
$matched = false;
$params = [];

// Check AJAX routes first if it's an AJAX request
if ($isAjax && isset($routes['AJAX'][$requestMethod])) {
    foreach ($routes['AJAX'][$requestMethod] as $pattern => $handler) {
        if (matchRoute($pattern, $uri, $params)) {
            $matched = true;
            break;
        }
    }
}

// Check regular routes
if (!$matched && isset($routes[$requestMethod])) {
    foreach ($routes[$requestMethod] as $pattern => $handler) {
        if (matchRoute($pattern, $uri, $params)) {
            $matched = true;
            break;
        }
    }
}

// ─── Dispatch ──────────────────────────────────────────────
if ($matched) {
    $controllerName = $handler[0];
    $methodName = $handler[1];
    
    // Load model files
    $modelFiles = glob(APP_PATH . '/models/*.php');
    foreach ($modelFiles as $modelFile) {
        require_once $modelFile;
    }
    
    // Load the controller
    $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
    
    // Special case for LanguageController (embedded below)
    if ($controllerName === 'LanguageController') {
        Language::handleSwitch($params['code'] ?? DEFAULT_LANG);
        exit;
    }
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        $controller = new $controllerName();
        
        if (method_exists($controller, $methodName)) {
            // Call the controller method with route parameters
            call_user_func_array([$controller, $methodName], $params);
        } else {
            show404();
        }
    } else {
        show404();
    }
} else {
    show404();
}

// ─── Flush Output ──────────────────────────────────────────
ob_end_flush();

// ─── Helper Functions ──────────────────────────────────────

/**
 * Match a route pattern against a URI
 * Supports {param} placeholders
 */
function matchRoute($pattern, $uri, &$params) {
    $params = [];
    
    // Exact match
    if ($pattern === $uri) return true;
    
    // Parameter matching
    $patternParts = explode('/', trim($pattern, '/'));
    $uriParts = explode('/', trim($uri, '/'));
    
    if (count($patternParts) !== count($uriParts)) return false;
    
    foreach ($patternParts as $i => $part) {
        if (preg_match('/^\{(\w+)\}$/', $part, $matches)) {
            $params[$matches[1]] = urldecode($uriParts[$i]);
        } elseif ($part !== $uriParts[$i]) {
            return false;
        }
    }
    
    return true;
}

/**
 * Show 404 page
 */
function show404() {
    http_response_code(404);
    $pageTitle = __('page_not_found') . ' | ' . SITE_NAME;
    
    if (file_exists(VIEW_PATH . '/errors/404.php')) {
        require VIEW_PATH . '/errors/404.php';
    } else {
        echo '<!DOCTYPE html><html><head><title>404</title></head><body>';
        echo '<h1>404 — ' . __('page_not_found') . '</h1>';
        echo '<p>' . __('404_desc') . '</p>';
        echo '<a href="' . BASE_URL . '">' . __('go_to_home') . '</a>';
        echo '</body></html>';
    }
    exit;
}
