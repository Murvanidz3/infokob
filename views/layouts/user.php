<?php

declare(strict_types=1);

$meta = $meta ?? SEO::defaultMeta();
$lang = Language::get();
?>
<!DOCTYPE html>
<html lang="<?= Helpers::e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e($meta['title'] ?? '') ?></title>
    <meta name="description" content="<?= Helpers::e($meta['description'] ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/components.css')) ?>">
    <?= $extraHead ?? '' ?>
</head>
<body class="user-body">
<?php View::partial('flash-message'); ?>
<div class="user-shell">
    <?php View::partial('user-sidebar'); ?>
    <div class="user-shell__main">
        <?= $content ?? '' ?>
    </div>
</div>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
