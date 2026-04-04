<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
$lang = Language::get();
?>
<div class="auth-card">
    <a class="auth-card__logo" href="<?= Helpers::e(BASE_URL) ?>/">
        <img src="<?= Helpers::e(Helpers::siteLogoUrl()) ?>" alt="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>" width="200" height="56" decoding="async">
    </a>
    <h1 class="auth-card__title"><?= Helpers::e(Helpers::__('nav_register')) ?></h1>
    <p class="auth-card__hint"><?= Helpers::e(Helpers::__('auth_register_hint')) ?></p>
    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/register" class="form-stack">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
        <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_name')) ?></label>
        <input class="input" type="text" name="name" required maxlength="255" autocomplete="name" value="<?= Helpers::e((string) ($_POST['name'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_email')) ?></label>
        <input class="input" type="email" name="email" required autocomplete="email" value="<?= Helpers::e((string) ($_POST['email'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_phone')) ?></label>
        <input class="input" type="text" name="phone" autocomplete="tel" value="<?= Helpers::e((string) ($_POST['phone'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_whatsapp')) ?></label>
        <input class="input" type="text" name="whatsapp_number" placeholder="+995..." value="<?= Helpers::e((string) ($_POST['whatsapp_number'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_telegram')) ?></label>
        <input class="input" type="text" name="telegram_username" placeholder="@username" value="<?= Helpers::e((string) ($_POST['telegram_username'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_password')) ?></label>
        <input class="input" type="password" name="password" required minlength="8" autocomplete="new-password">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_password_confirm')) ?></label>
        <input class="input" type="password" name="password_confirm" required minlength="8" autocomplete="new-password">

        <button type="submit" class="btn btn--primary btn--pill btn--block"><?= Helpers::e(Helpers::__('nav_register')) ?></button>
    </form>
    <p class="auth-card__footer"><?= Helpers::e(Helpers::__('auth_have_account')) ?> <a href="<?= Helpers::e(BASE_URL) ?>/login"><?= Helpers::e(Helpers::__('nav_login')) ?></a></p>
</div>
