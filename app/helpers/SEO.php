<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Router.php';

class SEO
{
    public static function defaultMeta(): array
    {
        $lang = Language::get();
        return [
            'title' => Helpers::__('site_name_' . $lang) . ' — InfoKobuleti',
            'description' => Helpers::__('meta_description_default'),
            'og_image' => Helpers::asset('img/og-default.svg'),
            'canonical' => self::currentCanonical(),
        ];
    }

    public static function currentCanonical(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = Router::normalizePath((string) $path);
        return rtrim(BASE_URL, '/') . $path;
    }

    /**
     * @param array<string, mixed> $property
     */
    public static function forProperty(array $property, string $lang): array
    {
        $title = (string) ($property['title'] ?? '');
        $price = Helpers::formatPrice(
            isset($property['price']) ? (float) $property['price'] : null,
            (string) ($property['currency'] ?? 'USD'),
            !empty($property['price_negotiable'])
        );
        $pageTitle = $title . ' — ' . $price . ' | InfoKobuleti';
        $desc = mb_substr(strip_tags((string) ($property['description'] ?? '')), 0, 160);
        if ($desc === '') {
            $desc = $title;
        }
        $img = !empty($property['main_image'])
            ? Image::getImageUrl((string) $property['main_image'], 'medium')
            : Helpers::asset('img/og-default.svg');

        return [
            'title' => $pageTitle,
            'description' => $desc,
            'og_image' => $img,
            'canonical' => rtrim(BASE_URL, '/') . '/listings/' . rawurlencode((string) $property['slug']),
        ];
    }
}
