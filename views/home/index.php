<?php
/**
 * Homepage View
 * Hero, featured listings, how it works, Kobuleti teaser
 */
?>

<!-- ═══ HERO SECTION ═══ -->
<section class="hero">
    <div class="hero-bg">
        <img src="<?= asset('img/hero-kobuleti.jpg') ?>" alt="Kobuleti Beach" 
             style="width:100%;height:100%;object-fit:cover;"
             onerror="this.parentElement.style.background='linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #0ea5e9 100%)'">
    </div>
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <h1><?= __('hero_title') ?></h1>
        <p class="hero-subtitle"><?= __('hero_subtitle') ?></p>
        
        <!-- Search Card -->
        <div class="search-card" x-data="{ dealType: 'sale' }">
            <div class="search-tabs">
                <button class="search-tab" :class="{ active: dealType === 'sale' }" @click="dealType = 'sale'">
                    <i class="ph ph-house"></i> <?= __('deal_sale') ?>
                </button>
                <button class="search-tab" :class="{ active: dealType === 'rent' }" @click="dealType = 'rent'">
                    <i class="ph ph-key"></i> <?= __('deal_rent') ?>
                </button>
                <button class="search-tab" :class="{ active: dealType === 'daily_rent' }" @click="dealType = 'daily_rent'">
                    <i class="ph ph-calendar-blank"></i> <?= __('deal_daily_rent') ?>
                </button>
            </div>
            
            <form action="<?= BASE_URL ?>/listings" method="GET">
                <input type="hidden" name="deal_type" x-bind:value="dealType">
                
                <div class="search-fields">
                    <div class="search-field">
                        <label><?= __('filter_type') ?></label>
                        <select name="type">
                            <option value=""><?= __('filter_all') ?></option>
                            <?php foreach (PROPERTY_TYPES as $type): ?>
                            <option value="<?= $type ?>"><?= getTypeLabel($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="search-field">
                        <label><?= __('filter_district') ?></label>
                        <select name="district">
                            <option value=""><?= __('all_districts') ?></option>
                            <?php foreach (DISTRICTS as $key => $name): ?>
                            <option value="<?= $key ?>"><?= __('district_' . $key) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="search-field">
                        <label><?= __('filter_price') ?></label>
                        <input type="number" name="price_max" placeholder="Max $" min="0">
                    </div>
                    
                    <div class="search-field">
                        <button type="submit" class="btn btn-primary btn-lg search-btn">
                            <i class="ph ph-magnifying-glass"></i> <?= __('btn_search') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ═══ FEATURED LISTINGS ═══ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">🔥 <?= __('featured_title') ?></h2>
            <a href="<?= BASE_URL ?>/listings" class="section-link">
                <?= __('btn_view_all') ?> <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        
        <div class="property-grid">
            <?php if (!empty($featured)): ?>
                <?php foreach ($featured as $property): ?>
                    <?php require VIEW_PATH . '/partials/property-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted text-center" style="grid-column: 1/-1; padding: var(--space-12) 0;">
                    <?= __('no_results') ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header" style="justify-content: center;">
            <h2 class="section-title"><?= __('how_it_works') ?></h2>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon">👤</div>
                <h3 class="step-title"><?= __('step1_title') ?></h3>
                <p class="step-desc"><?= __('step1_desc') ?></p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon">📋</div>
                <h3 class="step-title"><?= __('step2_title') ?></h3>
                <p class="step-desc"><?= __('step2_desc') ?></p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-icon">🤝</div>
                <h3 class="step-title"><?= __('step3_title') ?></h3>
                <p class="step-desc"><?= __('step3_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ═══ KOBULETI TEASER ═══ -->
<section class="section">
    <div class="container">
        <div class="teaser-split">
            <div class="teaser-text">
                <h2><?= __('kobuleti_teaser_title') ?></h2>
                <p><?= __('kobuleti_teaser_desc') ?></p>
                <a href="<?= BASE_URL ?>/kobuleti" class="btn btn-primary">
                    <?= __('kobuleti_learn_more') ?> <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div class="teaser-image">
                <img src="<?= asset('img/kobuleti-teaser.jpg') ?>" alt="Kobuleti" loading="lazy"
                     onerror="this.parentElement.style.background='linear-gradient(135deg, #10b981, #2563eb)'">
            </div>
        </div>
    </div>
</section>
