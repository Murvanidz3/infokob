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
        .admin-page { display: flex; min-height: 100vh; background: var(--bg); }
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, rgba(7, 11, 19, 0.98) 0%, rgba(10, 15, 26, 0.96) 100%);
            border-right: 1px solid var(--border);
            color: var(--white);
            flex-shrink: 0;
            padding: var(--space-6);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
        }
        .admin-sidebar .logo { display: flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-xl); font-weight: 800; margin-bottom: var(--space-8); color: var(--white); text-decoration: none; }
        .admin-nav { display: flex; flex-direction: column; gap: var(--space-1); }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            color: rgba(248, 250, 252, 0.72);
            font-weight: 500;
            font-size: var(--font-size-sm);
            border: 1px solid transparent;
            transition: var(--transition);
            text-decoration: none;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: rgba(234, 179, 8, 0.14);
            border-color: rgba(234, 179, 8, 0.32);
            color: var(--primary-light);
        }
        .admin-nav a i { font-size: 1.2rem; }
        .admin-nav .badge-count { margin-left: auto; background: var(--danger); color: var(--white); font-size: var(--font-size-xs); padding: 2px 8px; border-radius: var(--radius-full); font-weight: 700; }
        .admin-main { margin-left: 260px; flex: 1; padding: var(--space-8); background: var(--bg); min-height: 100vh; }
        .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-8); }
        .admin-header h1 { font-size: var(--font-size-2xl); }

        /* Admin dark UI overrides */
        .admin-main .table-wrap {
            background: linear-gradient(180deg, rgba(19, 26, 43, 0.94) 0%, rgba(10, 15, 26, 0.92) 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.35);
        }
        .admin-main .table-header {
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
        }
        .admin-main .data-table th {
            color: var(--text-light);
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid var(--border);
        }
        .admin-main .table-responsive {
            max-height: calc(100vh - 220px);
            overflow: auto;
        }
        .admin-main .data-table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: rgba(19, 26, 43, 0.98);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 1px 0 var(--border);
        }
        .admin-main .data-table td {
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }
        .admin-main .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }
        .admin-main .dash-stat-card {
            background: linear-gradient(180deg, rgba(19, 26, 43, 0.92) 0%, rgba(10, 15, 26, 0.88) 100%);
            border: 1px solid var(--border);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.28);
        }
        .admin-main .dash-stat-value { color: var(--text); }
        .admin-main .dash-stat-label { color: var(--text-light); }
        .admin-main .form-input,
        .admin-main .form-select,
        .admin-main .form-textarea {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-dark);
            color: var(--text);
        }
        .admin-main .form-input::placeholder,
        .admin-main .form-textarea::placeholder {
            color: var(--muted);
        }
        .admin-main .form-input:focus,
        .admin-main .form-select:focus,
        .admin-main .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.18);
        }
        .admin-main .form-label { color: var(--text-light); }
        .admin-main hr { border-top-color: var(--border) !important; }
        .admin-main .btn-ghost {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: var(--text-light);
        }
        .admin-main .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text);
        }
        .admin-main .pagination a {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: var(--text);
            box-shadow: none;
        }
        .admin-main .pagination a:hover {
            background: rgba(234, 179, 8, 0.14);
            border-color: rgba(234, 179, 8, 0.32);
            color: var(--primary-light);
        }
        .admin-main .pagination .active {
            color: var(--dark);
            box-shadow: 0 6px 16px rgba(234, 179, 8, 0.35);
        }

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
