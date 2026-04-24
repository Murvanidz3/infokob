<?php
/**
 * Route Definitions
 * Maps URI patterns to Controller::method
 */

return [
    // ─── Public Routes ─────────────────────────────────────
    'GET' => [
        '/'                     => ['HomeController', 'index'],
        '/listings'             => ['PropertyController', 'index'],
        '/listings/{slug}'      => ['PropertyController', 'show'],
        '/hotels'               => ['PageController', 'hotels'],
        '/announcements'        => ['PageController', 'announcements'],
        '/employment'           => ['PageController', 'employment'],
        '/education'            => ['PageController', 'education'],
        '/tourism'              => ['PageController', 'tourism'],
        '/kobuleti'             => ['PageController', 'kobuleti'],
        '/contact'              => ['PageController', 'contact'],
        '/register'             => ['AuthController', 'registerForm'],
        '/login'                => ['AuthController', 'loginForm'],
        '/logout'               => ['AuthController', 'logout'],
        '/lang/{code}'          => ['LanguageController', 'set'],
        '/sitemap.xml'          => ['PageController', 'sitemap'],
        
        // ─── User Routes (auth required) ──────────────────
        '/my/dashboard'         => ['UserController', 'dashboard'],
        '/my/listings'          => ['UserController', 'listings'],
        '/my/listings/create'   => ['UserController', 'createForm'],
        '/my/listings/{id}/edit' => ['UserController', 'editForm'],
        '/my/profile'           => ['UserController', 'profileForm'],
    ],
    
    'POST' => [
        '/register'             => ['AuthController', 'register'],
        '/login'                => ['AuthController', 'login'],
        '/contact'              => ['PageController', 'sendContact'],
        
        // ─── User Routes (auth required) ──────────────────
        '/my/listings/create'   => ['UserController', 'create'],
        '/my/listings/{id}/edit' => ['UserController', 'update'],
        '/my/listings/{id}/delete' => ['UserController', 'delete'],
        '/my/profile'           => ['UserController', 'updateProfile'],
    ],
    
    // ─── AJAX Routes ───────────────────────────────────────
    'AJAX' => [
        'GET' => [
            '/api/listings'     => ['PropertyController', 'apiIndex'],
        ],
    ],
];
