<?php

declare(strict_types=1);

/**
 * Language switcher — standalone entry (no mod_rewrite path).
 * Links: /switch-lang.php?code=ka|ru|en
 */
require_once __DIR__ . '/bootstrap.php';

session_name(SESSION_NAME);
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

Language::init();

require_once __DIR__ . '/app/controllers/LanguageController.php';
$controller = new LanguageController();
$controller->setLang([]);
