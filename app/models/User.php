<?php
/**
 * User Model
 * Handles all database operations for users
 */

class User {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new user
     */
    public function create($data) {
        $sql = "
            INSERT INTO users (name, email, phone, whatsapp_number, telegram_username, password, role)
            VALUES (:name, :email, :phone, :whatsapp, :telegram, :password, 'user')
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'      => $data['name'],
            ':email'     => $data['email'],
            ':phone'     => $data['phone'] ?? '',
            ':whatsapp'  => $data['whatsapp_number'] ?? '',
            ':telegram'  => $data['telegram_username'] ?? '',
            ':password'  => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update user profile
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowed = ['name', 'phone', 'whatsapp_number', 'telegram_username', 'avatar'];
        
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }
        
        // Handle password change
        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        if (empty($fields)) return false;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Check if email exists (for registration validation)
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        if (!$user['is_active']) return false;
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get listings count for a user
     */
    public function getListingsCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM properties WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }
    
    // ─── Admin Methods ─────────────────────────────────────
    
    /**
     * Get all users (admin)
     */
    public function getAll() {
        $sql = "
            SELECT u.*, 
                   (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id) as listings_count
            FROM users u
            ORDER BY u.created_at DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Toggle user active status
     */
    public function toggleActive($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = :id AND role != 'admin'");
        return $stmt->execute([':id' => $id]);
    }
}
