<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array{company:string,title:string,body:string,phone:string,created_at:string,from_site:bool}> $rows */
/** @var bool $isAuth */
?>
<div class="container guide-page">
    <p class="guide-demo-banner"><?= Helpers::e(Helpers::__('guide_demo_notice')) ?></p>
    <h1 class="page-title"><?= Helpers::e(Helpers::__('guide_vacancies_h1')) ?></h1>
    <p class="page-lead"><?= Helpers::e(Helpers::__('guide_vacancies_lead')) ?></p>

    <?php if ($isAuth): ?>
        <div class="guide-form-card">
            <h2 class="guide-form-card__title"><?= Helpers::e(Helpers::__('guide_vacancies_form_title')) ?></h2>
            <form method="post" action="<?= Helpers::e(BASE_URL) ?>/vacancies" class="form-stack">
                <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                <label class="form-label" for="gv-company"><?= Helpers::e(Helpers::__('guide_vacancies_field_company')) ?></label>
                <input class="input" id="gv-company" name="company" required maxlength="200" placeholder="<?= Helpers::e(Helpers::__('guide_vacancies_ph_company')) ?>">

                <label class="form-label" for="gv-title"><?= Helpers::e(Helpers::__('guide_vacancies_field_title')) ?></label>
                <input class="input" id="gv-title" name="title" required maxlength="200" placeholder="<?= Helpers::e(Helpers::__('guide_vacancies_ph_title')) ?>">

                <label class="form-label" for="gv-body"><?= Helpers::e(Helpers::__('guide_vacancies_field_body')) ?></label>
                <textarea class="input input--area" id="gv-body" name="body" required minlength="40" rows="5" placeholder="<?= Helpers::e(Helpers::__('guide_vacancies_ph_body')) ?>"></textarea>

                <label class="form-label" for="gv-phone"><?= Helpers::e(Helpers::__('guide_vacancies_field_phone')) ?></label>
                <input class="input" id="gv-phone" name="phone" required maxlength="40" autocomplete="tel" placeholder="+995 5XX XX XX XX">

                <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('guide_vacancies_submit')) ?></button>
            </form>
        </div>
    <?php else: ?>
        <p class="guide-auth-hint">
            <a href="<?= Helpers::e(BASE_URL) ?>/login"><?= Helpers::e(Helpers::__('guide_login_to_post')) ?></a>
            <?= Helpers::e(Helpers::__('guide_vacancies_login_suffix')) ?>
        </p>
    <?php endif; ?>

    <h2 class="guide-section-title"><?= Helpers::e(Helpers::__('guide_vacancies_list_title')) ?></h2>
    <div class="guide-job-list">
        <?php foreach ($rows as $row): ?>
            <article class="guide-job-card<?= $row['from_site'] ? ' guide-job-card--user' : '' ?>">
                <p class="guide-job-card__company"><?= Helpers::e($row['company']) ?></p>
                <h3 class="guide-job-card__title"><?= Helpers::e($row['title']) ?></h3>
                <p class="guide-job-card__body"><?= nl2br(Helpers::e($row['body'])) ?></p>
                <p class="guide-job-card__phone"><i class="ph ph-phone" aria-hidden="true"></i> <?= Helpers::e($row['phone']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>
