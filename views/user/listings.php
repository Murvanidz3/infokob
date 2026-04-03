<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array<string, mixed>> $listings */
?>
<div class="user-page">
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
                    ?>
                    <tr>
                        <td><img class="data-table__thumb" src="<?= Helpers::e($thumb) ?>" alt="" width="48" height="36" loading="lazy"></td>
                        <td><a href="<?= Helpers::e(BASE_URL) ?>/listings/<?= Helpers::e((string) $row['slug']) ?>"><?= Helpers::e((string) ($row['title'] ?? '')) ?></a></td>
                        <td><?= Helpers::e(Helpers::propertyTypeLabel((string) ($row['type'] ?? ''))) ?></td>
                        <td><?= Helpers::e(Helpers::formatPropertyPrice($row)) ?></td>
                        <td><span class="badge badge--<?= Helpers::e($st) ?>"><?= Helpers::e($statusLabel) ?></span></td>
                        <td><?= Helpers::e((string) (int) ($row['views'] ?? 0)) ?></td>
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
</div>
