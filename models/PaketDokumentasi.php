<?php
/**
 * ProLedger - PaketDokumentasi Model
 * Model untuk manajemen foto dokumentasi pengiriman & serah terima barang per alokasi
 */

class PaketDokumentasi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ambil seluruh foto dokumentasi berdasarkan paket_id
     */
    public function getByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT doc.*, a.name as uploader_nama 
            FROM paket_dokumentasi doc
            LEFT JOIN admins a ON doc.uploaded_by = a.id
            WHERE doc.paket_id = ? 
            ORDER BY doc.created_at ASC, doc.id ASC
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil foto dokumentasi yang dikelompokkan berdasarkan distribusi_id
     */
    public function getGroupedByDistribusi($paketId) {
        $all = $this->getByPaketId($paketId);
        $grouped = [];
        foreach ($all as $item) {
            $distId = (int)$item['distribusi_id'];
            if (!isset($grouped[$distId])) {
                $grouped[$distId] = [];
            }
            $grouped[$distId][] = $item;
        }
        return $grouped;
    }
    
    /**
     * Ambil foto dokumentasi spesifik untuk satu baris alokasi distribusi
     */
    public function getByDistribusiId($distribusiId) {
        $stmt = $this->db->prepare("
            SELECT doc.*, a.name as uploader_nama 
            FROM paket_dokumentasi doc
            LEFT JOIN admins a ON doc.uploaded_by = a.id
            WHERE doc.distribusi_id = ? 
            ORDER BY doc.created_at ASC, doc.id ASC
        ");
        $stmt->execute([(int)$distribusiId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil 1 record dokumentasi berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT doc.*, a.name as uploader_nama 
            FROM paket_dokumentasi doc
            LEFT JOIN admins a ON doc.uploaded_by = a.id
            WHERE doc.id = ? 
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Simpan data foto dokumentasi baru
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO paket_dokumentasi (
                paket_id, distribusi_id, nama_file_asli,
                file_path, thumb_path, file_size, file_ext, mime_type, keterangan, uploaded_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            (int)$data['paket_id'],
            (int)$data['distribusi_id'],
            trim($data['nama_file_asli'] ?? ''),
            trim($data['file_path']),
            !empty($data['thumb_path']) ? trim($data['thumb_path']) : null,
            (int)($data['file_size'] ?? 0),
            strtolower($data['file_ext'] ?? ''),
            $data['mime_type'] ?? null,
            !empty($data['keterangan']) ? trim($data['keterangan']) : null,
            !empty($data['uploaded_by']) ? (int)$data['uploaded_by'] : null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Hapus 1 foto dokumentasi berdasarkan ID (termasuk file fisiknya)
     */
    public function delete($id) {
        $doc = $this->getById($id);
        if (!$doc) return false;
        
        require_once __DIR__ . '/../helpers/Uploader.php';
        Uploader::deleteDocumentationImage($doc['file_path'] ?? null, $doc['thumb_path'] ?? null);
        
        $stmt = $this->db->prepare("DELETE FROM paket_dokumentasi WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
    
    /**
     * Hapus semua foto dokumentasi milik distribusi tertentu
     */
    public function deleteByDistribusiId($distribusiId) {
        $photos = $this->getByDistribusiId($distribusiId);
        if (!empty($photos)) {
            require_once __DIR__ . '/../helpers/Uploader.php';
            foreach ($photos as $p) {
                Uploader::deleteDocumentationImage($p['file_path'] ?? null, $p['thumb_path'] ?? null);
            }
        }
        $stmt = $this->db->prepare("DELETE FROM paket_dokumentasi WHERE distribusi_id = ?");
        return $stmt->execute([(int)$distribusiId]);
    }
    
    /**
     * Hitung total foto dokumentasi dalam sebuah paket
     */
    public function countByPaketId($paketId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM paket_dokumentasi WHERE paket_id = ?");
        $stmt->execute([(int)$paketId]);
        return (int)$stmt->fetchColumn();
    }
}
