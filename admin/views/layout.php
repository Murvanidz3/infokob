<!DOCTYPE html>
<html lang="<?= Language::get() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> — InfoKobuleti Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://unpkg.com/@phosphor-icons/web@2.0.3"></script>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    <style>
        .admin-page { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--dark); color: var(--white); flex-shrink: 0; padding: var(--space-6); position: fixed; top: 0; bottom: 0; left: 0; overflow-y: auto; }
        .admin-sidebar .logo { display: flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-xl); font-weight: 800; margin-bottom: var(--space-8); color: var(--white); text-decoration: none; }
        .admin-nav { display: flex; flex-direction: column; gap: var(--space-1); }
        .admin-nav a { display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius); color: rgba(255,255,255,0.6); font-weight: 500; font-size: var(--font-size-sm); transition: var(--transition); text-decoration: none; }
        .admin-nav a:hover, .admin-nav a.active { background: rgba(255,255,255,0.1); color: var(--white); }
        .admin-nav a i { font-size: 1.2rem; }
        .admin-nav .badge-count { margin-left: auto; background: var(--danger); color: var(--white); font-size: var(--font-size-xs); padding: 2px 8px; border-radius: var(--radius-full); font-weight: 700; }
        .admin-main { margin-left: 260px; flex: 1; padding: var(--space-8); background: var(--bg); min-height: 100vh; }
        .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-8); }
        .admin-header h1 { font-size: var(--font-size-2xl); }
        @media (max-width: 1023px) {
            .admin-sidebar { position: relative; width: 100%; }
            .admin-main { margin-left: 0; }
            .admin-page { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="admin-page">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="<?= ADMIN_URL ?>" class="logo">⚙️ Admin</a>
            
            <nav class="admin-nav">
                <a href="<?= ADMIN_URL ?>" class="<?= ($adminPath ?? '') === '/' ? 'active' : '' ?>">
                    <i class="ph ph-squares-four"></i> Dashboard
                </a>
                <a href="<?= ADMIN_URL ?>/listings" class="<?= strpos($adminPath ?? '', '/listings') === 0 ? 'active' : '' ?>">
                    <i class="ph ph-buildings"></i> Listings
                    <?php $pendingCount = $propertyModel->getPendingCount(); if ($pendingCount > 0): ?>
                    <span class="badge-count"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= ADMIN_URL ?>/users" class="<?= ($adminPath ?? '') === '/users' ? 'active' : '' ?>">
                    <i class="ph ph-users"></i> Users
                </a>
                <a href="<?= ADMIN_URL ?>/info" class="<?= ($adminPath ?? '') === '/info' ? 'active' : '' ?>">
                    <i class="ph ph-article"></i> Kobuleti Info
                </a>
                <a href="<?= ADMIN_URL ?>/settings" class="<?= ($adminPath ?? '') === '/settings' ? 'active' : '' ?>">
                    <i class="ph ph-gear"></i> Settings
                </a>
                
                <div style="height: 1px; background: rgba(255,255,255,0.1); margin: var(--space-6) 0;"></div>
                
                <a href="<?= BASE_URL ?>"><i class="ph ph-arrow-left"></i> Back to Site</a>
                <a href="<?= BASE_URL ?>/logout"><i class="ph ph-sign-out"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <?php require VIEW_PATH . '/partials/flash-message.php'; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>
