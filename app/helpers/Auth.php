<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once __DIR__ . '/Helpers.php';

class Auth
{
    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
    }

    public static function login(int $userId, string $role): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;
    }

    /**
     * @param array<string, mixed> $user users table row
     */
    public static function loginWithUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = (string) $user['role'];
        $_SESSION['user_name'] = (string) $user['name'];
        $_SESSION['user_email'] = (string) $user['email'];
    }

    public static function userName(): string
    {
        return (string) ($_SESSION['user_name'] ?? '');
    }

    public static function userEmail(): string
    {
        return (string) ($_SESSION['user_email'] ?? '');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            Helpers::redirect(PUBLIC_BASE_URL . '/login');
        }
    }

    /**
     * Guests go to public login; logged-in non-admins get 404 (no panel disclosure).
     */
    public static function requireAdmin(): void
    {
        if (!self::isLoggedIn()) {
            Helpers::redirect(PUBLIC_BASE_URL . '/login');
        }
        if (!self::isAdmin()) {
            http_response_code(404);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>404</title></head><body><p>Not found.</p></body></html>';
            exit;
        }
    }

    public static function rateLimitHit(string $action): bool
    {
        $key = 'rate_' . $action;
        $now = time();
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'window_start' => $now];
        }
        $data = &$_SESSION[$key];
        if ($now - (int) $data['window_start'] > RATE_LIMIT_WINDOW_SEC) {
            $data['count'] = 0;
            $data['window_start'] = $now;
        }
        $data['count']++;
        return $data['count'] > RATE_LIMIT_ATTEMPTS;
    }
}
