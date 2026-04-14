<?php
/**
 * Production Config Injector
 * Called by GitHub Actions during deploy to inject secret values
 * into config/database.php and config/config.php
 * 
 * Environment variables are passed from GitHub Secrets
 */

echo "🔧 Writing production configuration...\n";

// ─── 1. Inject database credentials ───────────────────────
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'infokobuleti';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

$databaseConfig = __DIR__ . '/../config/database.php';
$content = file_get_contents($databaseConfig);

$content = preg_replace(
    "/define\('DB_HOST',\s*'[^']*'\)/",
    "define('DB_HOST', '{$dbHost}')",
    $content
);
$content = preg_replace(
    "/define\('DB_NAME',\s*'[^']*'\)/",
    "define('DB_NAME', '{$dbName}')",
    $content
);
$content = preg_replace(
    "/define\('DB_USER',\s*'[^']*'\)/",
    "define('DB_USER', '{$dbUser}')",
    $content
);
$content = preg_replace(
    "/define\('DB_PASS',\s*'[^']*'\)/",
    "define('DB_PASS', '{$dbPass}')",
    $content
);

// If the defines are in config.php instead
$configFile = __DIR__ . '/../config/config.php';
$configContent = file_get_contents($configFile);

$configContent = preg_replace(
    "/define\('DB_HOST',\s*'[^']*'\)/",
    "define('DB_HOST', '{$dbHost}')",
    $configContent
);
$configContent = preg_replace(
    "/define\('DB_NAME',\s*'[^']*'\)/",
    "define('DB_NAME', '{$dbName}')",
    $configContent
);
$configContent = preg_replace(
    "/define\('DB_USER',\s*'[^']*'\)/",
    "define('DB_USER', '{$dbUser}')",
    $configContent
);
$configContent = preg_replace(
    "/define\('DB_PASS',\s*'[^']*'\)/",
    "define('DB_PASS', '{$dbPass}')",
    $configContent
);

// ─── 2. Set production mode ───────────────────────────────
$configContent = preg_replace(
    "/define\('APP_DEBUG',\s*true\)/",
    "define('APP_DEBUG', false)",
    $configContent
);
$configContent = preg_replace(
    "/define\('APP_ENV',\s*'development'\)/",
    "define('APP_ENV', 'production')",
    $configContent
);

// ─── 3. Set production BASE_URL ───────────────────────────
$configContent = preg_replace(
    "/define\('BASE_URL',\s*'[^']*'\)/",
    "define('BASE_URL', 'https://infokobuleti.com')",
    $configContent
);

// ─── 4. Disable display_errors in index.php ───────────────
$indexFile = __DIR__ . '/../index.php';
$indexContent = file_get_contents($indexFile);
$indexContent = str_replace(
    "ini_set('display_errors', 1);",
    "ini_set('display_errors', 0);",
    $indexContent
);
file_put_contents($indexFile, $indexContent);

// ─── 5. Disable display_errors in admin/index.php ─────────
$adminIndex = __DIR__ . '/../admin/index.php';
if (file_exists($adminIndex)) {
    $adminContent = file_get_contents($adminIndex);
    $adminContent = str_replace(
        "ini_set('display_errors', 1);",
        "ini_set('display_errors', 0);",
        $adminContent
    );
    file_put_contents($adminIndex, $adminContent);
}

// ─── Write files ──────────────────────────────────────────
file_put_contents($databaseConfig, $content);
file_put_contents($configFile, $configContent);

echo "✅ Production config written successfully!\n";
echo "   DB_HOST: {$dbHost}\n";
echo "   DB_NAME: {$dbName}\n";
echo "   DB_USER: {$dbUser}\n";
echo "   BASE_URL: https://infokobuleti.com\n";
echo "   APP_DEBUG: false\n";
echo "   display_errors: 0\n";
