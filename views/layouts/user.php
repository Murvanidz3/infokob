<!DOCTYPE html>
<html lang="<?= Language::get() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= SEO::render() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://unpkg.com/@phosphor-icons/web@2.0.3"></script>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌊</text></svg>">
</head>
<body class="has-header">
    
    <?php require VIEW_PATH . '/partials/header.php'; ?>
    
    <div class="container">
        <?php require VIEW_PATH . '/partials/flash-message.php'; ?>
        
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar hide-tablet">
                <div class="dashboard-user">
                    <div class="dashboard-avatar">
                        <?= mb_substr(Auth::user()['name'], 0, 1) ?>
                    </div>
                    <div class="font-semibold"><?= e(Auth::user()['name']) ?></div>
                    <div class="text-sm text-muted"><?= e(Auth::user()['email']) ?></div>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="<?= BASE_URL ?>/my/dashboard" class="<?= isActiveRoute('my/dashboard') ? 'active' : '' ?>">
                        <i class="ph ph-squares-four"></i> <?= __('nav_my_dashboard') ?>
                    </a>
                    <a href="<?= BASE_URL ?>/my/listings/create" class="<?= isActiveRoute('my/listings/create') ? 'active' : '' ?>">
                        <i class="ph ph-plus-circle"></i> <?= __('nav_add_listing') ?>
                    </a>
                    <a href="<?= BASE_URL ?>/my/profile" class="<?= isActiveRoute('my/profile') ? 'active' : '' ?>">
                        <i class="ph ph-user-circle"></i> <?= __('nav_profile') ?>
                    </a>
                    <a href="<?= BASE_URL ?>/logout">
                        <i class="ph ph-sign-out"></i> <?= __('nav_logout') ?>
                    </a>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <main>
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    
    <script src="<?= asset('js/main.js') ?>" defer></script>
    <?php if (!empty($scripts)): foreach ($scripts as $script): ?>
    <script src="<?= asset('js/' . $script) ?>" defer></script>
    <?php endforeach; endif; ?>
</body>
</html>
