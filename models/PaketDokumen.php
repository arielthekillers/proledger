<?php
/**
 * ProLedger - PaketDokumen Model
 * Model untuk manajemen berkas dokumen paket kegiatan (BAST, Kontrak, dll.)
 */

class PaketDokumen {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ambil seluruh berkas dokumen berdasarkan paket_id
     */
    public function getByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT d.*, a.name as uploader_nama 
            FROM paket_dokumen d
            LEFT JOIN admins a ON d.uploaded_by = a.id
            WHERE d.paket_id = ? 
            ORDER BY d.created_at DESC, d.id DESC
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil dokumen berdasarkan id
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT d.*, a.name as uploader_nama 
            FROM paket_dokumen d
            LEFT JOIN admins a ON d.uploaded_by = a.id
            WHERE d.id = ? 
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tambah dokumen baru
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO paket_dokumen (
                paket_id, jenis_dokumen, nama_dokumen, nomor_dokumen, tanggal_dokumen,
                nama_file_asli, file_path, file_size, file_ext, mime_type, keterangan, uploaded_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            (int)$data['paket_id'],
            !empty($data['jenis_dokumen']) ? trim($data['jenis_dokumen']) : 'BAST',
            trim($data['nama_dokumen']),
            !empty($data['nomor_dokumen']) ? trim($data['nomor_dokumen']) : null,
            !empty($data['tanggal_dokumen']) ? $data['tanggal_dokumen'] : null,
            $data['nama_file_asli'],
            $data['file_path'],
            (int)($data['file_size'] ?? 0),
            strtolower($data['file_ext'] ?? ''),
            $data['mime_type'] ?? null,
            !empty($data['keterangan']) ? trim($data['keterangan']) : null,
            !empty($data['uploaded_by']) ? (int)$data['uploaded_by'] : null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Hapus dokumen berdasarkan id
     */
    public function delete($id) {
        $doc = $this->getById($id);
        if (!$doc) return false;
        
        // Hapus fisik file jika ada
        if (!empty($doc['file_path'])) {
            require_once __DIR__ . '/../helpers/Uploader.php';
            Uploader::deleteFile($doc['file_path']);
        }
        
        $stmt = $this->db->prepare("DELETE FROM paket_dokumen WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
    
    /**
     * Hitung total dokumen berdasarkan paket_id
     */
    public function countByPaketId($paketId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM paket_dokumen WHERE paket_id = ?");
        $stmt->execute([(int)$paketId]);
        return (int)$stmt->fetchColumn();
    }
}
