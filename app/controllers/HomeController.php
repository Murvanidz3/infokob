<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/Property.php';
require_once dirname(__DIR__, 2) . '/app/models/User.php';
require_once dirname(__DIR__, 2) . '/app/models/Setting.php';

class HomeController
{
    public function index(array $params = []): void
    {
        Property::deactivateExpiredFeatured();
        $lang = Language::get();

        $featured = Property::getFeaturedForHome($lang, 6);
        $filtersNewest = Property::parseFiltersFromRequest(['deal' => 'sale']);
        $newestListings = Property::getFiltered($filtersNewest, $lang, 1, 6);
        $stats = [
            'listings' => Property::countActive(),
            'sea' => Property::countNearSea(200),
            'users' => User::countRegularUsers(),
            'sold' => Property::countSold(),
        ];

        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('site_name_' . $lang) . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('meta_description_default');

        $mapsKey = Setting::get('google_maps_key');
        $mapMarkers = [];
        foreach ($newestListings as $row) {
            if (!empty($row['lat']) && !empty($row['lng']) && !empty($row['slug'])) {
                $mapMarkers[] = [
                    'lat' => (float) $row['lat'],
                    'lng' => (float) $row['lng'],
                    'title' => (string) ($row['title'] ?? ''),
                    'price' => Helpers::formatPropertyPrice($row),
                    'url' => rtrim(BASE_URL, '/') . '/listings/' . rawurlencode((string) $row['slug']),
                ];
            }
        }

        $extraScripts = '';
        if ($mapsKey !== '' && $mapMarkers !== []) {
            $payload = json_encode(['key' => $mapsKey, 'markers' => $mapMarkers], JSON_UNESCAPED_UNICODE);
            $extraScripts = '<script>window.__HOME_MAP__=' . $payload . ';</script>'
                . '<script defer src="' . Helpers::asset('js/map.js') . '"></script>'
                . '<script defer src="https://maps.googleapis.com/maps/api/js?key=' . htmlspecialchars($mapsKey, ENT_QUOTES, 'UTF-8') . '&callback=initHomeMap"></script>';
        }

        View::render('home/index', [
            'meta' => $meta,
            'featured' => $featured,
            'newestListings' => $newestListings,
            'mapMarkers' => $mapMarkers,
            'stats' => $stats,
            'heroDeal' => 'sale',
            'bodyClass' => 'page-home',
            'mapsKey' => $mapsKey,
            'extraScripts' => $extraScripts,
        ]);
    }
}
