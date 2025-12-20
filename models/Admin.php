<?php
/**
 * ProLedger - Admin Model
 * Model untuk autentikasi sistem
 */

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find admin by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Find admin by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Check if login access exists for a reference
     */
    public function findByReference($role, $referenceId) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE role = ? AND reference_id = ?");
        $stmt->execute([$role, $referenceId]);
        return $stmt->fetch();
    }

    /**
     * Create login access
     */
    public function createLoginAccess($role, $referenceId, $name, $email, $password) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO admins (role, reference_id, name, email, password) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $role,
            $referenceId,
            $name,
            $email,
            $hashedPassword
        ]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
