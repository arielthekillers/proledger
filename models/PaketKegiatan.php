<?php
/**
 * ProLedger - PaketKegiatan Model
 * Model untuk manajemen paket kegiatan/kontrak
 */

class PaketKegiatan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all paket kegiatan with filters
     */
    public function getAll($filters = []) {
        // Updated to JOIN perusahaan
        $sql = "SELECT pk.*, p.nama as perusahaan_nama, p.email as perusahaan_email 
                FROM paket_kegiatan pk
                LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
                WHERE 1=1";
        $params = [];
        
        // Filter by status (simple value)
        if (!empty($filters['status'])) {
            $sql .= " AND pk.status = ?";
            $params[] = $filters['status'];
        }
        
        // Filter by name (search)
        if (!empty($filters['search'])) {
            $sql .= " AND (pk.nama_paket LIKE ? OR p.nama LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        // Filter by min pagu
        if (!empty($filters['min_pagu'])) {
            $sql .= " AND pk.pagu >= ?";
            $params[] = $filters['min_pagu'];
        }
        
        // Filter by max pagu
        if (!empty($filters['max_pagu'])) {
            $sql .= " AND pk.pagu <= ?";
            $params[] = $filters['max_pagu'];
        }
        
        $sql .= " ORDER BY pk.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        // Polyfill 'perusahaan' key for backward compatibility if needed, 
        // essentially overwrite the old string column with the joined name for display
        foreach ($results as &$row) {
            if ($row['perusahaan_nama']) {
                $row['perusahaan'] = $row['perusahaan_nama'];
            }
        }
        
        return $results;
    }
    
    /**
     * Get paket by ID with pemodal
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT pk.*, p.nama as perusahaan_nama, p.email as perusahaan_email, p.telepon as perusahaan_telepon 
            FROM paket_kegiatan pk
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            WHERE pk.id = ?
        ");
        $stmt->execute([$id]);
        $paket = $stmt->fetch();
        
        if ($paket) {
            // Apply name override
             if ($paket['perusahaan_nama']) {
                $paket['perusahaan'] = $paket['perusahaan_nama'];
            }
            
            // Get pemodal
            $paket['pemodal'] = $this->getPemodal($id);
        }
        
        return $paket;
    }
    
    /**
     * Create new paket
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Insert paket (using perusahaan_id)
            $stmt = $this->db->prepare("
                INSERT INTO paket_kegiatan (nama_paket, pagu, rab, jumlah_pemodal, status, perusahaan_id, perusahaan) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Keep the old 'perusahaan' column populated just in case, but use ID primarily
            // We need to fetch the name if we only have ID, OR just leave it empty/null if we fully switch
            // Logic: User selects ID.
            
            $perusahaanName = null;
            if(!empty($data['perusahaan_id'])) {
                 $stmtP = $this->db->prepare("SELECT nama FROM perusahaan WHERE id = ?");
                 $stmtP->execute([$data['perusahaan_id']]);
                 $p = $stmtP->fetch();
                 if ($p) $perusahaanName = $p['nama'];
            }
            
            $stmt->execute([
                $data['nama_paket'],
                $data['pagu'],
                $data['rab'],
                $data['jumlah_pemodal'],
                $data['status'] ?? 'Kontrak',
                $data['perusahaan_id'],
                $perusahaanName // Legacy column
            ]);
            
            $paket_id = $this->db->lastInsertId();
            
            // Add pemodal
            if (isset($data['pemodal']) && is_array($data['pemodal'])) {
                foreach ($data['pemodal'] as $user_id) {
                    $this->addPemodal($paket_id, $user_id);
                }
            }
            
            // Calculate modal
            $this->calculateModal($paket_id);
            
            $this->db->commit();
            return $paket_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update paket
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            $perusahaanName = null;
            if(!empty($data['perusahaan_id'])) {
                 $stmtP = $this->db->prepare("SELECT nama FROM perusahaan WHERE id = ?");
                 $stmtP->execute([$data['perusahaan_id']]);
                 $p = $stmtP->fetch();
                 if ($p) $perusahaanName = $p['nama'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE paket_kegiatan 
                SET nama_paket = ?, pagu = ?, rab = ?, jumlah_pemodal = ?, 
                    status = ?, perusahaan_id = ?, perusahaan = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nama_paket'],
                $data['pagu'],
                $data['rab'],
                $data['jumlah_pemodal'],
                $data['status'] ?? 'Kontrak',
                $data['perusahaan_id'],
                $perusahaanName,
                $id
            ]);
            
            // Update pemodal
            if (isset($data['pemodal']) && is_array($data['pemodal'])) {
                $this->removeAllPemodal($id);
                foreach ($data['pemodal'] as $user_id) {
                    $this->addPemodal($id, $user_id);
                }
            }
            
            // Recalculate modal
            $this->calculateModal($id);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete paket
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM paket_kegiatan WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get pemodal for a paket
     */
    public function getPemodal($paket_id) {
        $stmt = $this->db->prepare("
            SELECT u.*, pp.created_at as joined_at
            FROM users u
            JOIN paket_pemodal pp ON u.id = pp.user_id
            WHERE pp.paket_id = ?
            ORDER BY u.nama ASC
        ");
        $stmt->execute([$paket_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add pemodal to paket
     */
    public function addPemodal($paket_id, $user_id) {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO paket_pemodal (paket_id, user_id) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$paket_id, $user_id]);
    }
    
    /**
     * Remove all pemodal from paket
     */
    public function removeAllPemodal($paket_id) {
        $stmt = $this->db->prepare("DELETE FROM paket_pemodal WHERE paket_id = ?");
        return $stmt->execute([$paket_id]);
    }
    
    /**
     * Calculate modal for all pemodal in a paket
     */
    public function calculateModal($paket_id) {
        $paket = $this->getById($paket_id);
        if (!$paket) return false;
        
        $pagu = $paket['pagu'];
        $rab = $paket['rab'];
        $jumlah_pemodal = $paket['jumlah_pemodal'];
        
        // Calculate per person
        $modal_pasal = calculateModalPasal($pagu, $jumlah_pemodal);
        $modal_belanja = calculateModalBelanja($rab, $jumlah_pemodal);
        $total_modal = $modal_pasal + $modal_belanja;
        
        $stmt = $this->db->prepare("DELETE FROM modal_detail WHERE paket_id = ?");
        $stmt->execute([$paket_id]);
        
        $stmt = $this->db->prepare("
            INSERT INTO modal_detail (paket_id, user_id, modal_pasal, modal_belanja, total_modal)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($paket['pemodal'] as $pemodal) {
            $stmt->execute([
                $paket_id,
                $pemodal['id'],
                $modal_pasal,
                $modal_belanja,
                $total_modal
            ]);
        }
        
        return true;
    }
    
    public function getSummary($paket_id) {
        $stmt = $this->db->prepare("
            SELECT SUM(total_modal) as total_modal
            FROM modal_detail
            WHERE paket_id = ?
        ");
        $stmt->execute([$paket_id]);
        $modal = $stmt->fetch();
        
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
                SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as total_keluar
            FROM transaksi
            WHERE paket_id = ?
        ");
        $stmt->execute([$paket_id]);
        $transaksi = $stmt->fetch();
        
        $total_modal = $modal['total_modal'] ?? 0;
        $total_masuk = $transaksi['total_masuk'] ?? 0;
        $total_keluar = $transaksi['total_keluar'] ?? 0;
        $saldo = $total_masuk - $total_keluar;
        $laba_rugi = $saldo - $total_modal;
        
        return [
            'total_modal' => $total_modal,
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
            'saldo' => $saldo,
            'laba_rugi' => $laba_rugi
        ];
    }
}
