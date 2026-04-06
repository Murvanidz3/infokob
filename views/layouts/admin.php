<?php

declare(strict_types=1);

$meta = $meta ?? ['title' => 'Admin', 'description' => ''];
$lang = Language::get();
$pageTitle = $meta['title'] ?? 'Admin';
$path = $_SERVER['REQUEST_URI'] ?? '';
$isDash = !str_contains($path, '/properties') && !str_contains($path, '/users') && !str_contains($path, '/settings');
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/variables.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/admin.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <a class="admin-sidebar__brand" href="<?= Helpers::e(BASE_URL) ?>/">
            <span class="admin-sidebar__brand-text">
                <span class="admin-sidebar__wordmark">INFOKOBULETI</span>
                <span class="admin-sidebar__badge"><?= Helpers::e(Helpers::__('admin_sidebar_badge')) ?></span>
            </span>
        </a>

        <div class="admin-sidebar__body">
            <div class="admin-sidebar__section"><?= Helpers::e(Helpers::__('admin_sidebar_section_main')) ?></div>
            <nav class="admin-sidebar__nav" aria-label="<?= Helpers::e(Helpers::__('admin_sidebar_section_main')) ?>">
                <a class="admin-sidebar__link <?= $isDash ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/"><i class="ph ph-gauge" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('admin_nav_dashboard')) ?></span></a>
            </nav>

            <div class="admin-sidebar__section"><?= Helpers::e(Helpers::__('admin_sidebar_section_content')) ?></div>
            <nav class="admin-sidebar__nav" aria-label="<?= Helpers::e(Helpers::__('admin_sidebar_section_content')) ?>">
                <a class="admin-sidebar__link <?= str_contains($path, '/properties') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/properties"><i class="ph ph-buildings" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('admin_nav_properties')) ?></span></a>
            </nav>

            <div class="admin-sidebar__section"><?= Helpers::e(Helpers::__('admin_sidebar_section_users')) ?></div>
            <nav class="admin-sidebar__nav" aria-label="<?= Helpers::e(Helpers::__('admin_sidebar_section_users')) ?>">
                <a class="admin-sidebar__link <?= str_contains($path, '/users') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/users"><i class="ph ph-users" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('admin_nav_users')) ?></span></a>
            </nav>

            <div class="admin-sidebar__section"><?= Helpers::e(Helpers::__('admin_sidebar_section_system')) ?></div>
            <nav class="admin-sidebar__nav" aria-label="<?= Helpers::e(Helpers::__('admin_sidebar_section_system')) ?>">
                <a class="admin-sidebar__link <?= str_contains($path, '/settings') ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/settings"><i class="ph ph-gear" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('admin_nav_settings')) ?></span></a>
            </nav>
        </div>

        <div class="admin-sidebar__footer">
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-square-out" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('admin_nav_site')) ?></span></a>
            <a class="admin-sidebar__link admin-sidebar__link--muted" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/logout"><i class="ph ph-sign-out" aria-hidden="true"></i><span><?= Helpers::e(Helpers::__('user_nav_logout')) ?></span></a>
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
