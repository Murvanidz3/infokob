<?php

declare(strict_types=1);

/** @var array<string, int> $byStatus */
/** @var int $userCount */
/** @var int $featuredActive */
/** @var int $featuredExpiredCleared */
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('admin_dashboard_title')) ?></h1>
<p class="admin-page__lead"><?= Helpers::e(Helpers::__('admin_dashboard_lead')) ?></p>

<p class="admin-featured-summary">
    <?= Helpers::e(Helpers::__('admin_dashboard_featured_line', [
        'active' => (string) (int) ($featuredActive ?? 0),
        'cleared' => (string) (int) ($featuredExpiredCleared ?? 0),
    ])) ?>
</p>

<div class="admin-stats">
    <a class="admin-stat admin-stat--link" href="<?= Helpers::e(BASE_URL) ?>/properties?status=pending">
        <span class="admin-stat__val"><?= (int) ($byStatus['pending'] ?? 0) ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('status_pending')) ?></span>
    </a>
    <a class="admin-stat admin-stat--link" href="<?= Helpers::e(BASE_URL) ?>/properties?status=active">
        <span class="admin-stat__val"><?= (int) ($byStatus['active'] ?? 0) ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('status_active')) ?></span>
    </a>
    <a class="admin-stat admin-stat--link" href="<?= Helpers::e(BASE_URL) ?>/properties?status=rejected">
        <span class="admin-stat__val"><?= (int) ($byStatus['rejected'] ?? 0) ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('status_rejected')) ?></span>
    </a>
    <div class="admin-stat">
        <span class="admin-stat__val"><?= (int) ($byStatus['sold'] ?? 0) ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('status_sold')) ?></span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat__val"><?= (int) ($byStatus['archived'] ?? 0) ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('status_archived')) ?></span>
    </div>
    <a class="admin-stat admin-stat--link" href="<?= Helpers::e(BASE_URL) ?>/users">
        <span class="admin-stat__val"><?= (int) $userCount ?></span>
        <span class="admin-stat__lbl"><?= Helpers::e(Helpers::__('admin_stat_users')) ?></span>
    </a>
</div>

<p><a class="btn btn--primary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/properties"><?= Helpers::e(Helpers::__('admin_open_moderation')) ?></a></p>
