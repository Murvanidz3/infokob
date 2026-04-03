<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var array<string, mixed> $user */
?>
<div class="user-page user-page--narrow">
    <h1 class="user-page__title"><?= Helpers::e(Helpers::__('profile_title')) ?></h1>
    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/my/profile" class="form-stack profile-form">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
        <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_name')) ?></label>
        <input class="input" type="text" name="name" required value="<?= Helpers::e((string) ($user['name'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_email')) ?></label>
        <input class="input" type="email" name="email" required value="<?= Helpers::e((string) ($user['email'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_phone')) ?></label>
        <input class="input" type="text" name="phone" value="<?= Helpers::e((string) ($user['phone'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_whatsapp')) ?></label>
        <input class="input" type="text" name="whatsapp_number" value="<?= Helpers::e((string) ($user['whatsapp_number'] ?? '')) ?>">

        <label class="form-label"><?= Helpers::e(Helpers::__('auth_telegram')) ?></label>
        <input class="input" type="text" name="telegram_username" value="<?= Helpers::e((string) ($user['telegram_username'] ?? '')) ?>">

        <h2 class="profile-form__h"><?= Helpers::e(Helpers::__('profile_password_section')) ?></h2>
        <label class="form-label"><?= Helpers::e(Helpers::__('profile_new_password')) ?></label>
        <input class="input" type="password" name="new_password" autocomplete="new-password" minlength="8">

        <label class="form-label"><?= Helpers::e(Helpers::__('profile_new_password_again')) ?></label>
        <input class="input" type="password" name="new_password_confirm" autocomplete="new-password" minlength="8">

        <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('btn_save')) ?></button>
    </form>
</div>
