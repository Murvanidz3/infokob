<?php

declare(strict_types=1);

class LanguageController
{
    public function setLang(array $params = []): void
    {
        $code = isset($params['code']) ? strtolower((string) $params['code']) : DEFAULT_LANG;
        Language::set($code);
        $ref = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/');
        if (!is_string($ref) || !str_starts_with($ref, BASE_URL)) {
            $ref = BASE_URL . '/';
        }
        Helpers::redirect($ref);
    }
}
