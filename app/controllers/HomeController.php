<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/Property.php';
require_once dirname(__DIR__, 2) . '/app/models/User.php';

class HomeController
{
    public function index(array $params = []): void
    {
        Property::deactivateExpiredFeatured();
        $lang = Language::get();

        $featured = Property::getFeaturedForHome($lang, 6);
        $stats = [
            'listings' => Property::countActive(),
            'sea' => Property::countNearSea(200),
            'users' => User::countRegularUsers(),
            'sold' => Property::countSold(),
        ];

        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('site_name_' . $lang) . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('meta_description_default');

        View::render('home/index', [
            'meta' => $meta,
            'featured' => $featured,
            'stats' => $stats,
            'heroDeal' => 'sale',
        ]);
    }
}
