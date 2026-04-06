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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/app.css')) ?>">
    <style>[x-cloak]{display:none!important}</style>
    <?= $extraHead ?? '' ?>
</head>
<body class="user-body">
<?php View::partial('flash-message'); ?>
<?php View::partial('user-topnav'); ?>
<div class="user-shell">
    <?= $content ?? '' ?>
</div>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
<script defer src="<?= Helpers::e(Helpers::asset('js/animations.js')) ?>"></script>
<script defer src="<?= Helpers::e(Helpers::asset('js/toast.js')) ?>"></script>
<script defer src="<?= Helpers::e(Helpers::asset('js/app.js')) ?>"></script>
<script defer src="<?= Helpers::e(Helpers::asset('js/upload.js')) ?>"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js" crossorigin="anonymous"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
