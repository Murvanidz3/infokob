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
    <?php if (!empty($meta['robots'])): ?>
        <meta name="robots" content="<?= Helpers::e($meta['robots']) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="<?= Helpers::e(Helpers::asset('css/app.css')) ?>">
</head>
<body class="auth-body">
<?php View::partial('flash-message'); ?>
<div class="auth-wrap">
    <?= $content ?? '' ?>
</div>
</body>
</html>
