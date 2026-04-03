<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__, 2) . '/app/models/Property.php';
require_once dirname(__DIR__, 2) . '/app/models/Setting.php';

class PropertyController extends BaseController
{
    public function index(array $params = []): void
    {
        Property::deactivateExpiredFeatured();
        $lang = Language::get();
        $filters = Property::parseFiltersFromRequest($_GET);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = Property::countFiltered($filters, $lang);
        $perPage = Property::PER_PAGE;
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $listings = Property::getFiltered($filters, $lang, $page, $perPage);

        $qs = $_GET;
        unset($qs['page'], $qs['ajax']);
        $paginationBase = rtrim(BASE_URL, '/') . '/listings' . ($qs !== [] ? '?' . http_build_query($qs) : '');

        if (!empty($_GET['ajax'])) {
            ob_start();
            foreach ($listings as $property) {
                View::partial('property-card', ['property' => $property]);
            }
            $cardsHtml = ob_get_clean();
            ob_start();
            View::partial('pagination', [
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'paginationBase' => $paginationBase,
            ]);
            $paginationHtml = ob_get_clean();
            Helpers::jsonResponse([
                'ok' => true,
                'html' => $cardsHtml,
                'pagination' => $paginationHtml,
                'total' => $total,
                'countLabel' => Helpers::__('results_found', ['n' => (string) $total]),
                'page' => $page,
                'totalPages' => $totalPages,
            ]);
        }

        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_listings') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('meta_listings');

        $extraScripts = '<script defer src="' . Helpers::e(Helpers::asset('js/filters.js')) . '"></script>';

        View::render('properties/index', [
            'meta' => $meta,
            'listings' => $listings,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'paginationBase' => $paginationBase,
            'extraScripts' => $extraScripts,
        ]);
    }

    public function show(array $params = []): void
    {
        Property::deactivateExpiredFeatured();
        $slug = (string) ($params['slug'] ?? '');
        $lang = Language::get();
        $property = Property::getPublicBySlug($slug, $lang);
        if ($property === null) {
            http_response_code(404);
            $meta = SEO::defaultMeta();
            $meta['title'] = '404 — InfoKobuleti';
            View::render('errors/404', ['meta' => $meta], 'main');
            return;
        }

        Property::incrementViews((int) $property['id']);
        $images = Property::getImages((int) $property['id']);
        $similar = Property::getSimilar(
            (int) $property['id'],
            (string) ($property['district'] ?? ''),
            (string) ($property['type'] ?? ''),
            $lang,
            3
        );

        $meta = SEO::forProperty($property, $lang);

        $mapsKey = Setting::get('google_maps_key');
        $lat = $property['lat'] ?? null;
        $lng = $property['lng'] ?? null;

        $wa = $property['contact_whatsapp'] ?? $property['user_whatsapp'] ?? '';
        $tg = $property['contact_telegram'] ?? $property['user_telegram'] ?? '';
        $phone = (string) ($property['contact_phone'] ?? '');

        View::render('properties/single', [
            'meta' => $meta,
            'property' => $property,
            'images' => $images,
            'similar' => $similar,
            'mapsKey' => $mapsKey,
            'lat' => $lat,
            'lng' => $lng,
            'whatsapp' => $wa,
            'telegram' => $tg,
            'phone' => $phone,
        ]);
    }
}
