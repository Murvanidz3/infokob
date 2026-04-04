<?php

declare(strict_types=1);

$lang = Language::get();
$isAuth = Auth::isLoggedIn();
?>
<header class="site-header" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="site-header__inner">
        <a class="site-logo" href="<?= Helpers::e(BASE_URL) ?>/">
            <img class="site-logo__img" src="<?= Helpers::e(Helpers::siteLogoUrl()) ?>" alt="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>" width="240" height="68" decoding="async" fetchpriority="high">
        </a>

        <nav class="site-nav site-nav--desktop" aria-label="Main">
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('nav_kobuleti')) ?></a>
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/contact"><?= Helpers::e(Helpers::__('nav_contact')) ?></a>
        </nav>

        <div class="site-header__actions">
            <div class="lang-switch" role="group" aria-label="Language">
                <?php foreach (SUPPORTED_LANGS as $code): ?>
                    <a class="lang-switch__btn <?= $code === $lang ? 'is-active' : '' ?>"
                       href="<?= Helpers::e(BASE_URL) ?>/lang/<?= Helpers::e($code) ?>"><?= Helpers::e(strtoupper($code)) ?></a>
                <?php endforeach; ?>
            </div>
            <?php if ($isAuth): ?>
                <a class="btn btn--ghost btn--sm" href="<?= Helpers::e(BASE_URL) ?>/my/dashboard"><?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></a>
            <?php else: ?>
                <a class="btn btn--ghost btn--sm" href="<?= Helpers::e(BASE_URL) ?>/login"><?= Helpers::e(Helpers::__('nav_login')) ?></a>
            <?php endif; ?>
            <a class="btn btn--primary btn--sm btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
            <button type="button" class="site-header__burger" @click="open = !open" :aria-expanded="open.toString()" aria-label="Menu">
                <i class="ph ph-list" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="site-drawer" x-show="open" x-transition.opacity x-cloak @click.self="open = false">
        <div class="site-drawer__panel" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <button type="button" class="site-drawer__close" @click="open = false" aria-label="Close"><i class="ph ph-x"></i></button>
            <nav class="site-drawer__nav" aria-label="Mobile">
                <a href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
                <a href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('nav_kobuleti')) ?></a>
                <a href="<?= Helpers::e(BASE_URL) ?>/contact"><?= Helpers::e(Helpers::__('nav_contact')) ?></a>
                <?php if ($isAuth): ?>
                    <a href="<?= Helpers::e(BASE_URL) ?>/my/dashboard"><?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></a>
                <?php else: ?>
                    <a href="<?= Helpers::e(BASE_URL) ?>/login"><?= Helpers::e(Helpers::__('nav_login')) ?></a>
                <?php endif; ?>
                <a class="btn btn--primary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
            </nav>
        </div>
    </div>
</header>
