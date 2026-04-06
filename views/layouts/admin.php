<?php

declare(strict_types=1);

$meta = $meta ?? ['title' => 'Admin', 'description' => ''];
$lang = Language::get();
$pageTitle = $meta['title'] ?? 'Admin';
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
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/variables.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/admin.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <a class="admin-sidebar__brand" href="<?= Helpers::e(BASE_URL) ?>/">
            <span class="admin-sidebar__wordmark">🌊 InfoKobuleti</span>
            <span class="admin-sidebar__badge">Admin</span>
        </a>
        <div class="admin-sidebar__section">Main</div>
        <nav class="admin-sidebar__nav">
            <?php
            $path = $_SERVER['REQUEST_URI'] ?? '';
            $isDash = !str_contains($path, '/properties') && !str_contains($path, '/users') && !str_contains($path, '/settings');
            ?>
            <a class="admin-sidebar__link <?= $isDash ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/"><i class="ph ph-gauge"></i> <?= Helpers::e(Helpers::__('admin_nav_dashboard')) ?></a>
        </nav>
        <div class="admin-sidebar__section">Content</div>
        <nav class="admin-sidebar__nav">
            <a class="admin-sidebar__link <?= str_contains($path, '/properties') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/properties"><i class="ph ph-buildings"></i> <?= Helpers::e(Helpers::__('admin_nav_properties')) ?></a>
        </nav>
        <div class="admin-sidebar__section">Users</div>
        <nav class="admin-sidebar__nav">
            <a class="admin-sidebar__link <?= str_contains($path, '/users') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/users"><i class="ph ph-users"></i> <?= Helpers::e(Helpers::__('admin_nav_users')) ?></a>
        </nav>
        <div class="admin-sidebar__section">System</div>
        <nav class="admin-sidebar__nav">
            <a class="admin-sidebar__link <?= str_contains($path, '/settings') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/settings"><i class="ph ph-gear"></i> <?= Helpers::e(Helpers::__('admin_nav_settings')) ?></a>
        </nav>
        <div class="admin-sidebar__footer">
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-square-out"></i> <?= Helpers::e(Helpers::__('admin_nav_site')) ?></a>
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/logout"><i class="ph ph-sign-out"></i> <?= Helpers::e(Helpers::__('user_nav_logout')) ?></a>
        </div>
    </aside>
    <div class="admin-main-wrap">
        <header class="admin-topbar">
            <div>
                <h1 class="admin-topbar__title"><?= Helpers::e($pageTitle) ?></h1>
            </div>
            <div class="admin-topbar__crumb" aria-hidden="true">Admin</div>
        </header>
        <main class="admin-main">
            <?php View::partial('flash-message'); ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
</body>
</html>
