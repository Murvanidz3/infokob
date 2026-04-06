<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
$lang = Language::get();
?>
<div class="auth-card">
    <a class="auth-card__logo site-logo site-logo--wordmark" href="<?= Helpers::e(BASE_URL) ?>/">
        <span class="site-logo__text" aria-label="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>">INFOKOBULETI</span>
    </a>
    <h1 class="auth-card__title"><?= Helpers::e(Helpers::__('nav_login')) ?></h1>
    <p class="auth-card__hint"><?= Helpers::e(Helpers::__('auth_login_hint')) ?></p>
    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/login" class="form-stack">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
        <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_email')) ?></label>
        <input class="input" type="email" name="email" required autocomplete="email" value="<?= Helpers::e((string) ($_POST['email'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_password')) ?></label>
        <input class="input" type="password" name="password" required autocomplete="current-password">

        <button type="submit" class="btn btn--primary btn--pill btn--block"><?= Helpers::e(Helpers::__('nav_login')) ?></button>
    </form>
    <p class="auth-card__footer"><?= Helpers::e(Helpers::__('auth_no_account')) ?> <a href="<?= Helpers::e(BASE_URL) ?>/register"><?= Helpers::e(Helpers::__('nav_register')) ?></a></p>
</div>
