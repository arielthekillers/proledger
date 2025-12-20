<?php
/**
 * ProLedger - User Model
 * Model untuk manajemen pemodal/investor
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all users
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY nama ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (nama, email, telepon) 
            VALUES (?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['nama'],
            $data['email'] ?? null,
            $data['telepon'] ?? null
        ]);
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET nama = ?, email = ?, telepon = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nama'],
            $data['email'] ?? null,
            $data['telepon'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get user's paket kegiatan
     */
    public function getPaketKegiatan($user_id) {
        $stmt = $this->db->prepare("
            SELECT pk.*, pp.created_at as joined_at
            FROM paket_kegiatan pk
            JOIN paket_pemodal pp ON pk.id = pp.paket_id
            WHERE pp.user_id = ?
            ORDER BY pk.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get user's total modal across all paket
     */
    public function getTotalModal($user_id) {
        $stmt = $this->db->prepare("
            SELECT SUM(total_modal) as total
            FROM modal_detail
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
