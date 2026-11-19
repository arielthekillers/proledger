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
        // Updated to JOIN perusahaan and paket_group
        $sql = "SELECT pk.*, p.nama as perusahaan_nama, p.email as perusahaan_email,
                       pg.nama_group, pg.kode_group 
                FROM paket_kegiatan pk
                LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
                LEFT JOIN paket_group pg ON pk.group_id = pg.id
                WHERE 1=1";
        $params = [];
        
        // Filter by perusahaan_id (Single Active Company Architecture)
        if (!empty($filters['perusahaan_id'])) {
            $sql .= " AND pk.perusahaan_id = ?";
            $params[] = $filters['perusahaan_id'];
        }

        // Filter by group_id
        if (!empty($filters['group_id'])) {
            $sql .= " AND pk.group_id = ?";
            $params[] = $filters['group_id'];
        }

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
     * Generate unique 5-character alphanumeric code (letters & numbers)
     */
    public function generateUniqueKode() {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;
        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM paket_kegiatan WHERE kode_paket = ?");
        
        do {
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $chars[random_int(0, $max)];
            }
            $checkStmt->execute([$code]);
            $exists = $checkStmt->fetchColumn() > 0;
        } while ($exists);
        
        return $code;
    }

    /**
     * Get paket by ID or 5-digit Kode
     */
    public function getById($idOrKode) {
        $stmt = $this->db->prepare("
            SELECT pk.*, p.nama as perusahaan_nama, p.email as perusahaan_email, p.telepon as perusahaan_telepon,
                   pg.nama_group, pg.kode_group 
            FROM paket_kegiatan pk
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            LEFT JOIN paket_group pg ON pk.group_id = pg.id
            WHERE pk.kode_paket = ? OR pk.id = ?
            LIMIT 1
        ");
        $stmt->execute([$idOrKode, is_numeric($idOrKode) ? (int)$idOrKode : 0]);
        $paket = $stmt->fetch();
        
        if ($paket) {
            // Apply name override
             if ($paket['perusahaan_nama']) {
                $paket['perusahaan'] = $paket['perusahaan_nama'];
            }
            
            // Get pemodal
            $paket['pemodal'] = [];
        }
        
        return $paket;
    }
    
    /**
     * Create new paket
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();

            $pagu = (float)($data['pagu'] ?? 0);
            $paguNego = (!empty($data['pagu_nego']) && (float)$data['pagu_nego'] > 0) ? (float)$data['pagu_nego'] : null;
            $acuan = $paguNego !== null ? $paguNego : $pagu;

            $persenPasal = isset($data['persen_pasal']) && $data['persen_pasal'] !== '' ? (float)$data['persen_pasal'] : 13.0;
            $persenAdmin = isset($data['persen_admin']) && $data['persen_admin'] !== '' ? (float)$data['persen_admin'] : 2.0;

            $dpp = calculateDPP($acuan);
            $pasal = calculatePasal($dpp, $persenPasal);
            $admin = calculateAdmin($dpp, $persenAdmin);
            $totalPasal = $pasal + $admin;
            
            $setorAwal = (isset($data['setor_awal']) && $data['setor_awal'] !== '' && $data['setor_awal'] !== null) ? (float)$data['setor_awal'] : null;
            $selisih = $setorAwal !== null ? round($setorAwal - $totalPasal) : null;
            $rab = !empty($data['rab']) ? (float)$data['rab'] : 0;
            
            // Inisiasi 5 digit kode huruf & angka acak
            $kodePaket = !empty($data['kode_paket']) ? strtoupper($data['kode_paket']) : $this->generateUniqueKode();

            // Insert paket
            $stmt = $this->db->prepare("
                INSERT INTO paket_kegiatan (
                    kode_paket, nama_paket, nomor_sp, kab_kota, pagu, pagu_nego, dpp, pasal_13, admin_2, total_pasal,
                    persen_pasal, persen_admin,
                    setor_awal, selisih, rab, status, progres_barang,
                    perusahaan_id, group_id, skema_id, perusahaan, penanggung_jawab
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $perusahaanName = null;
            if(!empty($data['perusahaan_id'])) {
                 $stmtP = $this->db->prepare("SELECT nama FROM perusahaan WHERE id = ?");
                 $stmtP->execute([$data['perusahaan_id']]);
                 $p = $stmtP->fetch();
                 if ($p) $perusahaanName = $p['nama'];
            }
            
            $stmt->execute([
                $kodePaket,
                $data['nama_paket'],
                !empty($data['nomor_sp']) ? trim($data['nomor_sp']) : null,
                !empty($data['kab_kota']) ? trim($data['kab_kota']) : null,
                $pagu,
                $paguNego,
                $dpp,
                $pasal,
                $admin,
                $totalPasal,
                $persenPasal,
                $persenAdmin,
                $setorAwal,
                $selisih,
                $rab,
                $data['status'] ?? 'Proses',
                $data['progres_barang'] ?? 'Belum',
                $data['perusahaan_id'] ?? null,
                !empty($data['group_id']) ? (int)$data['group_id'] : null,
                !empty($data['skema_id']) ? (int)$data['skema_id'] : null,
                $perusahaanName, // Legacy column
                $data['penanggung_jawab'] ?? null
            ]);
            
            $paket_id = $this->db->lastInsertId();
            
            $this->db->commit();
            return [
                'id' => $paket_id,
                'kode_paket' => $kodePaket
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update paket
     */
    public function update($idOrKode, $data) {
        try {
            $this->db->beginTransaction();

            $pagu = (float)($data['pagu'] ?? 0);
            $paguNego = (!empty($data['pagu_nego']) && (float)$data['pagu_nego'] > 0) ? (float)$data['pagu_nego'] : null;
            $acuan = $paguNego !== null ? $paguNego : $pagu;

            $persenPasal = isset($data['persen_pasal']) && $data['persen_pasal'] !== '' ? (float)$data['persen_pasal'] : 13.0;
            $persenAdmin = isset($data['persen_admin']) && $data['persen_admin'] !== '' ? (float)$data['persen_admin'] : 2.0;

            $dpp = calculateDPP($acuan);
            $pasal = calculatePasal($dpp, $persenPasal);
            $admin = calculateAdmin($dpp, $persenAdmin);
            $totalPasal = $pasal + $admin;
            
            $setorAwal = (isset($data['setor_awal']) && $data['setor_awal'] !== '' && $data['setor_awal'] !== null) ? (float)$data['setor_awal'] : null;
            $selisih = $setorAwal !== null ? round($setorAwal - $totalPasal) : null;
            $rab = !empty($data['rab']) ? (float)$data['rab'] : 0;
            
            $perusahaanName = null;
            if(!empty($data['perusahaan_id'])) {
                 $stmtP = $this->db->prepare("SELECT nama FROM perusahaan WHERE id = ?");
                 $stmtP->execute([$data['perusahaan_id']]);
                 $p = $stmtP->fetch();
                 if ($p) $perusahaanName = $p['nama'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE paket_kegiatan 
                SET nama_paket = ?, nomor_sp = ?, kab_kota = ?, pagu = ?, pagu_nego = ?, dpp = ?, pasal_13 = ?, admin_2 = ?, total_pasal = ?,
                    persen_pasal = ?, persen_admin = ?,
                    setor_awal = ?, selisih = ?, rab = ?, status = ?, progres_barang = ?,
                    perusahaan_id = ?, group_id = ?, skema_id = ?, perusahaan = ?, penanggung_jawab = ?
                WHERE kode_paket = ? OR id = ?
            ");
            
            $stmt->execute([
                $data['nama_paket'],
                !empty($data['nomor_sp']) ? trim($data['nomor_sp']) : null,
                !empty($data['kab_kota']) ? trim($data['kab_kota']) : null,
                $pagu,
                $paguNego,
                $dpp,
                $pasal,
                $admin,
                $totalPasal,
                $persenPasal,
                $persenAdmin,
                $setorAwal,
                $selisih,
                $rab,
                $data['status'] ?? 'Proses',
                $data['progres_barang'] ?? 'Belum',
                $data['perusahaan_id'] ?? null,
                !empty($data['group_id']) ? (int)$data['group_id'] : null,
                !empty($data['skema_id']) ? (int)$data['skema_id'] : null,
                $perusahaanName,
                $data['penanggung_jawab'] ?? null,
                $idOrKode,
                is_numeric($idOrKode) ? (int)$idOrKode : 0
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete paket secara berantai (Cascade Delete):
     * 1. Hapus semua alokasi distribusi paket
     * 2. Hapus semua item barang / spesifikasi paket
     * 3. Hapus berkas fisik dokumen di server lalu hapus data paket_dokumen
     * 4. Hapus paket kegiatan
     */
    public function delete($idOrKode) {
        $paket = $this->getById($idOrKode);
        if (!$paket) return false;
        $paketId = (int)$paket['id'];

        try {
            $this->db->beginTransaction();

            // 1. Hapus seluruh alokasi distribusi
            $stmtDist = $this->db->prepare("DELETE FROM paket_distribusi WHERE paket_id = ?");
            $stmtDist->execute([$paketId]);

            // 2. Hapus seluruh item barang spesifikasi
            $stmtItem = $this->db->prepare("DELETE FROM paket_items WHERE paket_id = ?");
            $stmtItem->execute([$paketId]);

            // 3. Ambil berkas fisik dokumen untuk dihapus dari disk
            $stmtDocs = $this->db->prepare("SELECT file_path FROM paket_dokumen WHERE paket_id = ?");
            $stmtDocs->execute([$paketId]);
            $docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

            foreach ($docs as $doc) {
                if (!empty($doc['file_path'])) {
                    $physicalFile = PUBLIC_PATH . '/' . ltrim($doc['file_path'], '/');
                    if (file_exists($physicalFile) && is_file($physicalFile)) {
                        @unlink($physicalFile);
                    }
                }
            }

            // Hapus data dokumen di database
            $stmtDelDocs = $this->db->prepare("DELETE FROM paket_dokumen WHERE paket_id = ?");
            $stmtDelDocs->execute([$paketId]);

            // 4. Hapus paket kegiatan
            $stmt = $this->db->prepare("DELETE FROM paket_kegiatan WHERE id = ?");
            $res = $stmt->execute([$paketId]);

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
     * Get summary metrics for a paket kegiatan
     */
    public function getSummary($paket_id) {
        $paket = $this->getById($paket_id);
        if (!$paket) {
            return [
                'pagu' => 0,
                'pagu_nego' => 0,
                'dpp' => 0,
                'total_pasal' => 0,
                'rab' => 0,
                'total_modal' => 0,
                'total_masuk' => 0,
                'total_keluar' => 0,
                'saldo' => 0,
                'laba_rugi' => 0
            ];
        }
        return [
            'pagu' => (float)($paket['pagu'] ?? 0),
            'pagu_nego' => (float)($paket['pagu_nego'] ?? 0),
            'dpp' => (float)($paket['dpp'] ?? 0),
            'total_pasal' => (float)($paket['total_pasal'] ?? 0),
            'rab' => (float)($paket['rab'] ?? 0),
            'total_modal' => 0,
            'total_masuk' => 0,
            'total_keluar' => 0,
            'saldo' => 0,
            'laba_rugi' => 0
        ];
    }
}
