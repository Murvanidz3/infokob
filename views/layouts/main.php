<?php

declare(strict_types=1);

$meta = $meta ?? SEO::defaultMeta();
$bodyClass = $bodyClass ?? '';
$lang = Language::get();
?>
<!DOCTYPE html>
<html lang="<?= Helpers::e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e($meta['title'] ?? '') ?></title>
    <meta name="description" content="<?= Helpers::e($meta['description'] ?? '') ?>">
    <link rel="canonical" href="<?= Helpers::e($meta['canonical'] ?? '') ?>">
    <meta property="og:title" content="<?= Helpers::e($meta['title'] ?? '') ?>">
    <meta property="og:description" content="<?= Helpers::e($meta['description'] ?? '') ?>">
    <meta property="og:image" content="<?= Helpers::e($meta['og_image'] ?? '') ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/components.css')) ?>">
    <style>[x-cloak]{display:none!important}</style>
    <?= $extraHead ?? '' ?>
</head>
<body class="<?= Helpers::e($bodyClass) ?>">
<?php View::partial('header', ['meta' => $meta]); ?>
<?php View::partial('flash-message'); ?>
<main id="main-content">
    <?= $content ?? '' ?>
</main>
<?php View::partial('footer'); ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
<script defer src="<?= Helpers::e(Helpers::asset('js/main.js')) ?>"></script>
<?= $extraScripts ?? '' ?>

</body>
</html>
