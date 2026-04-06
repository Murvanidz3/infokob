<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var array<string, mixed> $property */
/** @var list<array<string, mixed>> $images */
/** @var list<array<string, mixed>> $similar */
/** @var string $mapsKey */
/** @var float|string|null $lat */
/** @var mixed $lng */
/** @var string $whatsapp */
/** @var string $telegram */
/** @var string $phone */

$p = $property;
$title = (string) ($p['title'] ?? '');
$mainFile = (string) ($p['main_image'] ?? '');
$gallery = $images;
if ($mainFile === '' && $gallery !== []) {
    $mainFile = (string) ($gallery[0]['filename'] ?? '');
}
$mainUrl = $mainFile !== '' ? Image::getImageUrl($mainFile, 'original') : Helpers::asset('img/placeholder.svg');

$email = (string) ($p['contact_email'] ?? $p['user_email'] ?? '');
$name = (string) ($p['contact_name'] ?? $p['user_name'] ?? '');

$waDigits = Helpers::digitsOnly($whatsapp);
$waText = rawurlencode(Helpers::__('wa_prefill'));
$waLink = $waDigits !== '' ? 'https://wa.me/' . $waDigits . '?text=' . $waText : '';

$tgUser = ltrim(trim($telegram), '@');
$tgLink = $tgUser !== '' ? 'https://t.me/' . rawurlencode($tgUser) : '';

$priceLine = Helpers::formatPropertyPrice($p);
$dealLabel = Helpers::propertyDealLabel((string) ($p['deal_type'] ?? 'sale'));
$typeLabel = Helpers::propertyTypeLabel((string) ($p['type'] ?? 'apartment'));

$features = [];
if (!empty($p['has_pool'])) {
    $features[] = Helpers::__('feat_pool');
}
if (!empty($p['has_garden'])) {
    $features[] = Helpers::__('feat_garden');
}
if (!empty($p['has_balcony'])) {
    $features[] = Helpers::__('feat_balcony');
}
if (!empty($p['has_garage'])) {
    $features[] = Helpers::__('feat_garage');
}

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $title,
    'description' => strip_tags((string) ($p['description'] ?? '')),
    'offers' => [
        '@type' => 'Offer',
        'price' => $p['price'] ?? 0,
        'priceCurrency' => (string) ($p['currency'] ?? 'USD'),
    ],
];

$extraScripts = '';
if ($mapsKey !== '' && $lat !== null && $lng !== null) {
    $latJs = json_encode((float) $lat);
    $lngJs = json_encode((float) $lng);
    $keyJs = json_encode($mapsKey);
    $extraScripts .= '<script>function initPropertyMap(){var c={lat:' . $latJs . ',lng:' . $lngJs . '};var el=document.getElementById("property-map");if(!el||typeof google==="undefined")return;var m=new google.maps.Map(el,{zoom:15,center:c,styles:[]});new google.maps.Marker({position:c,map:m});}</script>';
    $extraScripts .= '<script defer src="https://maps.googleapis.com/maps/api/js?key=' . Helpers::e($mapsKey) . '&callback=initPropertyMap"></script>';
}
?>
<div class="container property-single">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= Helpers::e(BASE_URL) ?>/"><?= Helpers::e(Helpers::__('nav_home')) ?></a>
        <span class="breadcrumb__sep">/</span>
        <a href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
        <span class="breadcrumb__sep">/</span>
        <span><?= Helpers::e($title) ?></span>
    </nav>

    <div class="property-single__grid">
        <div class="property-single__main">
            <h1 class="property-single__title"><?= Helpers::e($title) ?></h1>

            <div class="property-gallery">
                <div class="property-gallery__main">
                    <img id="gallery-main" src="<?= Helpers::e($mainUrl) ?>" alt="<?= Helpers::e($title) ?>" loading="eager" width="900" height="600">
                </div>
                <?php if ($gallery !== []): ?>
                    <div class="property-gallery__thumbs">
                        <?php foreach ($gallery as $idx => $img): ?>
                            <?php
                            $fn = (string) ($img['filename'] ?? '');
                            $url = $fn !== '' ? Image::getImageUrl($fn, 'thumb') : Helpers::asset('img/placeholder.svg');
                            $full = $fn !== '' ? Image::getImageUrl($fn, 'original') : $url;
                            ?>
                            <button type="button" class="property-gallery__thumb<?= $idx === 0 ? ' is-active' : '' ?>"
                                    data-full="<?= Helpers::e($full) ?>"
                                    aria-label="Photo <?= (int) ($idx + 1) ?>">
                                <img src="<?= Helpers::e($url) ?>" alt="" loading="lazy" width="120" height="90">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="spec-grid">
                <?php if (($p['rooms'] ?? null) !== null && $p['rooms'] !== ''): ?>
                    <div class="spec-item"><i class="ph ph-bed"></i><span><?= Helpers::e(Helpers::__('spec_rooms')) ?></span><strong><?= Helpers::e((string) $p['rooms']) ?></strong></div>
                <?php endif; ?>
                <?php if (($p['area_m2'] ?? null) !== null && $p['area_m2'] !== ''): ?>
                    <div class="spec-item"><i class="ph ph-ruler"></i><span><?= Helpers::e(Helpers::__('spec_area')) ?></span><strong><?= Helpers::e((string) $p['area_m2']) ?> m²</strong></div>
                <?php endif; ?>
                <?php if (($p['floors_total'] ?? null) !== null): ?>
                    <div class="spec-item"><i class="ph ph-buildings"></i><span><?= Helpers::e(Helpers::__('spec_floors')) ?></span><strong><?= Helpers::e((string) ($p['floor_number'] ?? '—')) ?> / <?= Helpers::e((string) $p['floors_total']) ?></strong></div>
                <?php endif; ?>
                <?php if (($p['sea_distance_m'] ?? null) !== null): ?>
                    <div class="spec-item"><i class="ph ph-waves"></i><span><?= Helpers::e(Helpers::__('spec_sea')) ?></span><strong><?= Helpers::e((string) $p['sea_distance_m']) ?> m</strong></div>
                <?php endif; ?>
            </div>

            <div class="property-body">
                <p class="property-meta-line"><?= Helpers::e($typeLabel) ?> · <?= Helpers::e($dealLabel) ?><?php if (($p['district'] ?? '') !== ''): ?> · <?= Helpers::e((string) $p['district']) ?><?php endif; ?></p>
                <div class="property-description rte"><?= nl2br(Helpers::e((string) ($p['description'] ?? ''))) ?></div>
                <?php if ($features !== []): ?>
                    <div class="tag-row">
                        <?php foreach ($features as $tag): ?>
                            <span class="tag-pill"><?= Helpers::e($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($mapsKey !== '' && $lat !== null && $lng !== null): ?>
                <section class="property-map-section" aria-label="Map">
                    <h2 class="section__title"><?= Helpers::e(Helpers::__('map_title')) ?></h2>
                    <div id="property-map" class="property-map"></div>
                </section>
            <?php endif; ?>

            <script type="application/ld+json"><?= Helpers::e(json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></script>
        </div>

        <aside class="property-single__aside">
            <div class="contact-card">
                <div class="contact-card__price"><?= Helpers::e($priceLine) ?></div>
                <p class="contact-card__summary"><?= Helpers::e($typeLabel) ?><?php if (($p['area_m2'] ?? null) !== null): ?> | <?= Helpers::e((string) $p['area_m2']) ?> m²<?php endif; ?><?php if (($p['district'] ?? '') !== ''): ?> | <?= Helpers::e((string) $p['district']) ?><?php endif; ?></p>
                <hr class="contact-card__hr">
                <p class="contact-card__name"><i class="ph ph-user"></i> <?= Helpers::e($name) ?></p>
                <?php if ($waLink !== ''): ?>
                    <a class="btn btn--whatsapp btn--pill btn--block" href="<?= Helpers::e($waLink) ?>" rel="noopener noreferrer" target="_blank"><?= Helpers::e(Helpers::__('btn_whatsapp')) ?></a>
                <?php endif; ?>
                <?php if ($tgLink !== ''): ?>
                    <a class="btn btn--telegram btn--pill btn--block" href="<?= Helpers::e($tgLink) ?>" rel="noopener noreferrer" target="_blank"><?= Helpers::e(Helpers::__('btn_telegram')) ?></a>
                <?php endif; ?>
                <?php if ($phone !== ''): ?>
                    <a class="btn btn--outline btn--pill btn--block" href="tel:<?= Helpers::e(preg_replace('/\s+/', '', $phone)) ?>"><?= Helpers::e(Helpers::__('btn_call')) ?> · <?= Helpers::e($phone) ?></a>
                <?php endif; ?>
                <?php if ($email !== ''): ?>
                    <a class="btn btn--ghost btn--pill btn--block" href="mailto:<?= Helpers::e($email) ?>"><?= Helpers::e($email) ?></a>
                <?php endif; ?>
                <hr class="contact-card__hr">
                <p class="contact-card__meta">
                    <span><i class="ph ph-eye"></i> <?= Helpers::e((string) (int) ($p['views'] ?? 0)) ?> <?= Helpers::e(Helpers::__('views_label')) ?></span>
                    <?php if (!empty($p['created_at'])): ?>
                        <span>· <?= Helpers::e(Helpers::timeAgo((string) $p['created_at'])) ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </aside>
    </div>

    <?php if ($similar !== []): ?>
        <section class="section">
            <div class="container">
                <h2 class="section__title"><?= Helpers::e(Helpers::__('similar_title')) ?></h2>
                <div class="grid grid--4">
                    <?php foreach ($similar as $property): ?>
                        <?php View::partial('property-card', ['property' => $property]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
(function(){
  var main = document.getElementById('gallery-main');
  document.querySelectorAll('.property-gallery__thumb').forEach(function(btn){
    btn.addEventListener('click', function(){
      var full = btn.getAttribute('data-full');
      if (main && full) main.src = full;
      document.querySelectorAll('.property-gallery__thumb').forEach(function(b){ b.classList.remove('is-active'); });
      btn.classList.add('is-active');
    });
  });
})();
</script>
