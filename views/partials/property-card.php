<?php

declare(strict_types=1);

/** @var array<string, mixed> $property */
$href = BASE_URL . '/listings/' . rawurlencode((string) $property['slug']);
$img = !empty($property['main_image'])
    ? Image::getImageUrl((string) $property['main_image'], 'thumb')
    : '';
$dealType = (string) ($property['deal_type'] ?? 'sale');
$deal = Helpers::propertyDealLabel($dealType);
$dealClass = match ($dealType) {
    'rent' => 'property-card__badge--deal-rent',
    'daily_rent' => 'property-card__badge--deal-daily',
    default => 'property-card__badge--deal-sale',
};
$price = Helpers::formatPropertyPrice($property);
$title = (string) ($property['title'] ?? '');
$rooms = $property['rooms'] ?? null;
$area = $property['area_m2'] ?? null;
$sea = $property['sea_distance_m'] ?? null;
$district = (string) ($property['district'] ?? '');
$city = Helpers::__('nav_kobuleti');
$created = Helpers::timeAgo($property['created_at'] ?? null);
$fu = $property['featured_until'] ?? null;
$featured = !empty($property['is_featured'])
    && ($fu === null || $fu === '' || strtotime((string) $fu) > time());
$pid = (int) ($property['id'] ?? 0);
$locLine = $district !== '' ? $city . ', ' . $district : $city;
if ($sea !== null && $sea !== '') {
    $locLine .= ' · 🌊 ' . (string) $sea . 'მ ზღვ.';
}
?>
<article
    class="property-card reveal"
    x-data="{ saved: false, id: <?= $pid ?> }"
    x-init="if (id) { saved = localStorage.getItem('saved_' + id) === '1'; }"
>
    <div class="property-card__media-wrap">
        <a class="property-card__media <?= $img === '' ? 'property-card__media--placeholder' : '' ?>" href="<?= Helpers::e($href) ?>">
            <?php if ($img !== ''): ?>
                <img src="<?= Helpers::e($img) ?>" alt="" loading="lazy" width="400" height="300">
            <?php else: ?>
                <span><?= Helpers::e(Helpers::__('card_no_photo')) ?></span>
            <?php endif; ?>
        </a>
        <div class="property-card__badges">
            <span class="property-card__badge <?= Helpers::e($dealClass) ?>"><?= Helpers::e($deal) ?></span>
            <?php if ($featured): ?>
                <span class="property-card__badge property-card__badge--featured" title="<?= Helpers::e(Helpers::__('featured_badge')) ?>">⭐</span>
            <?php endif; ?>
        </div>
        <button
            type="button"
            class="property-card__save"
            :class="{ 'is-saved': saved }"
            @click.prevent="saved = !saved; if(id) localStorage.setItem('saved_'+id, saved ? '1' : '0')"
            aria-label="Save"
        >
            <span x-show="!saved">🤍</span>
            <span x-show="saved">❤</span>
        </button>
    </div>
    <div class="property-card__body">
        <p class="property-card__loc"><?= Helpers::e($locLine) ?></p>
        <h3 class="property-card__title">
            <a href="<?= Helpers::e($href) ?>"><?= Helpers::e($title) ?></a>
        </h3>
        <div class="property-card__stats">
            <?php if ($rooms !== null && $rooms !== ''): ?>
                <span><i class="ph ph-bed" aria-hidden="true"></i> <?= Helpers::e((string) $rooms) ?></span>
            <?php endif; ?>
            <?php if ($area !== null && $area !== ''): ?>
                <span><i class="ph ph-ruler" aria-hidden="true"></i> <?= Helpers::e((string) $area) ?> м²</span>
            <?php endif; ?>
            <?php
            $floorN = $property['floor_number'] ?? null;
            $floorsT = $property['floors_total'] ?? null;
            if ($floorN !== null && $floorsT !== null):
                ?>
                <span><i class="ph ph-buildings" aria-hidden="true"></i> <?= Helpers::e((string) $floorN) ?>/<?= Helpers::e((string) $floorsT) ?></span>
            <?php endif; ?>
        </div>
        <div class="property-card__footer">
            <span class="property-card__price"><?= Helpers::e($price) ?></span>
            <?php if ($created !== ''): ?>
                <span class="property-card__time"><?= Helpers::e($created) ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
