<?php
/**
 * Database Connection Singleton
 * PDO connection with UTF-8, error mode exception, fetch assoc
 */

class Database {
    private static ?PDO $instance = null;
    
    /**
     * Get the singleton PDO instance
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $name = defined('DB_NAME') ? DB_NAME : 'infokobuleti';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $charset = 'utf8mb4';
            
            $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    die('Database connection failed: ' . $e->getMessage());
                }
                die('Database connection failed. Please try again later.');
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
