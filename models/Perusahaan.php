<?php
/**
 * ProLedger - Perusahaan Model
 */

class Perusahaan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        // Get all companies with count of packets
        $sql = "SELECT p.*, COUNT(pk.id) as jumlah_paket 
                FROM perusahaan p 
                LEFT JOIN paket_kegiatan pk ON p.id = pk.perusahaan_id 
                GROUP BY p.id 
                ORDER BY p.nama ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM perusahaan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO perusahaan (nama, email, telepon, alamat) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nama'],
            $data['email'] ?? null,
            $data['telepon'] ?? null,
            $data['alamat'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE perusahaan 
            SET nama = ?, email = ?, telepon = ?, alamat = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nama'],
            $data['email'] ?? null,
            $data['telepon'] ?? null,
            $data['alamat'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        // Only delete if no associated packets (or set them to null)
        // For safety, let's just check first or cascade set null if defined.
        // Assuming user wants simple delete.
        $stmt = $this->db->prepare("DELETE FROM perusahaan WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPaketByPerusahaan($id) {
         $stmt = $this->db->prepare("SELECT * FROM paket_kegiatan WHERE perusahaan_id = ? ORDER BY created_at DESC");
         $stmt->execute([$id]);
         return $stmt->fetchAll();
    }
}
