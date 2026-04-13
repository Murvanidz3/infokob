<?php
/**
 * Authentication Helper
 * Session-based auth with rate limiting
 */

class Auth {
    
    /**
     * Log in a user (store user data in session)
     */
    public static function login($user) {
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'phone'    => $user['phone'] ?? '',
            'whatsapp_number' => $user['whatsapp_number'] ?? '',
            'telegram_username' => $user['telegram_username'] ?? '',
            'avatar'   => $user['avatar'] ?? '',
            'role'     => $user['role'],
        ];
        
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        
        // Clear rate limiting on successful login
        self::clearAttempts('login');
    }
    
    /**
     * Log out the current user
     */
    public static function logout() {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }
    
    /**
     * Get the current user data from session
     * @return array|null
     */
    public static function user() {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Get current user ID
     * @return int|null
     */
    public static function id() {
        return $_SESSION['user']['id'] ?? null;
    }
    
    /**
     * Check if a user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    /**
     * Check if the current user is an admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && ($_SESSION['user']['role'] === 'admin');
    }
    
    /**
     * Require authentication — redirect to login if not authenticated
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = currentUrl();
            flash('error', __('error_unauthorized'));
            redirect(BASE_URL . '/login');
            exit;
        }
    }
    
    /**
     * Require admin role — show 404 if not admin
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            http_response_code(404);
            require VIEW_PATH . '/errors/404.php';
            exit;
        }
    }
    
    /**
     * Get redirect URL after login (if set)
     */
    public static function getRedirectUrl() {
        $url = $_SESSION['redirect_after_login'] ?? BASE_URL;
        unset($_SESSION['redirect_after_login']);
        return $url;
    }
    
    /**
     * Check if owner of a property
     */
    public static function isOwner($userId) {
        return self::isLoggedIn() && (self::id() === (int)$userId);
    }
    
    /**
     * Check if user can edit a property (owner or admin)
     */
    public static function canEdit($userId) {
        return self::isAdmin() || self::isOwner($userId);
    }
    
    // ─── Rate Limiting ─────────────────────────────────────
    
    /**
     * Record a login/register attempt
     */
    public static function recordAttempt($type = 'login') {
        $key = 'rate_limit_' . $type;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first_at' => time()];
        }
        $_SESSION[$key]['count']++;
    }
    
    /**
     * Check if rate limit is exceeded
     */
    public static function isRateLimited($type = 'login') {
        $key = 'rate_limit_' . $type;
        if (!isset($_SESSION[$key])) return false;
        
        $data = $_SESSION[$key];
        
        // Reset if lockout time has passed
        if (time() - $data['first_at'] > LOGIN_LOCKOUT_TIME) {
            self::clearAttempts($type);
            return false;
        }
        
        return $data['count'] >= MAX_LOGIN_ATTEMPTS;
    }
    
    /**
     * Clear rate limiting attempts
     */
    public static function clearAttempts($type = 'login') {
        unset($_SESSION['rate_limit_' . $type]);
    }
    
    /**
     * Refresh user session data (after profile update)
     */
    public static function refreshSession($userData) {
        if (self::isLoggedIn()) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $userData);
        }
    }
}
