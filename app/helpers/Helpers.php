<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once __DIR__ . '/Language.php';

class Helpers
{
    private static ?string $csrfToken = null;

    public static function __(string $key, array $replace = []): string
    {
        static $cache = [];
        $lang = Language::get();
        if (!isset($cache[$lang])) {
            $file = LANG_PATH . DIRECTORY_SEPARATOR . $lang . '.php';
            $cache[$lang] = is_readable($file) ? (require $file) : [];
        }
        $str = $cache[$lang][$key] ?? $key;
        foreach ($replace as $k => $v) {
            $str = str_replace(':' . $k, (string) $v, $str);
        }
        return $str;
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        if (self::$csrfToken === null) {
            if (empty($_SESSION[CSRF_TOKEN_KEY])) {
                $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
            }
            self::$csrfToken = $_SESSION[CSRF_TOKEN_KEY];
        }
        return self::$csrfToken;
    }

    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION[CSRF_TOKEN_KEY])
            && hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }

    /**
     * URL-safe slug from any UTF-8 title (Georgian transliterated + ASCII fallback).
     */
    public static function slug(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return 'listing-' . bin2hex(random_bytes(4));
        }
        $text = self::transliterateGeorgian($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = strtolower((string) $text);
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
        $text = trim($text, '-');
        return $text !== '' ? $text : 'listing-' . bin2hex(random_bytes(4));
    }

    private static function transliterateGeorgian(string $text): string
    {
        $map = [
            'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v', 'ზ' => 'z',
            'თ' => 't', 'ი' => 'i', 'კ' => 'k', 'ლ' => 'l', 'მ' => 'm', 'ნ' => 'n', 'ო' => 'o',
            'პ' => 'p', 'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's', 'ტ' => 't', 'უ' => 'u', 'ფ' => 'p',
            'ქ' => 'k', 'ღ' => 'gh', 'ყ' => 'q', 'შ' => 'sh', 'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz',
            'წ' => 'ts', 'ჭ' => 'ch', 'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h',
        ];
        return strtr($text, $map);
    }

    public static function formatPrice(?float $amount, string $currency = 'USD', bool $negotiable = false): string
    {
        if ($amount === null) {
            return self::__('price_on_request');
        }
        $symbols = ['USD' => '$', 'EUR' => '€', 'GEL' => '₾'];
        $sym = $symbols[$currency] ?? $currency . ' ';
        $formatted = number_format($amount, $amount == floor($amount) ? 0 : 2, '.', ',');
        $out = $sym . $formatted;
        if ($negotiable) {
            $out .= ' (' . self::__('price_negotiable_short') . ')';
        }
        return $out;
    }

    public static function timeAgo(?string $datetime): string
    {
        if ($datetime === null || $datetime === '') {
            return '';
        }
        $ts = strtotime($datetime);
        if ($ts === false) {
            return '';
        }
        $diff = time() - $ts;
        $lang = Language::get();

        if ($diff < 60) {
            return self::__('time_just_now');
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return self::pluralTime($m, 'time_minutes', $lang);
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return self::pluralTime($h, 'time_hours', $lang);
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return self::pluralTime($d, 'time_days', $lang);
        }
        return date('Y-m-d', $ts);
    }

    private static function pluralTime(int $n, string $baseKey, string $lang): string
    {
        return str_replace(':n', (string) $n, self::__($baseKey));
    }

    public static function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    public static function jsonResponse(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function asset(string $path): string
    {
        return rtrim(PUBLIC_BASE_URL, '/') . '/public/assets/' . ltrim($path, '/');
    }

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * @return array{type:string,message:string}|null
     */
    public static function consumeFlash(): ?array
    {
        if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
            return null;
        }
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return [
            'type' => (string) ($f['type'] ?? 'info'),
            'message' => (string) ($f['message'] ?? ''),
        ];
    }

    public static function propertyDealLabel(string $dealType): string
    {
        return match ($dealType) {
            'rent' => self::__('deal_rent'),
            'daily_rent' => self::__('deal_daily'),
            default => self::__('deal_sale'),
        };
    }

    public static function propertyTypeLabel(string $type): string
    {
        return self::__('type_' . $type);
    }

    /**
     * @param array<string, mixed> $p
     */
    public static function formatPropertyPrice(array $p): string
    {
        $amount = isset($p['price']) ? (float) $p['price'] : null;
        $currency = (string) ($p['currency'] ?? 'USD');
        $negotiable = !empty($p['price_negotiable']);
        $base = self::formatPrice($amount, $currency, $negotiable);
        if (($p['deal_type'] ?? '') === 'daily_rent') {
            return $base . ' / ' . self::__('price_per_night');
        }
        return $base;
    }

    /**
     * Digits only for wa.me links.
     */
    public static function digitsOnly(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone) ?? '';
    }

    /**
     * @return list<array{name:string,type:string,tmp_name:string,error:int,size:int}>
     */
    public static function restructureUploadedFiles(string $field): array
    {
        if (empty($_FILES[$field])) {
            return [];
        }
        $f = $_FILES[$field];
        if (!is_array($f['name'])) {
            return $f['error'] !== UPLOAD_ERR_NO_FILE ? [$f] : [];
        }
        $out = [];
        $n = count($f['name']);
        for ($i = 0; $i < $n; $i++) {
            if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name' => $f['name'][$i],
                'type' => $f['type'][$i] ?? '',
                'tmp_name' => $f['tmp_name'][$i],
                'error' => (int) $f['error'][$i],
                'size' => (int) ($f['size'][$i] ?? 0),
            ];
        }
        return $out;
    }
}
