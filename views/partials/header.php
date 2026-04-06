<?php

declare(strict_types=1);

$lang = Language::get();
$isAuth = Auth::isLoggedIn();
$bodyClass = $bodyClass ?? '';
$showMiniSearch = strpos($bodyClass, 'page-home') === false;
$switchLangUrl = rtrim(PUBLIC_BASE_URL, '/') . '/switch-lang.php';
?>
<header class="site-header" x-data="{ open: false, searchOpen: false }" @keydown.escape.window="open = false; searchOpen = false">
    <div class="site-header__inner container">
        <a class="site-logo site-logo--wordmark" href="<?= Helpers::e(BASE_URL) ?>/">
            <span class="site-logo__text" aria-label="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>">INFOKOBULETI</span>
        </a>

        <?php if ($showMiniSearch): ?>
            <div class="header-search-wrap">
                <button type="button" class="header-mini-search" @click="searchOpen = true" aria-expanded="false">
                    <span>📍 <?= Helpers::e(Helpers::__('nav_kobuleti')) ?></span>
                    <span class="header-mini-search__sep"></span>
                    <span>🏠 <?= Helpers::e(Helpers::__('filter_type_any')) ?></span>
                    <span class="header-mini-search__sep"></span>
                    <span>💰</span>
                    <span class="header-mini-search__btn" aria-hidden="true"><i class="ph ph-magnifying-glass"></i></span>
                </button>
            </div>
        <?php endif; ?>

        <nav class="site-nav site-nav--desktop" aria-label="Main">
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('nav_kobuleti')) ?></a>
            <a class="site-nav__link" href="<?= Helpers::e(BASE_URL) ?>/contact"><?= Helpers::e(Helpers::__('nav_contact')) ?></a>
        </nav>

        <div class="site-header__actions">
            <div class="lang-switch" role="group" aria-label="Language">
                <?php
                foreach (SUPPORTED_LANGS as $code):
                    $switchHref = $switchLangUrl . '?code=' . rawurlencode($code);
                    ?>
                    <a class="lang-switch__btn <?= $code === $lang ? 'is-active' : '' ?>"
                       href="<?= Helpers::e($switchHref) ?>"><?= Helpers::e(strtoupper($code)) ?></a>
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

    <div class="site-drawer" x-show="open" x-transition.opacity x-cloak @click.self="open = false" style="display:none;">
        <div class="site-drawer__panel" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <button type="button" class="site-drawer__close" @click="open = false" aria-label="Close"><i class="ph ph-x"></i></button>
            <nav class="site-drawer__nav" aria-label="Mobile">
                <a href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a>
                <a href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('nav_kobuleti')) ?></a>
                <a href="<?= Helpers::e(BASE_URL) ?>/contact"><?= Helpers::e(Helpers::__('nav_contact')) ?></a>
                <?php foreach (SUPPORTED_LANGS as $code): ?>
                    <a href="<?= Helpers::e($switchLangUrl . '?code=' . rawurlencode($code)) ?>"><?= Helpers::e(strtoupper($code)) ?></a>
                <?php endforeach; ?>
                <?php if ($isAuth): ?>
                    <a href="<?= Helpers::e(BASE_URL) ?>/my/dashboard"><?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></a>
                <?php else: ?>
                    <a href="<?= Helpers::e(BASE_URL) ?>/login"><?= Helpers::e(Helpers::__('nav_login')) ?></a>
                <?php endif; ?>
                <a class="btn btn--primary btn--pill" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create"><?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
            </nav>
        </div>
    </div>

    <div id="search-modal" class="search-modal" x-show="searchOpen" x-cloak x-transition @click.self="searchOpen = false" :aria-hidden="!searchOpen" style="display:none;">
        <div class="search-modal__box" @click.stop>
            <button type="button" class="search-modal__close" data-close-modal="search-modal" @click="searchOpen = false" aria-label="Close">×</button>
            <h2 style="margin:0 0 1rem;font-size:1.125rem"><?= Helpers::e(Helpers::__('btn_search')) ?></h2>
            <?php View::partial('search-bar', ['variant' => 'compact', 'deal' => 'sale']); ?>
        </div>
    </div>
</header>
