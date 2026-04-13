<?php
/**
 * Auth Controller
 * Login, Register, Logout
 */

class AuthController {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function loginForm() {
        if (Auth::isLoggedIn()) {
            redirect(BASE_URL . '/my/dashboard');
        }
        
        SEO::set(__('login_title') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/auth/login.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/auth.php';
    }
    
    public function login() {
        verify_csrf();
        
        if (Auth::isRateLimited('login')) {
            flash('error', __('error_too_many_attempts'));
            redirect(BASE_URL . '/login');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            flash('error', __('error_required'));
            redirect(BASE_URL . '/login');
        }
        
        $user = $this->userModel->verifyPassword($email, $password);
        
        if ($user) {
            Auth::login($user);
            flash('success', __('login_success'));
            
            // If admin, redirect to admin panel
            if ($user['role'] === 'admin') {
                redirect(ADMIN_URL);
            }
            
            redirect(Auth::getRedirectUrl());
        } else {
            Auth::recordAttempt('login');
            storeOldInput();
            flash('error', __('error_login'));
            redirect(BASE_URL . '/login');
        }
    }
    
    public function registerForm() {
        if (Auth::isLoggedIn()) {
            redirect(BASE_URL . '/my/dashboard');
        }
        
        SEO::set(__('register_title') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/auth/register.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/auth.php';
    }
    
    public function register() {
        verify_csrf();
        
        if (Auth::isRateLimited('register')) {
            flash('error', __('error_too_many_attempts'));
            redirect(BASE_URL . '/register');
        }
        
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $telegram = trim($_POST['telegram_username'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = __('error_required');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = __('error_invalid_email');
        if (strlen($password) < 6) $errors[] = __('error_password_short');
        if ($password !== $confirm) $errors[] = __('error_password_match');
        if ($this->userModel->emailExists($email)) $errors[] = __('error_email_taken');
        
        if (!empty($errors)) {
            storeOldInput();
            flash('error', implode('<br>', $errors));
            redirect(BASE_URL . '/register');
        }
        
        Auth::recordAttempt('register');
        
        $userId = $this->userModel->create([
            'name'               => $name,
            'email'              => $email,
            'phone'              => $phone,
            'password'           => $password,
            'whatsapp_number'    => $whatsapp,
            'telegram_username'  => $telegram,
        ]);
        
        // Auto-login
        $user = $this->userModel->findById($userId);
        Auth::login($user);
        
        flash('success', __('register_success'));
        redirect(BASE_URL . '/my/dashboard');
    }
    
    public function logout() {
        Auth::logout();
        flash('success', __('logout_success'));
        redirect(BASE_URL);
    }
}
