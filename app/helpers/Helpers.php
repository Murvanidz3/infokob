<?php
/**
 * Global Helper Functions
 * Translation, slugs, formatting, CSRF, flash messages, utilities
 */

// ─── Global language array ─────────────────────────────────
$GLOBALS['_lang'] = [];

/**
 * Translation function
 * @param string $key Translation key
 * @param array $params Replacement parameters
 * @return string Translated string or key if not found
 */
function __($key, $params = []) {
    $text = $GLOBALS['_lang'][$key] ?? $key;
    if (!empty($params)) {
        foreach ($params as $k => $v) {
            $text = str_replace(':' . $k, $v, $text);
        }
    }
    return $text;
}

/**
 * Generate URL slug from text (supports Georgian transliteration)
 */
function slug($text) {
    // Georgian to Latin transliteration map
    $geo = [
        'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e',
        'ვ' => 'v', 'ზ' => 'z', 'თ' => 't', 'ი' => 'i', 'კ' => 'k',
        'ლ' => 'l', 'მ' => 'm', 'ნ' => 'n', 'ო' => 'o', 'პ' => 'p',
        'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's', 'ტ' => 't', 'უ' => 'u',
        'ფ' => 'p', 'ქ' => 'q', 'ღ' => 'gh', 'ყ' => 'y', 'შ' => 'sh',
        'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz', 'წ' => 'ts', 'ჭ' => 'ch',
        'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h'
    ];
    
    // Russian to Latin
    $rus = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];
    
    $text = mb_strtolower($text, 'UTF-8');
    $text = strtr($text, $geo);
    $text = strtr($text, $rus);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Format price for display
 */
function formatPrice($amount, $currency = 'USD') {
    $symbols = [
        'USD' => '$',
        'GEL' => '₾',
        'EUR' => '€'
    ];
    
    $symbol = $symbols[$currency] ?? $currency;
    $formatted = number_format((float)$amount, 0, '.', ',');
    
    if ($currency === 'GEL') {
        return $formatted . ' ' . $symbol;
    }
    return $symbol . $formatted;
}

/**
 * Relative time display (e.g., "2 hours ago")
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0 || $diff->m > 0) {
        $count = $diff->y > 0 ? $diff->y * 12 + $diff->m : $diff->m;
        return $count . ' ' . __('time_months_ago');
    }
    if ($diff->d >= 7) {
        return floor($diff->d / 7) . ' ' . __('time_weeks_ago');
    }
    if ($diff->d > 0) {
        return $diff->d . ' ' . __('time_days_ago');
    }
    if ($diff->h > 0) {
        return $diff->h . ' ' . __('time_hours_ago');
    }
    if ($diff->i > 0) {
        return $diff->i . ' ' . __('time_minutes_ago');
    }
    return __('time_just_now');
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Output hidden CSRF field for forms
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token from POST request
 */
function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
        flash('error', __('error_csrf'));
        redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL);
        exit;
    }
    // Regenerate token after successful verification
    unset($_SESSION[CSRF_TOKEN_NAME]);
}

/**
 * Set flash message
 */
function flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get and clear flash message
 */
function getFlash($key) {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

/**
 * Check if flash message exists
 */
function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Get old form input value (stored in session after validation failure)
 */
function old($field, $default = '') {
    $value = $_SESSION['old_input'][$field] ?? $default;
    unset($_SESSION['old_input'][$field]);
    return $value;
}

/**
 * Store old form inputs in session
 */
function storeOldInput() {
    $_SESSION['old_input'] = $_POST;
}

/**
 * Escape HTML output (XSS prevention)
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get current URL
 */
function currentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get image URL helper
 */
function getImageUrl($filename, $size = 'thumb') {
    if (empty($filename)) {
        return BASE_URL . '/public/assets/img/no-image.svg';
    }
    return UPLOAD_URL . '/properties/' . $size . '/' . $filename;
}

/**
 * Truncate text to specified length
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get property type translation key
 */
function getTypeLabel($type) {
    return __('type_' . $type);
}

/**
 * Get deal type translation key
 */
function getDealLabel($dealType) {
    return __('deal_' . $dealType);
}

/**
 * Get status translation key  
 */
function getStatusLabel($status) {
    return __('status_' . $status);
}

/**
 * Get district translation key
 */
function getDistrictLabel($district) {
    return __('district_' . $district) !== 'district_' . $district 
        ? __('district_' . $district) 
        : $district;
}

/**
 * Get currency symbol
 */
function getCurrencySymbol($currency) {
    $symbols = ['USD' => '$', 'GEL' => '₾', 'EUR' => '€'];
    return $symbols[$currency] ?? $currency;
}

/**
 * Format price with deal type suffix
 */
function formatPriceWithDeal($price, $currency, $dealType) {
    $formatted = formatPrice($price, $currency);
    if ($dealType === 'daily_rent') {
        $formatted .= __('per_night');
    } elseif ($dealType === 'rent') {
        $formatted .= __('per_month');
    }
    return $formatted;
}

/**
 * Simple file-based cache
 */
function cache_get($key) {
    $file = CACHE_PATH . '/' . md5($key) . '.cache';
    if (!file_exists($file)) return null;
    
    $data = unserialize(file_get_contents($file));
    if ($data['expires'] < time()) {
        @unlink($file);
        return null;
    }
    return $data['value'];
}

function cache_set($key, $value, $ttl = 1800) {
    if (!is_dir(CACHE_PATH)) {
        @mkdir(CACHE_PATH, 0755, true);
    }
    $data = [
        'value' => $value,
        'expires' => time() + $ttl
    ];
    file_put_contents(CACHE_PATH . '/' . md5($key) . '.cache', serialize($data));
}

function cache_clear($key = null) {
    if ($key) {
        @unlink(CACHE_PATH . '/' . md5($key) . '.cache');
    } else {
        $files = glob(CACHE_PATH . '/*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}

/**
 * Get base URL for assets
 */
function asset($path) {
    return BASE_URL . '/public/assets/' . ltrim($path, '/');
}

/**
 * Check if current route matches
 */
function isActiveRoute($route) {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
    $uri = $base ? str_replace($base . '/', '', $uri) : $uri;
    return $uri === ltrim($route, '/');
}

/**
 * Generate pagination data
 */
function paginate($totalItems, $currentPage, $perPage = ITEMS_PER_PAGE) {
    $totalPages = max(1, ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'total_items' => $totalItems,
        'per_page' => $perPage,
        'offset' => ($currentPage - 1) * $perPage,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
    ];
}
