<?php

declare(strict_types=1);

$name = Auth::userName() ?: Helpers::__('nav_my_dashboard');
$email = Auth::userEmail();
$lang = Language::get();
?>
<aside class="user-sidebar">
    <div class="user-sidebar__brand">
        <a href="<?= Helpers::e(BASE_URL) ?>/" class="site-logo site-logo--sidebar">
            <img class="site-logo__img" src="<?= Helpers::e(Helpers::siteLogoUrl()) ?>" alt="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>" width="200" height="52" loading="lazy" decoding="async">
        </a>
    </div>
    <div class="user-sidebar__user">
        <div class="user-sidebar__avatar" aria-hidden="true"><i class="ph ph-user-circle"></i></div>
        <div>
            <div class="user-sidebar__name"><?= Helpers::e($name) ?></div>
            <div class="user-sidebar__email"><?= Helpers::e($email) ?></div>
        </div>
    </div>
    <nav class="user-sidebar__nav" aria-label="Account">
        <a class="user-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/my/dashboard"><i class="ph ph-squares-four"></i> <?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></a>
        <a class="user-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/my/listings"><i class="ph ph-list"></i> <?= Helpers::e(Helpers::__('user_nav_listings')) ?></a>
        <a class="user-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><i class="ph ph-plus-circle"></i> <?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
        <a class="user-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/my/profile"><i class="ph ph-user"></i> <?= Helpers::e(Helpers::__('profile_title')) ?></a>
        <a class="user-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/logout"><i class="ph ph-sign-out"></i> <?= Helpers::e(Helpers::__('user_nav_logout')) ?></a>
    </nav>
</aside>
