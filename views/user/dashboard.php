<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var array{total:int,active:int,pending:int,rejected:int,views:int} $stats */
/** @var list<array<string, mixed>> $recent */
?>
<div class="user-page">
    <h1 class="user-page__title"><?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></h1>

    <div class="user-stats">
        <div class="user-stat"><span class="user-stat__val"><?= (int) $stats['active'] ?></span><span class="user-stat__lbl"><?= Helpers::e(Helpers::__('status_active')) ?></span></div>
        <div class="user-stat"><span class="user-stat__val"><?= (int) $stats['pending'] ?></span><span class="user-stat__lbl"><?= Helpers::e(Helpers::__('status_pending')) ?></span></div>
        <div class="user-stat"><span class="user-stat__val"><?= (int) $stats['rejected'] ?></span><span class="user-stat__lbl"><?= Helpers::e(Helpers::__('status_rejected')) ?></span></div>
        <div class="user-stat"><span class="user-stat__val"><?= (int) $stats['views'] ?></span><span class="user-stat__lbl"><?= Helpers::e(Helpers::__('user_total_views')) ?></span></div>
    </div>

    <div class="user-section-head">
        <h2 class="user-section-head__title"><?= Helpers::e(Helpers::__('user_recent_listings')) ?></h2>
        <a class="btn btn--secondary btn--sm btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings"><?= Helpers::e(Helpers::__('user_view_all')) ?></a>
    </div>

    <div class="grid grid--4">
        <?php foreach ($recent as $property): ?>
            <?php View::partial('property-card', ['property' => $property]); ?>
        <?php endforeach; ?>
    </div>
    <?php if ($recent === []): ?>
        <p class="empty-state"><?= Helpers::e(Helpers::__('user_no_listings')) ?></p>
        <p><a class="btn btn--primary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><?= Helpers::e(Helpers::__('nav_add_listing')) ?></a></p>
    <?php endif; ?>
</div>
