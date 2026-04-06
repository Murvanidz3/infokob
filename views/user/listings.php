<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array<string, mixed>> $listings */
/** @var string $featuredPriceGel */
/** @var string $featuredDurationDays */
/** @var string $contactPhone */

$priceGel = $featuredPriceGel ?? '25';
$days = $featuredDurationDays ?? '30';
$adminPhone = $contactPhone ?? '';
?>
<div
    class="user-page"
    x-data="{ pmOpen: false, pmTitle: '' }"
    @keydown.escape.window="pmOpen = false"
>
    <div class="user-section-head">
        <h1 class="user-page__title"><?= Helpers::e(Helpers::__('user_nav_listings')) ?></h1>
        <a class="btn btn--primary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th></th>
                    <th><?= Helpers::e(Helpers::__('user_col_title')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_type')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_price')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_status')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_views')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_premium')) ?></th>
                    <th><?= Helpers::e(Helpers::__('user_col_actions')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $row): ?>
                    <?php
                    $thumb = !empty($row['main_image']) ? Image::getImageUrl((string) $row['main_image'], 'thumb') : Helpers::asset('img/placeholder.svg');
                    $st = (string) ($row['status'] ?? '');
                    $statusKey = 'status_' . $st;
                    $statusLabel = Helpers::__($statusKey);
                    $titleJs = json_encode((string) ($row['title'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                    $fu = $row['featured_until'] ?? null;
                    $featuredValid = !empty($row['is_featured'])
                        && ($fu === null || $fu === '' || strtotime((string) $fu) > time());
                    ?>
                    <tr>
                        <td><img class="data-table__thumb" src="<?= Helpers::e($thumb) ?>" alt="" width="48" height="36" loading="lazy"></td>
                        <td><a href="<?= Helpers::e(BASE_URL) ?>/listings/<?= Helpers::e((string) $row['slug']) ?>"><?= Helpers::e((string) ($row['title'] ?? '')) ?></a></td>
                        <td><?= Helpers::e(Helpers::propertyTypeLabel((string) ($row['type'] ?? ''))) ?></td>
                        <td><?= Helpers::e(Helpers::formatPropertyPrice($row)) ?></td>
                        <td><span class="badge badge--<?= Helpers::e($st) ?>"><?= Helpers::e($statusLabel) ?></span></td>
                        <td><?= Helpers::e((string) (int) ($row['views'] ?? 0)) ?></td>
                        <td class="data-table__premium">
                            <?php if ($st === 'active'): ?>
                                <?php if ($featuredValid): ?>
                                    <span class="premium-badge" title="<?= Helpers::e(Helpers::__('premium_badge_title')) ?>">⭐</span>
                                    <button type="button" class="btn btn--accent btn--sm btn--pill" @click="pmOpen = true; pmTitle = <?= $titleJs ?>"><?= Helpers::e(Helpers::__('premium_btn_renew')) ?></button>
                                <?php else: ?>
                                    <button type="button" class="btn btn--accent btn--sm btn--pill" @click="pmOpen = true; pmTitle = <?= $titleJs ?>"><?= Helpers::e(Helpers::__('premium_cta')) ?> — <?= Helpers::e($priceGel) ?>₾</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="muted-dash">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="data-table__actions">
                            <a class="btn btn--ghost btn--sm" href="<?= Helpers::e(BASE_URL) ?>/my/listings/<?= (int) $row['id'] ?>/edit"><?= Helpers::e(Helpers::__('user_action_edit')) ?></a>
                            <?php if ($st === 'active'): ?>
                                <form method="post" action="<?= Helpers::e(BASE_URL) ?>/my/listings/<?= (int) $row['id'] ?>/sold" style="display:inline;">
                                    <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                    <button type="submit" class="btn btn--ghost btn--sm"><?= Helpers::e(Helpers::__('user_action_sold')) ?></button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="<?= Helpers::e(BASE_URL) ?>/my/listings/<?= (int) $row['id'] ?>/archive" style="display:inline;" onsubmit="return confirm('<?= Helpers::e(Helpers::__('user_confirm_archive')) ?>');">
                                <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                <button type="submit" class="btn btn--ghost btn--sm"><?= Helpers::e(Helpers::__('user_action_archive')) ?></button>
                            </form>
                            <form method="post" action="<?= Helpers::e(BASE_URL) ?>/my/listings/<?= (int) $row['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('<?= Helpers::e(Helpers::__('user_confirm_delete')) ?>');">
                                <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                <button type="submit" class="btn btn--ghost btn--sm"><?= Helpers::e(Helpers::__('user_action_delete')) ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($listings === []): ?>
        <p class="empty-state"><?= Helpers::e(Helpers::__('user_no_listings')) ?></p>
    <?php endif; ?>

    <!-- Premium info modal (manual payment — no gateway) -->
    <div class="premium-modal" x-show="pmOpen" x-cloak x-transition.opacity @click.self="pmOpen = false" role="dialog" aria-modal="true" :aria-hidden="!pmOpen">
        <div class="premium-modal__panel" @click.stop>
            <h2 class="premium-modal__title"><?= Helpers::e(Helpers::__('premium_modal_title')) ?></h2>
            <p class="premium-modal__lead"><?= Helpers::e(Helpers::__('premium_modal_sub', ['days' => $days, 'price' => $priceGel])) ?></p>
            <p class="premium-modal__listing" x-text="pmTitle"></p>
            <ul class="premium-modal__list">
                <li><?= Helpers::e(Helpers::__('premium_modal_b1')) ?></li>
                <li><?= Helpers::e(Helpers::__('premium_modal_b2')) ?></li>
                <li><?= Helpers::e(Helpers::__('premium_modal_b3')) ?></li>
            </ul>
            <p class="premium-modal__pay"><?= Helpers::e(Helpers::__('premium_modal_pay_title')) ?></p>
            <p class="premium-modal__banks"><?= Helpers::e(Helpers::__('premium_modal_banks')) ?></p>
            <?php if ($adminPhone !== ''): ?>
                <p class="premium-modal__phone"><?= Helpers::e(Helpers::__('premium_modal_phone_note', ['phone' => $adminPhone])) ?></p>
            <?php endif; ?>
            <p class="premium-modal__disclaimer"><?= Helpers::e(Helpers::__('premium_modal_disclaimer')) ?></p>
            <button type="button" class="btn btn--primary btn--pill btn--block premium-modal__ok" @click="pmOpen = false"><?= Helpers::e(Helpers::__('premium_modal_ok')) ?></button>
        </div>
    </div>
</div>
