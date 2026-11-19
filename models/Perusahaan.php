<?php
/**
 * ProLedger - Perusahaan Model
 * Model untuk manajemen data perusahaan / mitra
 */

class Perusahaan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all companies (optionally filtered by user_id)
     */
    public function getAll($userId = null) {
        $sql = "SELECT p.*, COUNT(pk.id) as jumlah_paket, u.name as owner_name 
                FROM perusahaan p 
                LEFT JOIN paket_kegiatan pk ON p.id = pk.perusahaan_id 
                LEFT JOIN admins u ON p.user_id = u.id";
        
        $params = [];
        if ($userId !== null) {
            $sql .= " WHERE p.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " GROUP BY p.id ORDER BY p.nama ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get companies owned by a specific user
     */
    public function getByUser($userId) {
        return $this->getAll($userId);
    }
    
    /**
     * Get company by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, u.name as owner_name FROM perusahaan p LEFT JOIN admins u ON p.user_id = u.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Check if a user owns a company (or is admin)
     */
    public function isOwnerOrAdmin($perusahaanId, $userId, $userRole = 'user') {
        if ($userRole === 'admin') return true;
        
        $stmt = $this->db->prepare("SELECT id FROM perusahaan WHERE id = ? AND user_id = ?");
        $stmt->execute([$perusahaanId, $userId]);
        return (bool)$stmt->fetch();
    }
    
    /**
     * Create new company
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO perusahaan (user_id, nama, nama_direktur, jabatan_direktur, email, telepon, alamat, website, keterangan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'] ?? null,
            $data['nama'],
            $data['nama_direktur'] ?? null,
            $data['jabatan_direktur'] ?? 'Direktur',
            $data['email'] ?? null,
            $data['telepon'] ?? null,
            $data['alamat'] ?? null,
            $data['website'] ?? null,
            $data['keterangan'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update company
     */
    public function update($id, $data) {
        $fields = ["nama = ?", "email = ?", "telepon = ?", "alamat = ?"];
        $params = [
            $data['nama'],
            $data['email'] ?? null,
            $data['telepon'] ?? null,
            $data['alamat'] ?? null
        ];

        if (array_key_exists('nama_direktur', $data)) {
            $fields[] = "nama_direktur = ?";
            $params[] = $data['nama_direktur'] ?: null;
        }

        if (array_key_exists('jabatan_direktur', $data)) {
            $fields[] = "jabatan_direktur = ?";
            $params[] = $data['jabatan_direktur'] ?: 'Direktur';
        }

        if (array_key_exists('website', $data)) {
            $fields[] = "website = ?";
            $params[] = $data['website'] ?: null;
        }

        if (array_key_exists('keterangan', $data)) {
            $fields[] = "keterangan = ?";
            $params[] = $data['keterangan'] ?: null;
        }

        if (array_key_exists('user_id', $data) && $data['user_id'] !== null) {
            $fields[] = "user_id = ?";
            $params[] = $data['user_id'];
        }

        $params[] = $id;

        $stmt = $this->db->prepare("UPDATE perusahaan SET " . implode(", ", $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }
    
    /**
     * Delete company
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM perusahaan WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get packets associated with company
     */
    public function getPaketByPerusahaan($id) {
        $stmt = $this->db->prepare("SELECT * FROM paket_kegiatan WHERE perusahaan_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}
