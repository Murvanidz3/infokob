<?php

declare(strict_types=1);

$meta = $meta ?? ['title' => 'Admin', 'description' => ''];
$lang = Language::get();
?>
<!DOCTYPE html>
<html lang="<?= Helpers::e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e($meta['title'] ?? 'Admin') ?></title>
    <meta name="description" content="<?= Helpers::e($meta['description'] ?? '') ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/components.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/admin.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <div class="admin-sidebar__brand">InfoKobuleti</div>
        <nav class="admin-sidebar__nav">
            <a class="admin-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/"><i class="ph ph-gauge"></i> <?= Helpers::e(Helpers::__('admin_nav_dashboard')) ?></a>
            <a class="admin-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/properties"><i class="ph ph-buildings"></i> <?= Helpers::e(Helpers::__('admin_nav_properties')) ?></a>
            <a class="admin-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/users"><i class="ph ph-users"></i> <?= Helpers::e(Helpers::__('admin_nav_users')) ?></a>
            <a class="admin-sidebar__link" href="<?= Helpers::e(BASE_URL) ?>/settings"><i class="ph ph-gear"></i> <?= Helpers::e(Helpers::__('admin_nav_settings')) ?></a>
        </nav>
        <div class="admin-sidebar__footer">
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-square-out"></i> <?= Helpers::e(Helpers::__('admin_nav_site')) ?></a>
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/logout"><i class="ph ph-sign-out"></i> <?= Helpers::e(Helpers::__('user_nav_logout')) ?></a>
        </div>
    </aside>
    <main class="admin-main">
        <?php View::partial('flash-message'); ?>
        <?= $content ?? '' ?>
    </main>
</div>
</body>
</html>
