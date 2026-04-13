<?php $currentLang = Language::get(); $langLabels = Language::getShortLabels(); ?>

<header class="site-header" id="site-header" x-data="{ mobileOpen: false }">
    <div class="header-inner">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="header-logo">
            <span>🌊</span> InfoKobuleti
        </a>
        
        <!-- Desktop Nav -->
        <nav class="header-nav">
            <a href="<?= BASE_URL ?>/listings" class="<?= isActiveRoute('listings') ? 'active' : '' ?>">
                <?= __('nav_listings') ?>
            </a>
            <a href="<?= BASE_URL ?>/kobuleti" class="<?= isActiveRoute('kobuleti') ? 'active' : '' ?>">
                <?= __('nav_kobuleti') ?>
            </a>
            <a href="<?= BASE_URL ?>/contact" class="<?= isActiveRoute('contact') ? 'active' : '' ?>">
                <?= __('nav_contact') ?>
            </a>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <!-- Language Switcher -->
            <div class="lang-switcher">
                <?php foreach ($langLabels as $code => $label): ?>
                <a href="<?= BASE_URL ?>/lang/<?= $code ?>" 
                   class="<?= $currentLang === $code ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <?php if (Auth::isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/my/dashboard" class="btn btn-ghost btn-sm hide-mobile">
                    <i class="ph ph-user"></i>
                    <?= e(Auth::user()['name']) ?>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="btn btn-ghost btn-sm hide-mobile">
                    <?= __('nav_login') ?>
                </a>
            <?php endif; ?>
            
            <a href="<?= Auth::isLoggedIn() ? BASE_URL . '/my/listings/create' : BASE_URL . '/login' ?>" 
               class="btn btn-accent btn-sm hide-mobile">
                <i class="ph ph-plus"></i>
                <?= __('nav_add_listing') ?>
            </a>
            
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
                <span>🌊</span> InfoKobuleti
            </a>
            <button class="mobile-drawer-close" @click="mobileOpen = false">
                <i class="ph ph-x"></i>
            </button>
        </div>
        
        <nav class="mobile-nav">
            <a href="<?= BASE_URL ?>/listings">
                <i class="ph ph-buildings"></i> <?= __('nav_listings') ?>
            </a>
            <a href="<?= BASE_URL ?>/kobuleti">
                <i class="ph ph-map-trifold"></i> <?= __('nav_kobuleti') ?>
            </a>
            <a href="<?= BASE_URL ?>/contact">
                <i class="ph ph-envelope"></i> <?= __('nav_contact') ?>
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
        
        <div class="mobile-lang">
            <?php foreach ($langLabels as $code => $label): ?>
            <a href="<?= BASE_URL ?>/lang/<?= $code ?>" 
               class="<?= $currentLang === $code ? 'active' : '' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
