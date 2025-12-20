<?php
/**
 * ProLedger - Transaksi Model
 * Model untuk manajemen transaksi masuk dan keluar
 */

class Transaksi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all transaksi for a paket
     */
    /**
     * Get all transaksi with paket info
     */
    public function getAllWithPaket($filters = []) {
        $sql = "SELECT t.*, pk.nama_paket 
                FROM transaksi t
                JOIN paket_kegiatan pk ON t.paket_id = pk.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['paket_id'])) {
            $sql .= " AND t.paket_id = ?";
            $params[] = $filters['paket_id'];
        }

        // Search: filter by paket name or kategori
        if (!empty($filters['search'])) {
            $sql .= " AND (pk.nama_paket LIKE ? OR t.kategori LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        // Filter Type
        if (!empty($filters['tipe'])) {
            $sql .= " AND t.tipe = ?";
            $params[] = $filters['tipe'];
        }
        
        // Filter Kategori
        if (!empty($filters['kategori'])) {
            $sql .= " AND t.kategori = ?";
            $params[] = $filters['kategori'];
        }
        
        // Filter Date Range
        if (!empty($filters['start_date'])) {
            $sql .= " AND t.tanggal >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND t.tanggal <= ?";
            $params[] = $filters['end_date'];
        }
        
        // Order by date desc
        $sql .= " ORDER BY t.tanggal DESC, t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all transaksi for a paket
     */
    public function getByPaket($paket_id, $tipe = null) {
        $sql = "SELECT * FROM transaksi WHERE paket_id = ?";
        
        if ($tipe) {
            $sql .= " AND tipe = ?";
            $stmt = $this->db->prepare($sql . " ORDER BY tanggal DESC, created_at DESC");
            $stmt->execute([$paket_id, $tipe]);
        } else {
            $stmt = $this->db->prepare($sql . " ORDER BY tanggal DESC, created_at DESC");
            $stmt->execute([$paket_id]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get transaksi by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM transaksi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new transaksi
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO transaksi (paket_id, tanggal, tipe, kategori, jumlah, keterangan) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['paket_id'],
            $data['tanggal'],
            $data['tipe'],
            $data['kategori'] ?? null,
            $data['jumlah'],
            $data['keterangan'] ?? null
        ]);
    }
    
    /**
     * Update transaksi
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE transaksi 
            SET tanggal = ?, tipe = ?, kategori = ?, jumlah = ?, keterangan = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['tanggal'],
            $data['tipe'],
            $data['kategori'] ?? null,
            $data['jumlah'],
            $data['keterangan'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete transaksi
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM transaksi WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get summary for a paket
     */
    public function getSummary($paket_id) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
                SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as total_keluar,
                COUNT(CASE WHEN tipe = 'masuk' THEN 1 END) as count_masuk,
                COUNT(CASE WHEN tipe = 'keluar' THEN 1 END) as count_keluar
            FROM transaksi
            WHERE paket_id = ?
        ");
        $stmt->execute([$paket_id]);
        $result = $stmt->fetch();
        
        $total_masuk = $result['total_masuk'] ?? 0;
        $total_keluar = $result['total_keluar'] ?? 0;
        
        return [
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
            'count_masuk' => $result['count_masuk'] ?? 0,
            'count_keluar' => $result['count_keluar'] ?? 0,
            'saldo' => $total_masuk - $total_keluar
        ];
    }
    
    /**
     * Get transaksi by kategori for a paket
     */
    public function getByKategori($paket_id) {
        $stmt = $this->db->prepare("
            SELECT kategori, tipe, SUM(jumlah) as total, COUNT(*) as count
            FROM transaksi
            WHERE paket_id = ? AND kategori IS NOT NULL
            GROUP BY kategori, tipe
            ORDER BY total DESC
        ");
        $stmt->execute([$paket_id]);
        return $stmt->fetchAll();
    }
}
