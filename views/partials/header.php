<?php

declare(strict_types=1);

$lang = Language::get();
$isAuth = Auth::isLoggedIn();
$bodyClass = $bodyClass ?? '';
$showMiniSearch = strpos($bodyClass, 'page-home') === false;
$switchLangUrl = rtrim(PUBLIC_BASE_URL, '/') . '/switch-lang.php';

$pathOnly = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pathOnly = is_string($pathOnly) && $pathOnly !== '' ? $pathOnly : '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH);
$basePath = is_string($basePath) ? rtrim($basePath, '/') : '';
if ($basePath !== '' && str_starts_with($pathOnly, $basePath)) {
    $pathOnly = substr($pathOnly, strlen($basePath)) ?: '/';
}
$pathOnly = '/' . ltrim($pathOnly, '/');
if ($pathOnly !== '/') {
    $pathOnly = rtrim($pathOnly, '/') ?: '/';
}
if ($pathOnly === '/index.php') {
    $pathOnly = '/';
}

$navSeg = null;
if ($pathOnly === '/' || str_starts_with($pathOnly, '/listings')) {
    $navSeg = 'property';
} elseif (str_starts_with($pathOnly, '/vacancies')) {
    $navSeg = 'jobs';
} elseif (str_starts_with($pathOnly, '/classifieds')) {
    $navSeg = 'services';
} elseif (str_starts_with($pathOnly, '/hotels')) {
    $navSeg = 'market';
} elseif (str_starts_with($pathOnly, '/kobuleti')) {
    $navSeg = 'kobuleti';
} elseif (str_starts_with($pathOnly, '/transport')) {
    $navSeg = 'transport';
}

$segBase = rtrim(BASE_URL, '/') . '/';
$headerSegments = [
    ['id' => 'property', 'href' => $segBase . 'listings', 'icon' => 'ph-house', 'labelKey' => 'nav_segment_property'],
    ['id' => 'jobs', 'href' => $segBase . 'vacancies', 'icon' => 'ph-briefcase', 'labelKey' => 'nav_segment_jobs'],
    ['id' => 'services', 'href' => $segBase . 'classifieds', 'icon' => 'ph-wrench', 'labelKey' => 'nav_segment_services'],
    ['id' => 'market', 'href' => $segBase . 'hotels', 'icon' => 'ph-storefront', 'labelKey' => 'nav_segment_market'],
    ['id' => 'kobuleti', 'href' => $segBase . 'kobuleti', 'icon' => 'ph-map-pin', 'labelKey' => 'nav_segment_kobuleti'],
    ['id' => 'transport', 'href' => $segBase . 'transport', 'icon' => 'ph-car', 'labelKey' => 'nav_segment_transport'],
];
?>
<header class="site-header" x-data="{ open: false, searchOpen: false }" @keydown.escape.window="open = false; searchOpen = false">
    <div class="site-header__inner container">
        <a class="site-logo site-logo--wordmark" href="<?= Helpers::e(BASE_URL) ?>/">
            <span class="site-logo__text" aria-label="<?= Helpers::e(Helpers::__('site_name_' . $lang)) ?>">🌊 InfoKobuleti</span>
        </a>

        <div class="site-header__mid">
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

            <nav class="header-segments" aria-label="<?= Helpers::e(Helpers::__('nav_sections_aria')) ?>">
                <div class="header-segments__track" role="list">
                    <?php foreach ($headerSegments as $seg):
                        $isActive = $navSeg === $seg['id'];
                        $label = Helpers::__($seg['labelKey']);
                        ?>
                        <a class="header-segment <?= $isActive ? 'is-active' : '' ?>"
                           href="<?= Helpers::e($seg['href']) ?>"
                           role="listitem"
                           title="<?= Helpers::e($label) ?>"
                           <?php if ($isActive): ?>aria-current="page"<?php endif; ?>>
                            <span class="header-segment__icon" aria-hidden="true"><i class="ph <?= Helpers::e($seg['icon']) ?>"></i></span>
                            <span class="header-segment__label"><?= Helpers::e($label) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </nav>
        </div>

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
                <?php foreach ($headerSegments as $seg):
                    $isActive = $navSeg === $seg['id'];
                    $label = Helpers::__($seg['labelKey']);
                    ?>
                    <a class="site-drawer__segment <?= $isActive ? 'is-active' : '' ?>" href="<?= Helpers::e($seg['href']) ?>">
                        <i class="ph <?= Helpers::e($seg['icon']) ?>" aria-hidden="true"></i>
                        <?= Helpers::e($label) ?>
                    </a>
                <?php endforeach; ?>
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
