<?php
/**
 * ProLedger - Admin Model
 * Model untuk autentikasi dan manajemen pengguna sistem
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
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Find admin by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Check if login access exists for a reference
     */
    public function findByReference($role, $referenceId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? AND reference_id = ?");
        $stmt->execute([$role, $referenceId]);
        return $stmt->fetch();
    }

    /**
     * Create login access (for legacy pemodal/perusahaan generator)
     */
    public function createLoginAccess($role, $referenceId, $name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (role, reference_id, name, email, password, status) 
            VALUES (?, ?, ?, ?, ?, 'active')
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
     * Register a new user (self-registration)
     */
    public function register($name, $email, $phone, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, phone, password, role, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        
        return $stmt->execute([
            $name,
            $email,
            $phone ?: null,
            $hashedPassword,
            $role
        ]);
    }

    /**
     * Admin create new user directly
     */
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $status = $data['status'] ?? 'active';
        $role = $data['role'] ?? 'user';
        $phone = $data['phone'] ?? null;
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, phone, password, role, status, reference_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $phone,
            $hashedPassword,
            $role,
            $status,
            $data['reference_id'] ?? null
        ]);
    }

    /**
     * Update user details
     */
    public function update($id, $data) {
        $fields = ["name = ?", "email = ?", "phone = ?", "role = ?", "status = ?"];
        $params = [
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['role'],
            $data['status']
        ];

        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $params[] = $id;

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update status only (approve / reject / activate / deactivate)
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Update user profile (name, email, phone)
     */
    public function updateProfile($id, $name, $email, $phone = null) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $phone ?: null, (int)$id]);
    }

    /**
     * Update password
     */
    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get pending registrations count
     */
    public function getPendingCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE status = 'pending'");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get all users with optional filters
     */
    public function getAll($filter = []) {
        $sql = "SELECT id, name, email, phone, role, status, reference_id, created_at, updated_at FROM users WHERE 1=1";
        $params = [];

        if (!empty($filter['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filter['status'];
        }

        if (!empty($filter['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filter['role'];
        }

        if (!empty($filter['search'])) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = '%' . $filter['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
