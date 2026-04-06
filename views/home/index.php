<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array<string, mixed>> $featured */
/** @var list<array<string, mixed>> $newestListings */
/** @var array{listings:int,sea:int,users:int,sold:int} $stats */
/** @var string $heroDeal */
/** @var string $mapsKey */
/** @var list<array{lat:float,lng:float,title:string,price:string,url:string}> $mapMarkers */

$lang = Language::get();

$heroImgs = [
    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80',
    'https://images.unsplash.com/photo-1519046904884-73404d90b1e8?w=800&q=80',
    'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=800&q=80',
    'https://images.unsplash.com/photo-1439066615861-d1af74d74000?w=800&q=80',
];

$catTypes = [
    ['type' => 'apartment', 'icon' => '🏢', 'label' => 'type_apartment'],
    ['type' => 'house', 'icon' => '🏠', 'label' => 'type_house'],
    ['type' => 'cottage', 'icon' => '🌿', 'label' => 'type_cottage'],
    ['type' => 'land', 'icon' => '🌱', 'label' => 'type_land'],
    ['type' => 'hotel_room', 'icon' => '🏖', 'label' => 'type_hotel_room'],
    ['type' => 'commercial', 'icon' => '🏪', 'label' => 'type_commercial'],
];

?>
<section class="hero">
    <div class="hero__bg" aria-hidden="true">
        <div class="hero__bg-grid">
            <div class="hero__bg-cell hero__bg-cell--main" style="background-image:url('<?= Helpers::e($heroImgs[0]) ?>')"></div>
            <div class="hero__bg-cell" style="background-image:url('<?= Helpers::e($heroImgs[1]) ?>')"></div>
            <div class="hero__bg-cell" style="background-image:url('<?= Helpers::e($heroImgs[2]) ?>')"></div>
            <div class="hero__bg-cell" style="background-image:url('<?= Helpers::e($heroImgs[3]) ?>')"></div>
        </div>
        <div class="hero__overlay"></div>
    </div>
    <div class="hero__content">
        <p class="hero__eyebrow"><?= Helpers::e(Helpers::__('home_hero_eyebrow')) ?></p>
        <h1 class="hero__title"><?= Helpers::__('home_hero_title') ?></h1>
        <p class="hero__subtitle"><?= Helpers::e(Helpers::__('home_hero_lead')) ?></p>

        <div class="hero__search-card">
            <?php View::partial('search-bar', ['variant' => 'hero', 'deal' => $heroDeal]); ?>
        </div>

        <div class="hero__scroll" aria-hidden="true">
            <span class="hero__scroll-icon">↓</span>
            <span><?= Helpers::e(Helpers::__('nav_listings')) ?></span>
        </div>
    </div>
</section>

<section class="trust-bar">
    <div class="container trust-bar__grid">
        <div class="trust-bar__item stat-pill" data-count="<?= (int) $stats['listings'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">🏠</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_listings')) ?></span>
        </div>
        <div class="trust-bar__item stat-pill" data-count="<?= (int) $stats['sea'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">🌊</span>
            <span class="stat-pill__value"><span class="js-count">0</span></span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_sea')) ?></span>
        </div>
        <div class="trust-bar__item stat-pill" data-count="<?= (int) $stats['users'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">👤</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_users')) ?></span>
        </div>
        <div class="trust-bar__item stat-pill" data-count="<?= (int) $stats['sold'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">✅</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_sold')) ?></span>
        </div>
    </div>
</section>

<section class="cat-shortcuts section" aria-labelledby="cat-shortcuts-title">
    <div class="container">
        <h2 id="cat-shortcuts-title" class="section__title"><?= Helpers::e(Helpers::__('filter_type')) ?></h2>
        <div class="cat-shortcuts__track">
            <?php foreach ($catTypes as $c):
                $href = rtrim(BASE_URL, '/') . '/listings?deal=sale&type=' . rawurlencode($c['type']);
                ?>
                <a class="cat-pill reveal" href="<?= Helpers::e($href) ?>">
                    <span class="cat-pill__icon" aria-hidden="true"><?= Helpers::e($c['icon']) ?></span>
                    <span class="cat-pill__label"><?= Helpers::e(Helpers::__($c['label'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head">
            <div>
                <h2 class="section__title"><?= Helpers::e(Helpers::__('section_featured')) ?></h2>
                <p class="section__subtitle"><?= Helpers::e(Helpers::__('section_featured_subtitle')) ?></p>
            </div>
            <a class="section__link" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('btn_view_all')) ?> →</a>
        </div>
        <div class="grid grid--4 reveal-stagger">
            <?php foreach ($featured as $property): ?>
                <?php View::partial('property-card', ['property' => $property]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-map-split">
    <div class="container">
        <div class="home-map-split__grid">
            <div>
                <h2 class="section__title"><?= Helpers::e(Helpers::__('home_map_title')) ?></h2>
                <?php if ($mapsKey !== '' && $mapMarkers !== []): ?>
                    <div id="home-map-canvas" class="home-map-split__map home-map-canvas"></div>
                <?php else: ?>
                    <div class="home-map-split__map">
                        <iframe
                            title="Kobuleti map"
                            loading="lazy"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11915.5!2d41.774!3d41.822!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4067dc2b8c3b3b3b%3A0x0!2zNDHCsDQ5JzE5LjIiTiA0McKwNDYnMjYuNCJF!5e0!3m2!1sen!2sge!4v1700000000000!5m2!1sen!2sge"
                            width="100%"
                            height="380"
                            style="border:0;"
                            allowfullscreen=""
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h2 class="section__title"><?= Helpers::e(Helpers::__('home_newest_title')) ?></h2>
                <div class="home-map-split__list">
                    <?php foreach ($newestListings as $property):
                        $href = rtrim(BASE_URL, '/') . '/listings/' . rawurlencode((string) ($property['slug'] ?? ''));
                        $img = !empty($property['main_image'])
                            ? Image::getImageUrl((string) $property['main_image'], 'thumb')
                            : '';
                        ?>
                        <a class="compact-card" href="<?= Helpers::e($href) ?>">
                            <?php if ($img !== ''): ?>
                                <img class="compact-card__img" src="<?= Helpers::e($img) ?>" alt="" loading="lazy" width="96" height="72">
                            <?php else: ?>
                                <div class="compact-card__img property-card__media--placeholder" style="min-width:96px">📷</div>
                            <?php endif; ?>
                            <div>
                                <div class="compact-card__title"><?= Helpers::e((string) ($property['title'] ?? '')) ?></div>
                                <div class="compact-card__meta"><?= Helpers::e(Helpers::formatPropertyPrice($property)) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p style="margin-top:var(--space-4)">
                    <a class="section__link" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('home_newest_more')) ?> →</a>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <h2 class="section__title" style="text-align:center;margin-bottom:var(--space-10)"><?= Helpers::e(Helpers::__('section_how_title')) ?></h2>
        <div class="how-grid">
            <div class="how-card reveal">
                <div class="how-card__icon" aria-hidden="true">👤</div>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step1_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step1_text')) ?></p>
            </div>
            <div class="how-card reveal">
                <div class="how-card__icon" aria-hidden="true">📝</div>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step2_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step2_text')) ?></p>
            </div>
            <div class="how-card reveal">
                <div class="how-card__icon" aria-hidden="true">✅</div>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step3_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step3_text')) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section kob-teaser">
    <div class="container kob-teaser__grid">
        <div class="kob-teaser__visual" style="background-image:url('https://images.unsplash.com/photo-1505142468610-359e7d316be0?w=900&q=80')" role="presentation"></div>
        <div>
            <p class="kob-teaser__eyebrow"><?= Helpers::e(Helpers::__('kob_teaser_eyebrow')) ?></p>
            <h2 class="section__title"><?= Helpers::e(Helpers::__('kob_teaser_headline')) ?></h2>
            <p class="kob-teaser__text"><?= Helpers::e(Helpers::__('kob_teaser_text')) ?></p>
            <a class="kob-teaser__link btn btn--secondary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/kobuleti">
                <?= Helpers::e(Helpers::__('kob_teaser_cta')) ?> <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section__title"><?= Helpers::e(Helpers::__('nav_listings')) ?></h2>
        <div class="masonry-grid grid grid--4">
            <?php foreach ($featured as $property): ?>
                <?php View::partial('property-card', ['property' => $property]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section cta-bottom">
    <div class="container cta-bottom__inner">
        <h2 class="cta-bottom__title"><?= Helpers::e(Helpers::__('cta_listings_title')) ?></h2>
        <a class="btn btn--primary btn--pill btn--lg" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
    </div>
</section>

<a class="listing-fab" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create" title="<?= Helpers::e(Helpers::__('nav_add_listing')) ?>">+</a>
