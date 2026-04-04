<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array{role:string,body:string,phone:string,name:string,created_at:string,from_site:bool}> $rows */
?>
<div class="container guide-page">
    <p class="guide-demo-banner"><?= Helpers::e(Helpers::__('guide_demo_notice')) ?></p>
    <h1 class="page-title"><?= Helpers::e(Helpers::__('guide_classifieds_h1')) ?></h1>
    <p class="page-lead"><?= Helpers::e(Helpers::__('guide_classifieds_lead')) ?></p>

    <div class="guide-form-card">
        <h2 class="guide-form-card__title"><?= Helpers::e(Helpers::__('guide_classifieds_form_title')) ?></h2>
        <form method="post" action="<?= Helpers::e(BASE_URL) ?>/classifieds" class="form-stack">
            <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
            <label class="form-label" for="gc-name"><?= Helpers::e(Helpers::__('guide_classifieds_field_name_req')) ?></label>
            <input class="input" id="gc-name" name="name" required minlength="2" maxlength="120" autocomplete="name" placeholder="<?= Helpers::e(Helpers::__('guide_classifieds_ph_name_req')) ?>">

            <label class="form-label" for="gc-role"><?= Helpers::e(Helpers::__('guide_classifieds_field_role')) ?></label>
            <input class="input" id="gc-role" name="role" required maxlength="200" placeholder="<?= Helpers::e(Helpers::__('guide_classifieds_ph_role')) ?>">

            <label class="form-label" for="gc-body"><?= Helpers::e(Helpers::__('guide_classifieds_field_about')) ?></label>
            <textarea class="input input--area" id="gc-body" name="body" required minlength="20" rows="4" placeholder="<?= Helpers::e(Helpers::__('guide_classifieds_ph_about')) ?>"></textarea>

            <label class="form-label" for="gc-phone"><?= Helpers::e(Helpers::__('guide_classifieds_field_phone')) ?></label>
            <input class="input" id="gc-phone" name="phone" required maxlength="40" autocomplete="tel" placeholder="+995 5XX XX XX XX">

            <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('guide_classifieds_submit')) ?></button>
        </form>
    </div>

    <h2 class="guide-section-title"><?= Helpers::e(Helpers::__('guide_classifieds_list_title')) ?></h2>
    <div class="guide-seeker-list">
        <?php foreach ($rows as $row): ?>
            <article class="guide-seeker-card<?= $row['from_site'] ? ' guide-seeker-card--user' : '' ?>">
                <h3 class="guide-seeker-card__role"><?= Helpers::e($row['role']) ?></h3>
                <?php if ($row['name'] !== ''): ?>
                    <p class="guide-seeker-card__meta"><?= Helpers::e($row['name']) ?></p>
                <?php endif; ?>
                <p class="guide-seeker-card__body"><?= nl2br(Helpers::e($row['body'])) ?></p>
                <p class="guide-seeker-card__phone"><i class="ph ph-phone" aria-hidden="true"></i> <?= Helpers::e($row['phone']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>
