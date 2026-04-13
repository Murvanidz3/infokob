<?php
/**
 * Language Helper
 * Manages the multilingual system (KA/RU/EN)
 */

class Language {
    
    /**
     * Set the active language
     * Validates against supported languages, stores in session and cookie
     */
    public static function set($code) {
        if (!in_array($code, SUPPORTED_LANGS)) {
            $code = DEFAULT_LANG;
        }
        
        $_SESSION['lang'] = $code;
        setcookie('lang', $code, time() + (86400 * 30), '/'); // 30 days
    }
    
    /**
     * Get the current language code
     * Priority: session > cookie > default
     */
    public static function get() {
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], SUPPORTED_LANGS)) {
            return $_SESSION['lang'];
        }
        
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], SUPPORTED_LANGS)) {
            $_SESSION['lang'] = $_COOKIE['lang'];
            return $_COOKIE['lang'];
        }
        
        return DEFAULT_LANG;
    }
    
    /**
     * Load language file into global scope
     */
    public static function load() {
        $lang = self::get();
        $file = LANG_PATH . '/' . $lang . '.php';
        
        if (file_exists($file)) {
            $GLOBALS['_lang'] = require $file;
        } else {
            // Fallback to default language
            $GLOBALS['_lang'] = require LANG_PATH . '/' . DEFAULT_LANG . '.php';
        }
    }
    
    /**
     * Handle language switch request
     * Sets language and redirects back to referrer
     */
    public static function handleSwitch($code) {
        self::set($code);
        
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        redirect($referer);
    }
    
    /**
     * Get all language options for display
     */
    public static function getOptions() {
        return [
            'ka' => 'ქართული',
            'ru' => 'Русский',
            'en' => 'English'
        ];
    }
    
    /**
     * Get short language labels (for switcher)
     */
    public static function getShortLabels() {
        return [
            'ka' => 'KA',
            'ru' => 'RU',
            'en' => 'EN'
        ];
    }
    
    /**
     * Get the current language name
     */
    public static function getCurrentName() {
        $options = self::getOptions();
        return $options[self::get()] ?? 'ქართული';
    }
}
