<?php
/**
 * Generic informational page template
 */
?>

<section class="section">
    <div class="container">
        <div class="card p-6" style="max-width: 920px; margin: 0 auto;">
            <div class="badge mb-4">
                <i class="ph <?= e($page['icon']) ?>"></i>
                <?= e($page['title']) ?>
            </div>
            
            <h1><?= e($page['title']) ?></h1>
            <p class="text-muted mb-6" style="font-size: 1.05rem;">
                <?= e($page['subtitle']) ?>
            </p>
            
            <?php if (!empty($page['items'])): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <?php foreach ($page['items'] as $item): ?>
                <div class="card p-4">
                    <i class="ph ph-check-circle" style="color: var(--primary);"></i>
                    <span style="margin-left: var(--space-2);"><?= e($item) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="flex gap-3" style="flex-wrap: wrap;">
                <a href="<?= e($page['primary_link']) ?>" class="btn btn-primary">
                    <?= e($page['primary_text']) ?>
                </a>
                <a href="<?= e($page['secondary_link']) ?>" class="btn btn-ghost">
                    <?= e($page['secondary_text']) ?>
                </a>
            </div>
        </div>
    </div>
</section>
