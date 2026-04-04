<?php

declare(strict_types=1);

/**
 * Localized strings from guide demo arrays (ka / en / ru).
 */
class GuideLocale
{
    /**
     * @param array<string, string>|string $value
     */
    public static function t(array|string $value, ?string $lang = null): string
    {
        if (is_string($value)) {
            return $value;
        }
        $lang = $lang ?? Language::get();
        return $value[$lang] ?? $value['ka'] ?? $value['en'] ?? $value['ru'] ?? '';
    }

    /**
     * @param list<array<string, string>>|list<string> $lines
     * @return list<string>
     */
    public static function lines(array $lines, ?string $lang = null): array
    {
        $out = [];
        foreach ($lines as $line) {
            $out[] = self::t($line, $lang);
        }
        return $out;
    }
}
