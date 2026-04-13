<?php require VIEW_PATH . '/partials/header.php'; ?>

<div class="error-page">
    <div>
        <div class="error-code">404</div>
        <h1 style="margin-bottom: var(--space-4);"><?= __('page_not_found') ?></h1>
        <p class="text-muted mb-6"><?= __('404_desc') ?></p>
        <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg">
            <i class="ph ph-house"></i> <?= __('go_to_home') ?>
        </a>
    </div>
</div>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
