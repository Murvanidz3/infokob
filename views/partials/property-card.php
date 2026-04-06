<?php

declare(strict_types=1);

/** @var array<string, mixed> $property */
$href = BASE_URL . '/listings/' . rawurlencode((string) $property['slug']);
$img = !empty($property['main_image'])
    ? Image::getImageUrl((string) $property['main_image'], 'thumb')
    : Helpers::asset('img/placeholder.svg');
$deal = Helpers::propertyDealLabel((string) ($property['deal_type'] ?? 'sale'));
$price = Helpers::formatPropertyPrice($property);
$title = (string) ($property['title'] ?? '');
$rooms = $property['rooms'] ?? null;
$area = $property['area_m2'] ?? null;
$sea = $property['sea_distance_m'] ?? null;
$district = (string) ($property['district'] ?? '');
$created = Helpers::timeAgo($property['created_at'] ?? null);
$fu = $property['featured_until'] ?? null;
$featured = !empty($property['is_featured'])
    && ($fu === null || $fu === '' || strtotime((string) $fu) > time());
?>
<article class="property-card">
    <a class="property-card__media" href="<?= Helpers::e($href) ?>">
        <img src="<?= Helpers::e($img) ?>" alt="" loading="lazy" width="400" height="300">
        <span class="property-card__badge property-card__badge--deal"><?= Helpers::e($deal) ?></span>
        <?php if ($featured): ?>
            <span class="property-card__badge property-card__badge--star" title="<?= Helpers::e(Helpers::__('featured_badge')) ?>">⭐</span>
        <?php endif; ?>
    </a>
    <div class="property-card__body">
        <a class="property-card__price" href="<?= Helpers::e($href) ?>"><?= Helpers::e($price) ?></a>
        <h3 class="property-card__title"><a href="<?= Helpers::e($href) ?>"><?= Helpers::e($title) ?></a></h3>
        <div class="property-card__stats">
            <?php if ($rooms !== null && $rooms !== ''): ?>
                <span><i class="ph ph-bed" aria-hidden="true"></i> <?= Helpers::e((string) $rooms) ?></span>
            <?php endif; ?>
            <?php if ($area !== null && $area !== ''): ?>
                <span><i class="ph ph-ruler" aria-hidden="true"></i> <?= Helpers::e((string) $area) ?> m²</span>
            <?php endif; ?>
            <?php if ($sea !== null && $sea !== ''): ?>
                <span><i class="ph ph-waves" aria-hidden="true"></i> <?= Helpers::e((string) $sea) ?> m</span>
            <?php endif; ?>
        </div>
        <?php if ($district !== ''): ?>
            <p class="property-card__loc"><i class="ph ph-map-pin" aria-hidden="true"></i> <?= Helpers::e($district) ?></p>
        <?php endif; ?>
        <?php if ($created !== ''): ?>
            <p class="property-card__time"><?= Helpers::e($created) ?></p>
        <?php endif; ?>
    </div>
</article>
