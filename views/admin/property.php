<?php

declare(strict_types=1);

/** @var array<string, mixed> $property */
/** @var list<array<string, mixed>> $images */
/** @var array<string, array{title:string,description:string}> $translations */
$p = $property;
$id = (int) $p['id'];
$slug = (string) ($p['slug'] ?? '');
$status = (string) ($p['status'] ?? '');
$publicUrl = PUBLIC_BASE_URL . '/listings/' . rawurlencode($slug);
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('admin_property_title')) ?> #<?= $id ?></h1>
<p class="admin-page__lead">
    <a href="<?= Helpers::e($publicUrl) ?>" target="_blank" rel="noopener noreferrer"><?= Helpers::e(Helpers::__('admin_view_public')) ?></a>
    · <a href="<?= Helpers::e(BASE_URL) ?>/properties"><?= Helpers::e(Helpers::__('admin_back_list')) ?></a>
</p>

<div class="admin-detail-grid">
    <div>
        <div class="admin-card">
            <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_detail_meta')) ?></h2>
            <dl class="admin-dl">
                <dt><?= Helpers::e(Helpers::__('user_col_status')) ?></dt>
                <dd><span class="admin-badge admin-badge--<?= Helpers::e($status) ?>"><?= Helpers::e(Helpers::__('status_' . $status)) ?></span></dd>
                <dt><?= Helpers::e(Helpers::__('filter_type')) ?></dt>
                <dd><?= Helpers::e(Helpers::__('type_' . (string) ($p['type'] ?? ''))) ?></dd>
                <dt><?= Helpers::e(Helpers::__('filter_deal')) ?></dt>
                <dd><?php
                    $deal = (string) ($p['deal_type'] ?? 'sale');
                    $dealKey = $deal === 'daily_rent' ? 'deal_daily' : 'deal_' . $deal;
                    echo Helpers::e(Helpers::__($dealKey));
                ?></dd>
                <dt><?= Helpers::e(Helpers::__('user_col_price')) ?></dt>
                <dd><?= Helpers::e(Helpers::formatPrice(isset($p['price']) ? (float) $p['price'] : null, (string) ($p['currency'] ?? 'USD'), !empty($p['price_negotiable']))) ?></dd>
                <dt><?= Helpers::e(Helpers::__('admin_col_owner')) ?></dt>
                <dd><?= Helpers::e((string) ($p['owner_name'] ?? '')) ?> &lt;<?= Helpers::e((string) ($p['owner_email'] ?? '')) ?>&gt;</dd>
                <dt><?= Helpers::e(Helpers::__('user_contact_phone_req')) ?></dt>
                <dd><?= Helpers::e((string) ($p['contact_phone'] ?? '—')) ?></dd>
                <dt><?= Helpers::e(Helpers::__('featured_badge')) ?></dt>
                <dd><?= !empty($p['is_featured']) ? Helpers::e(Helpers::__('admin_yes')) : Helpers::e(Helpers::__('admin_no')) ?>
                    <?php if (!empty($p['featured_until'])): ?>
                        (<?= Helpers::e((string) $p['featured_until']) ?>)
                    <?php endif; ?>
                </dd>
                <dt><?= Helpers::e(Helpers::__('views_label')) ?></dt>
                <dd><?= (int) ($p['views'] ?? 0) ?></dd>
            </dl>
        </div>

        <?php if ($status === 'rejected' && !empty($p['admin_note'])): ?>
            <div class="admin-card" style="margin-top:1rem;border-color:rgba(239,68,68,0.35)">
                <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_reject_note')) ?></h2>
                <p style="margin:0;color:#cbd5e1;white-space:pre-wrap"><?= Helpers::e((string) $p['admin_note']) ?></p>
            </div>
        <?php endif; ?>

        <div class="admin-card" style="margin-top:1rem">
            <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_translations')) ?></h2>
            <?php foreach (['ka', 'ru', 'en'] as $lc): ?>
                <?php $t = $translations[$lc] ?? ['title' => '', 'description' => '']; ?>
                <div class="admin-lang-block">
                    <h4><?= Helpers::e(strtoupper($lc)) ?></h4>
                    <p><strong><?= Helpers::e($t['title']) ?></strong></p>
                    <p><?= Helpers::e($t['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($images !== []): ?>
            <div class="admin-card" style="margin-top:1rem">
                <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('user_step3')) ?></h2>
                <div class="admin-gallery">
                    <?php foreach ($images as $im): ?>
                        <?php $fn = (string) ($im['filename'] ?? ''); ?>
                        <?php if ($fn !== ''): ?>
                            <img src="<?= Helpers::e(Image::getImageUrl($fn, 'thumb')) ?>" alt="">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-actions">
        <?php if (in_array($status, ['pending', 'rejected'], true)): ?>
            <div class="admin-card">
                <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_action_approve')) ?></h2>
                <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/approve">
                    <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                    <button type="submit" class="btn btn--primary btn--pill btn--block"><?= Helpers::e(Helpers::__('admin_btn_approve')) ?></button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($status === 'pending'): ?>
            <div class="admin-card">
                <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_action_reject')) ?></h2>
                <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/reject">
                    <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                    <label class="visually-hidden" for="admin_note"><?= Helpers::e(Helpers::__('admin_reject_reason')) ?></label>
                    <textarea id="admin_note" class="admin-note" name="admin_note" required minlength="3" placeholder="<?= Helpers::e(Helpers::__('admin_reject_ph')) ?>"></textarea>
                    <button type="submit" class="btn btn--danger btn--pill btn--block admin-mt"><?= Helpers::e(Helpers::__('admin_btn_reject')) ?></button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($status === 'active'): ?>
            <div class="admin-card">
                <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_action_featured')) ?></h2>
                <p style="margin:0 0 0.75rem;font-size:0.875rem;color:#94a3b8"><?= Helpers::e(Helpers::__('admin_featured_hint')) ?></p>
                <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/feature">
                    <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                    <button type="submit" class="btn btn--accent btn--pill btn--block"><?= Helpers::e(Helpers::__('admin_btn_toggle_feature')) ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
