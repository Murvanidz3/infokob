<?php
/**
 * Kobuleti hotels — dedicated listing page (card grid)
 */
$hotelIds = $hotelIds ?? [1, 2, 3, 4, 5, 6];
?>

<section class="hotels-hero">
    <div class="container">
        <h1><?= __('menu_hotels') ?></h1>
        <p class="hotels-hero-sub"><?= __('hotels_intro') ?></p>
    </div>
</section>

<div class="container hotels-page-body">
    <div class="hotels-grid">
        <?php foreach ($hotelIds as $id): ?>
        <article class="hotel-card">
            <div class="hotel-card-image">
                <div class="hotel-card-image-inner">
                    <i class="ph ph-bed"></i>
                </div>
                <?php if ($stars = __('hotel_' . $id . '_stars')): ?>
                <span class="hotel-card-stars" aria-hidden="true"><?= e($stars) ?></span>
                <?php endif; ?>
            </div>
            <div class="hotel-card-body">
                <h2 class="hotel-card-title"><?= e(__('hotel_' . $id . '_name')) ?></h2>
                <p class="hotel-card-area"><i class="ph ph-map-pin"></i> <?= e(__('hotel_' . $id . '_area')) ?></p>
                <p class="hotel-card-desc"><?= e(__('hotel_' . $id . '_desc')) ?></p>
                <a href="<?= BASE_URL ?>/contact" class="btn btn-outline btn-sm hotel-card-cta">
                    <?= __('hotels_card_cta') ?>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>
