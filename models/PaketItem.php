<?php
/**
 * ProLedger - PaketItem Model
 * Model untuk manajemen item / spesifikasi dalam paket kegiatan
 */

class PaketItem {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ambil seluruh item berdasarkan paket_id
     */
    public function getByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT * FROM paket_items 
            WHERE paket_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil item berdasarkan id
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM paket_items 
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tambah item baru
     */
    public function create($data) {
        $jumlah = (float)($data['jumlah'] ?? 1);
        $hargaSatuan = (float)($data['harga_satuan'] ?? 0);
        $totalHarga = (float)($data['total_harga'] ?? round($jumlah * $hargaSatuan));

        $stmt = $this->db->prepare("
            INSERT INTO paket_items (
                paket_id, komponen, spesifikasi, satuan, jumlah, harga_satuan, total_harga
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            (int)$data['paket_id'],
            trim($data['komponen']),
            !empty($data['spesifikasi']) ? trim($data['spesifikasi']) : null,
            !empty($data['satuan']) ? trim($data['satuan']) : 'Unit',
            $jumlah,
            $hargaSatuan,
            $totalHarga
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Perbarui item
     */
    public function update($id, $data) {
        $jumlah = (float)($data['jumlah'] ?? 1);
        $hargaSatuan = (float)($data['harga_satuan'] ?? 0);
        $totalHarga = (float)($data['total_harga'] ?? round($jumlah * $hargaSatuan));

        $stmt = $this->db->prepare("
            UPDATE paket_items SET 
                komponen = ?,
                spesifikasi = ?,
                satuan = ?,
                jumlah = ?,
                harga_satuan = ?,
                total_harga = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            trim($data['komponen']),
            !empty($data['spesifikasi']) ? trim($data['spesifikasi']) : null,
            !empty($data['satuan']) ? trim($data['satuan']) : 'Unit',
            $jumlah,
            $hargaSatuan,
            $totalHarga,
            (int)$id
        ]);
    }
    
    /**
     * Hapus item dan seluruh alokasi distribusinya secara atomik
     */
    public function delete($id) {
        $itemId = (int)$id;
        try {
            $this->db->beginTransaction();

            // 1. Hapus semua alokasi distribusi yang merujuk ke item ini
            $stmtDist = $this->db->prepare("DELETE FROM paket_distribusi WHERE item_id = ?");
            $stmtDist->execute([$itemId]);

            // 2. Hapus item spesifikasi
            $stmt = $this->db->prepare("DELETE FROM paket_items WHERE id = ?");
            $res = $stmt->execute([$itemId]);

            $this->db->commit();
            return $res;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    
    /**
     * Ringkasan total jumlah dan total nilai item untuk paket
     */
    public function getSummaryByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_item,
                COALESCE(SUM(jumlah), 0) as total_qty,
                COALESCE(SUM(total_harga), 0) as grand_total
            FROM paket_items 
            WHERE paket_id = ?
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
