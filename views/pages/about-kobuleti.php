<?php
/**
 * About Kobuleti Page
 */
?>

<!-- Hero -->
<section class="info-hero">
    <div class="hero-bg">
        <img src="<?= asset('img/kobuleti-hero.jpg') ?>" alt="Kobuleti"
             onerror="this.parentElement.style.background='linear-gradient(135deg, #1e3a5f, #10b981)'">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 style="color: var(--white);"><?= __('kobuleti_title') ?></h1>
        <p class="hero-subtitle"><?= __('kobuleti_subtitle') ?></p>
    </div>
</section>

<!-- Content Sections -->
<?php if (!empty($sections)): ?>
    <?php foreach ($sections as $i => $section): ?>
    <section class="info-section">
        <div class="container">
            <div class="info-content">
                <?php if (!empty($section['title'])): ?>
                <h2><?= e($section['title']) ?></h2>
                <?php endif; ?>
                <?= $section['content'] ?>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
<?php else: ?>
    <section class="info-section">
        <div class="container">
            <div class="info-content text-center p-6">
                <p class="text-muted">Content coming soon...</p>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section section-alt">
    <div class="container text-center">
        <h2 style="margin-bottom: var(--space-4);"><?= __('kobuleti_cta') ?></h2>
        <a href="<?= BASE_URL ?>/listings" class="btn btn-primary btn-lg">
            <i class="ph ph-buildings"></i> <?= __('nav_listings') ?>
        </a>
    </div>
</section>
