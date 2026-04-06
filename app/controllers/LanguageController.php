<?php

declare(strict_types=1);

class LanguageController
{
    public function setLang(array $params = []): void
    {
        // Prefer query string (?code=) — works with switch-lang.php and avoids /lang/ path clashes with the lang/ folder
        $code = isset($_GET['code']) && is_string($_GET['code'])
            ? strtolower($_GET['code'])
            : (isset($params['code']) ? strtolower((string) $params['code']) : DEFAULT_LANG);
        Language::set($code);
        $ref = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
        $fallback = rtrim(PUBLIC_BASE_URL, '/') . '/';
        $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        $refHost = $ref !== '' ? parse_url($ref, PHP_URL_HOST) : null;
        $refHost = is_string($refHost) ? strtolower($refHost) : '';
        if ($ref !== '' && $host !== '' && $refHost === $host && str_starts_with($ref, 'http')) {
            Helpers::redirect($ref);
            return;
        }
        Helpers::redirect($fallback);
    }
}
