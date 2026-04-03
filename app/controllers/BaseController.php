<?php

declare(strict_types=1);

class BaseController
{
    protected function renderPhase2Placeholder(string $title = ''): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $lang = Language::get();
        $pageTitle = $title !== '' ? $title : Helpers::__('site_name_' . $lang);
        ?>
<!DOCTYPE html>
<html lang="<?= Helpers::e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e($pageTitle) ?> — InfoKobuleti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, sans-serif; background: #F8FAFC; color: #1E293B; margin: 0; padding: 2rem; }
        .card { max-width: 560px; margin: 4rem auto; background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 10px 40px rgba(30,41,59,.08); }
        a { color: #2563EB; }
    </style>
</head>
<body>
    <div class="card">
        <p><?= Helpers::e(Helpers::__('phase2_placeholder')) ?></p>
        <p><a href="<?= Helpers::e(BASE_URL) ?>/"><?= Helpers::e(Helpers::__('nav_home')) ?></a></p>
    </div>
</body>
</html>
        <?php
    }
}
