<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/User.php';

class AuthController
{
    public function registerForm(array $params = []): void
    {
        if (Auth::isLoggedIn()) {
            Helpers::redirect(BASE_URL . '/my/dashboard');
        }
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_register') . ' — InfoKobuleti';
        $meta['robots'] = 'noindex, nofollow';
        View::render('auth/register', ['meta' => $meta], 'auth');
    }

    public function register(array $params = []): void
    {
        if (Auth::isLoggedIn()) {
            Helpers::redirect(BASE_URL . '/my/dashboard');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/register');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/register');
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $whatsapp = trim((string) ($_POST['whatsapp_number'] ?? ''));
        $telegram = trim((string) ($_POST['telegram_username'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $pass2 = (string) ($_POST['password_confirm'] ?? '');

        if ($name === '' || mb_strlen($name) < 2) {
            Helpers::setFlash('error', Helpers::__('auth_error_name'));
            Helpers::redirect(BASE_URL . '/register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Helpers::setFlash('error', Helpers::__('auth_error_email'));
            Helpers::redirect(BASE_URL . '/register');
        }
        if (User::emailExists($email)) {
            Helpers::setFlash('error', Helpers::__('auth_error_email_taken'));
            Helpers::redirect(BASE_URL . '/register');
        }
        if (strlen($pass) < 8) {
            Helpers::setFlash('error', Helpers::__('auth_error_password_short'));
            Helpers::redirect(BASE_URL . '/register');
        }
        if (!hash_equals($pass, $pass2)) {
            Helpers::setFlash('error', Helpers::__('auth_error_password_match'));
            Helpers::redirect(BASE_URL . '/register');
        }
        if (Auth::rateLimitHit('register')) {
            Helpers::setFlash('error', Helpers::__('auth_rate_limit'));
            Helpers::redirect(BASE_URL . '/register');
        }

        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
        $uid = User::create($name, $email, $phone, $hash, $whatsapp !== '' ? $whatsapp : null, $telegram !== '' ? $telegram : null);
        $user = User::findById($uid);
        if ($user === null) {
            Helpers::setFlash('error', Helpers::__('auth_error_generic'));
            Helpers::redirect(BASE_URL . '/register');
        }
        Auth::loginWithUser($user);
        Helpers::setFlash('success', Helpers::__('register_success'));
        Helpers::redirect(BASE_URL . '/my/dashboard');
    }

    public function loginForm(array $params = []): void
    {
        if (Auth::isLoggedIn()) {
            Helpers::redirect(BASE_URL . '/my/dashboard');
        }
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_login') . ' — InfoKobuleti';
        $meta['robots'] = 'noindex, nofollow';
        View::render('auth/login', ['meta' => $meta], 'auth');
    }

    public function login(array $params = []): void
    {
        if (Auth::isLoggedIn()) {
            Helpers::redirect(BASE_URL . '/my/dashboard');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/login');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $user = User::findByEmail($email);
        if ($user === null || empty($user['password']) || !password_verify($pass, (string) $user['password'])) {
            if (Auth::rateLimitHit('login')) {
                Helpers::setFlash('error', Helpers::__('auth_rate_limit'));
                Helpers::redirect(BASE_URL . '/login');
            }
            Helpers::setFlash('error', Helpers::__('auth_error_credentials'));
            Helpers::redirect(BASE_URL . '/login');
        }
        if (empty($user['is_active'])) {
            Helpers::setFlash('error', Helpers::__('auth_error_inactive'));
            Helpers::redirect(BASE_URL . '/login');
        }

        Auth::loginWithUser($user);
        Helpers::setFlash('success', Helpers::__('login_success'));
        Helpers::redirect(BASE_URL . '/my/dashboard');
    }

    public function logout(array $params = []): void
    {
        Auth::logout();
        Helpers::redirect(BASE_URL . '/');
    }
}
