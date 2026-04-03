<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array<string, mixed>> $featured */
/** @var array{listings:int,sea:int,users:int,sold:int} $stats */
/** @var string $heroDeal */
$lang = Language::get();
?>
<section class="hero" style="--hero-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80');">
    <div class="hero__overlay"></div>
    <div class="container hero__content">
        <h1 class="hero__title"><?= Helpers::e(Helpers::__('hero_title')) ?></h1>
        <p class="hero__subtitle"><?= Helpers::e(Helpers::__('hero_subtitle')) ?></p>
        <?php View::partial('search-bar', ['variant' => 'hero', 'deal' => $heroDeal]); ?>
    </div>
</section>

<section class="stats-bar">
    <div class="container stats-bar__grid">
        <div class="stat-pill" data-count="<?= (int) $stats['listings'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">🏠</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_listings')) ?></span>
        </div>
        <div class="stat-pill" data-count="<?= (int) $stats['sea'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">🌊</span>
            <span class="stat-pill__value"><span class="js-count">0</span></span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_sea')) ?></span>
        </div>
        <div class="stat-pill" data-count="<?= (int) $stats['users'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">👤</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_users')) ?></span>
        </div>
        <div class="stat-pill" data-count="<?= (int) $stats['sold'] ?>">
            <span class="stat-pill__icon" aria-hidden="true">✅</span>
            <span class="stat-pill__value"><span class="js-count">0</span>+</span>
            <span class="stat-pill__label"><?= Helpers::e(Helpers::__('stat_sold')) ?></span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title"><?= Helpers::e(Helpers::__('section_featured')) ?></h2>
            <a class="section__link" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('btn_view_all')) ?> →</a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($featured as $property): ?>
                <?php View::partial('property-card', ['property' => $property]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <h2 class="section__title"><?= Helpers::e(Helpers::__('section_how_title')) ?></h2>
        <div class="how-grid">
            <div class="how-card">
                <span class="how-card__step">1</span>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step1_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step1_text')) ?></p>
            </div>
            <div class="how-card">
                <span class="how-card__step">2</span>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step2_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step2_text')) ?></p>
            </div>
            <div class="how-card">
                <span class="how-card__step">3</span>
                <h3 class="how-card__title"><?= Helpers::e(Helpers::__('how_step3_title')) ?></h3>
                <p class="how-card__text"><?= Helpers::e(Helpers::__('how_step3_text')) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section kob-teaser">
    <div class="container kob-teaser__grid">
        <div>
            <h2 class="section__title"><?= Helpers::e(Helpers::__('kob_teaser_title')) ?></h2>
            <p class="kob-teaser__text"><?= Helpers::e(Helpers::__('kob_teaser_text')) ?></p>
            <a class="btn btn--secondary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('kob_teaser_cta')) ?></a>
        </div>
        <div class="kob-teaser__visual" aria-hidden="true"></div>
    </div>
</section>

<section class="section cta-bottom">
    <div class="container cta-bottom__inner">
        <h2 class="cta-bottom__title"><?= Helpers::e(Helpers::__('cta_listings_title')) ?></h2>
        <a class="btn btn--primary btn--pill btn--lg" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
    </div>
</section>
