<?php

declare(strict_types=1);

/** @var array<string, string> $values */
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('admin_settings_title')) ?></h1>
<p class="admin-page__lead"><?= Helpers::e(Helpers::__('admin_settings_lead')) ?></p>

<form class="admin-card" method="post" action="<?= Helpers::e(BASE_URL) ?>/settings" style="max-width:520px">
    <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">

    <h2 class="admin-card__title"><?= Helpers::e(Helpers::__('admin_settings_site_names')) ?></h2>
    <label class="form-label"><?= Helpers::e(Helpers::__('admin_label_site_name_ka')) ?></label>
    <input class="input admin-input" type="text" name="site_name_ka" value="<?= Helpers::e($values['site_name_ka'] ?? '') ?>">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_site_name_ru')) ?></label>
    <input class="input admin-input" type="text" name="site_name_ru" value="<?= Helpers::e($values['site_name_ru'] ?? '') ?>">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_site_name_en')) ?></label>
    <input class="input admin-input" type="text" name="site_name_en" value="<?= Helpers::e($values['site_name_en'] ?? '') ?>">

    <h2 class="admin-card__title" style="margin-top:1.5rem"><?= Helpers::e(Helpers::__('admin_settings_contact')) ?></h2>
    <label class="form-label"><?= Helpers::e(Helpers::__('admin_label_contact_phone')) ?></label>
    <input class="input admin-input" type="text" name="contact_phone" value="<?= Helpers::e($values['contact_phone'] ?? '') ?>">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_contact_email')) ?></label>
    <input class="input admin-input" type="email" name="contact_email" value="<?= Helpers::e($values['contact_email'] ?? '') ?>">

    <h2 class="admin-card__title" style="margin-top:1.5rem"><?= Helpers::e(Helpers::__('admin_settings_featured')) ?></h2>
    <label class="form-label"><?= Helpers::e(Helpers::__('admin_label_featured_price')) ?></label>
    <input class="input admin-input" type="text" name="featured_price_gel" value="<?= Helpers::e($values['featured_price_gel'] ?? '') ?>" inputmode="numeric">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_featured_days')) ?></label>
    <input class="input admin-input" type="text" name="featured_duration_days" value="<?= Helpers::e($values['featured_duration_days'] ?? '') ?>" inputmode="numeric">

    <h2 class="admin-card__title" style="margin-top:1.5rem"><?= Helpers::e(Helpers::__('admin_settings_integrations')) ?></h2>
    <label class="form-label"><?= Helpers::e(Helpers::__('admin_label_maps_key')) ?></label>
    <input class="input admin-input" type="text" name="google_maps_key" value="<?= Helpers::e($values['google_maps_key'] ?? '') ?>" autocomplete="off">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_facebook')) ?></label>
    <input class="input admin-input" type="url" name="facebook_url" value="<?= Helpers::e($values['facebook_url'] ?? '') ?>" placeholder="https://">

    <label class="form-label" style="margin-top:0.75rem"><?= Helpers::e(Helpers::__('admin_label_instagram')) ?></label>
    <input class="input admin-input" type="url" name="instagram_url" value="<?= Helpers::e($values['instagram_url'] ?? '') ?>" placeholder="https://">

    <button type="submit" class="btn btn--primary btn--pill" style="margin-top:1.25rem"><?= Helpers::e(Helpers::__('btn_save')) ?></button>
</form>
