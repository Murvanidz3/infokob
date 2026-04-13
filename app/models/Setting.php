<?php
/**
 * Setting Model
 * Handles key-value site settings from database
 */

class Setting {
    private PDO $db;
    private static array $cache = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get a setting value
     */
    public function get($key, $default = '') {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE key_name = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        
        $value = $row ? $row['value'] : $default;
        self::$cache[$key] = $value;
        
        return $value;
    }
    
    /**
     * Set a setting value
     */
    public function set($key, $value) {
        $sql = "INSERT INTO settings (key_name, value) VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE value = :value2, updated_at = NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':key' => $key, ':value' => $value, ':value2' => $value]);
        
        self::$cache[$key] = $value;
    }
    
    /**
     * Get all settings
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM settings ORDER BY key_name");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['key_name']] = $row['value'];
        }
        return $settings;
    }
    
    /**
     * Update multiple settings at once
     */
    public function updateBulk($data) {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    /**
     * Get site name in current language
     */
    public function getSiteName() {
        $lang = Language::get();
        return $this->get('site_name_' . $lang, SITE_NAME);
    }
}
