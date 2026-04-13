<?php
/**
 * Application Configuration
 * All constants and global settings
 */

// ─── Environment ───────────────────────────────────────────
define('APP_DEBUG', true); // Set to false on production
define('APP_ENV', 'development'); // 'development' or 'production'

// ─── Database ──────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'infokobuleti');
define('DB_USER', 'root');
define('DB_PASS', '');

// ─── URLs ──────────────────────────────────────────────────
define('BASE_URL', 'http://localhost/infokob'); // No trailing slash
define('ADMIN_URL', BASE_URL . '/admin');

// ─── Paths ─────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('LANG_PATH', ROOT_PATH . '/lang');

// ─── Upload Settings ──────────────────────────────────────
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ─── Image Sizes ───────────────────────────────────────────
define('IMG_ORIGINAL_WIDTH', 1920);
define('IMG_ORIGINAL_HEIGHT', 1280);
define('IMG_MEDIUM_WIDTH', 800);
define('IMG_MEDIUM_HEIGHT', 600);
define('IMG_THUMB_WIDTH', 400);
define('IMG_THUMB_HEIGHT', 300);
define('IMG_QUALITY_ORIGINAL', 85);
define('IMG_QUALITY_MEDIUM', 80);
define('IMG_QUALITY_THUMB', 75);

// ─── Pagination ────────────────────────────────────────────
define('ITEMS_PER_PAGE', 12);

// ─── Language ──────────────────────────────────────────────
define('SUPPORTED_LANGS', ['ka', 'ru', 'en']);
define('DEFAULT_LANG', 'ka');

// ─── Google Maps ───────────────────────────────────────────
define('GOOGLE_MAPS_KEY', 'AIzaSyBprMKl2_uowt9qp4x2_R4JICAHC4Yi810');

// ─── Security ──────────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 600); // 10 minutes in seconds

// ─── Cache ─────────────────────────────────────────────────
define('CACHE_PATH', ROOT_PATH . '/cache');
define('CACHE_HOMEPAGE_TTL', 1800); // 30 minutes

// ─── Site Info ─────────────────────────────────────────────
define('SITE_NAME', 'InfoKobuleti');
define('SITE_DOMAIN', 'infokobuleti.com');

// ─── Districts ─────────────────────────────────────────────
define('DISTRICTS', [
    'center'    => 'ცენტრი',
    'chakvi'    => 'ჩაქვი',
    'sakhareb'  => 'სანახარებო',
    'ecopark'   => 'ეკო-პარკი',
    'gontsa'    => 'გონცა',
    'bobokvati' => 'ბობოქვათი',
    'tsikhisdziri' => 'ციხისძირი',
    'other'     => 'სხვა'
]);

// ─── Property Types ────────────────────────────────────────
define('PROPERTY_TYPES', ['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room']);
define('DEAL_TYPES', ['sale', 'rent', 'daily_rent']);
define('CURRENCIES', ['USD', 'GEL', 'EUR']);
define('PROPERTY_STATUSES', ['pending', 'active', 'rejected', 'sold', 'archived']);

// ─── Sea Distance Options ──────────────────────────────────
define('SEA_DISTANCES', [
    50   => '50მ-მდე',
    100  => '100მ-მდე',
    200  => '200მ-მდე',
    300  => '300მ-მდე',
    500  => '500მ-მდე',
    1000 => '1კმ+',
]);
