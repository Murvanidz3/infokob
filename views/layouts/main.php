<!DOCTYPE html>
<html lang="<?= Language::get() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= SEO::render() ?>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web@2.0.3"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌊</text></svg>">
</head>
<body class="<?= isset($noHeaderPadding) ? '' : 'has-header' ?>">
    
    <?php require VIEW_PATH . '/partials/header.php'; ?>
    
    <?php require VIEW_PATH . '/partials/flash-message.php'; ?>
    
    <main>
        <?= $content ?? '' ?>
    </main>
    
    <?php require VIEW_PATH . '/partials/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="<?= asset('js/main.js') ?>" defer></script>
    
    <?php if (!empty($scripts)): foreach ($scripts as $script): ?>
    <script src="<?= asset('js/' . $script) ?>" defer></script>
    <?php endforeach; endif; ?>
    
    <?php if (defined('GOOGLE_MAPS_KEY') && GOOGLE_MAPS_KEY): ?>
    <?php if (!empty($loadMaps)): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_KEY ?>&callback=initMap" async defer></script>
    <?php endif; ?>
    <?php endif; ?>
</body>
</html>
