<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';

class Language
{
    public static function init(): void
    {
        if (isset($_SESSION['lang']) && self::isSupported((string) $_SESSION['lang'])) {
            return;
        }
        if (isset($_COOKIE[LANG_COOKIE]) && self::isSupported((string) $_COOKIE[LANG_COOKIE])) {
            $_SESSION['lang'] = $_COOKIE[LANG_COOKIE];
            return;
        }
        $_SESSION['lang'] = DEFAULT_LANG;
    }

    public static function get(): string
    {
        $lang = $_SESSION['lang'] ?? DEFAULT_LANG;
        return self::isSupported((string) $lang) ? (string) $lang : DEFAULT_LANG;
    }

    public static function set(string $code): void
    {
        $code = strtolower($code);
        if (!self::isSupported($code)) {
            $code = DEFAULT_LANG;
        }
        $_SESSION['lang'] = $code;
        $exp = time() + LANG_COOKIE_DAYS * 86400;
        setcookie(LANG_COOKIE, $code, [
            'expires' => $exp,
            'path' => '/',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function isSupported(string $code): bool
    {
        return in_array($code, SUPPORTED_LANGS, true);
    }
}
