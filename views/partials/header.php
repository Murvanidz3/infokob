<?php
$currentLang = Language::get();
$langOptions = Language::getOptions();
$currentLangLabel = $langOptions[$currentLang] ?? 'KA';
?>

<header class="site-header" id="site-header" x-data="{ mobileOpen: false, langOpen: false, drawerLangOpen: false }" @keydown.escape.window="mobileOpen = false; langOpen = false; drawerLangOpen = false">
    <div class="header-inner">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="header-logo">
            <span>🌊</span>
        </a>
        
        <!-- Desktop Nav -->
        <nav class="header-nav">
            <a href="<?= BASE_URL ?>" class="<?= isActiveRoute('') ? 'active' : '' ?>">
                <?= __('menu_real_estate') ?>
            </a>
            <a href="<?= BASE_URL ?>/hotels" class="<?= isActiveRoute('hotels') ? 'active' : '' ?>">
                <?= __('menu_hotels') ?>
            </a>
            <a href="<?= BASE_URL ?>/announcements" class="<?= isActiveRoute('announcements') ? 'active' : '' ?>">
                <?= __('menu_announcements') ?>
            </a>
            <a href="<?= BASE_URL ?>/employment" class="<?= isActiveRoute('employment') ? 'active' : '' ?>">
                <?= __('menu_employment') ?>
            </a>
            <a href="<?= BASE_URL ?>/education" class="<?= isActiveRoute('education') ? 'active' : '' ?>">
                <?= __('menu_education') ?>
            </a>
            <a href="<?= BASE_URL ?>/tourism" class="<?= isActiveRoute('tourism') ? 'active' : '' ?>">
                <?= __('menu_tourism') ?>
            </a>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <a href="<?= Auth::isLoggedIn() ? BASE_URL . '/my/listings/create' : BASE_URL . '/login' ?>" 
               class="btn btn-accent btn-sm hide-mobile">
                <i class="ph ph-plus"></i>
                <?= __('nav_add_listing') ?>
            </a>

            <?php if (Auth::isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/my/dashboard" class="btn btn-ghost btn-sm hide-mobile">
                    <i class="ph ph-user"></i>
                    <?= e(Auth::user()['name']) ?>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="btn btn-accent btn-sm hide-mobile">
                    <?= __('nav_login') ?>
                </a>
            <?php endif; ?>

            <!-- Language dropdown (desktop) -->
            <div class="lang-dropdown hide-mobile" @click.outside="langOpen = false">
                <button type="button" class="lang-dropdown-trigger" @click="langOpen = !langOpen" :aria-expanded="langOpen" aria-haspopup="listbox">
                    <span><?= e($currentLangLabel) ?></span>
                    <i class="ph ph-caret-down lang-dropdown-caret" :class="{ 'is-open': langOpen }"></i>
                </button>
                <div class="lang-dropdown-panel" x-show="langOpen" x-cloak x-transition.opacity.duration.150ms>
                    <?php foreach ($langOptions as $code => $label): ?>
                    <a href="<?= BASE_URL ?>/lang/<?= $code ?>" class="<?= $currentLang === $code ? 'is-active' : '' ?>">
                        <?= e($label) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" @click="mobileOpen = true" aria-label="Menu">
                <i class="ph ph-list"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" :class="{ 'open': mobileOpen }" @click="mobileOpen = false"></div>
    
    <!-- Mobile Drawer -->
    <div class="mobile-drawer" :class="{ 'open': mobileOpen }">
        <div class="mobile-drawer-header">
            <a href="<?= BASE_URL ?>" class="header-logo">
                <span>🌊</span>
            </a>
            <button class="mobile-drawer-close" @click="mobileOpen = false">
                <i class="ph ph-x"></i>
            </button>
        </div>
        
        <nav class="mobile-nav">
            <a href="<?= BASE_URL ?>">
                <i class="ph ph-buildings"></i> <?= __('menu_real_estate') ?>
            </a>
            <a href="<?= BASE_URL ?>/hotels">
                <i class="ph ph-bed"></i> <?= __('menu_hotels') ?>
            </a>
            <a href="<?= BASE_URL ?>/announcements">
                <i class="ph ph-megaphone-simple"></i> <?= __('menu_announcements') ?>
            </a>
            <a href="<?= BASE_URL ?>/employment">
                <i class="ph ph-briefcase"></i> <?= __('menu_employment') ?>
            </a>
            <a href="<?= BASE_URL ?>/education">
                <i class="ph ph-graduation-cap"></i> <?= __('menu_education') ?>
            </a>
            <a href="<?= BASE_URL ?>/tourism">
                <i class="ph ph-map-trifold"></i> <?= __('menu_tourism') ?>
            </a>
            
            <div class="divider"></div>
            
            <?php if (Auth::isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/my/dashboard">
                    <i class="ph ph-squares-four"></i> <?= __('nav_my_dashboard') ?>
                </a>
                <a href="<?= BASE_URL ?>/my/listings">
                    <i class="ph ph-list-bullets"></i> <?= __('nav_my_listings') ?>
                </a>
                <a href="<?= BASE_URL ?>/my/listings/create">
                    <i class="ph ph-plus-circle"></i> <?= __('nav_add_listing') ?>
                </a>
                <a href="<?= BASE_URL ?>/my/profile">
                    <i class="ph ph-user-circle"></i> <?= __('nav_profile') ?>
                </a>
                
                <div class="divider"></div>
                
                <a href="<?= BASE_URL ?>/logout">
                    <i class="ph ph-sign-out"></i> <?= __('nav_logout') ?>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login">
                    <i class="ph ph-sign-in"></i> <?= __('nav_login') ?>
                </a>
                <a href="<?= BASE_URL ?>/register">
                    <i class="ph ph-user-plus"></i> <?= __('nav_register') ?>
                </a>
            <?php endif; ?>
        </nav>
        
        <div class="mobile-lang-dropdown">
            <button type="button" class="mobile-lang-trigger" @click="drawerLangOpen = !drawerLangOpen" :aria-expanded="drawerLangOpen">
                <i class="ph ph-globe"></i>
                <span><?= e($currentLangLabel) ?></span>
                <i class="ph ph-caret-down" :class="{ 'is-rotated': drawerLangOpen }"></i>
            </button>
            <div class="mobile-lang-panel" x-show="drawerLangOpen" x-cloak x-transition.opacity.duration.150ms>
                <?php foreach ($langOptions as $code => $label): ?>
                <a href="<?= BASE_URL ?>/lang/<?= $code ?>" class="<?= $currentLang === $code ? 'is-active' : '' ?>">
                    <?= e($label) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</header>
