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
        // Updated to JOIN perusahaan, paket_group, paket_items summary, and progress metrics
        $sql = "SELECT pk.*, 
                       COALESCE(pi_sum.total_item_harga, 0) as total_item_harga,
                       COALESCE(pi_sum.total_item_qty, 0) as total_item_qty,
                       COALESCE(pi_sum.total_item_count, 0) as total_item_count,
                       COALESCE(pd_sum.total_belanja, 0) as total_belanja,
                       COALESCE(p_prog.total_dist, 0) as total_dist,
                       COALESCE(p_prog.pesan_done, 0) as pesan_done,
                       COALESCE(p_prog.dist_done, 0) as dist_done,
                       COALESCE(p_prog.dok_done, 0) as dok_done,
                       COALESCE(p_prog.total_bast, 0) as total_bast,
                       COALESCE(p_prog.bast_done, 0) as bast_done,
                       p.nama as perusahaan_nama, p.email as perusahaan_email,
                       pg.nama_group, pg.kode_group 
                FROM paket_kegiatan pk
                LEFT JOIN (
                    SELECT paket_id, 
                           COUNT(*) as total_item_count,
                           SUM(jumlah) as total_item_qty,
                           SUM(total_harga) as total_item_harga
                    FROM paket_items
                    GROUP BY paket_id
                ) pi_sum ON pk.id = pi_sum.paket_id
                LEFT JOIN (
                    SELECT paket_id,
                           COALESCE(SUM((CASE WHEN harga_satuan > 0 THEN harga_satuan * jumlah ELSE 0 END) + COALESCE(ongkos_kirim, 0)), 0) as total_belanja
                    FROM paket_distribusi
                    GROUP BY paket_id
                ) pd_sum ON pk.id = pd_sum.paket_id
                LEFT JOIN (
                    SELECT 
                        pd.paket_id,
                        COUNT(DISTINCT pd.id) as total_dist,
                        COUNT(DISTINCT CASE WHEN pd.status_barang = 'Sudah Dipesan' THEN pd.id END) as pesan_done,
                        COUNT(DISTINCT CASE WHEN pd.status_distribusi IN ('Tiba di Tujuan', 'Tiba di Gudang') THEN pd.id END) as dist_done,
                        COUNT(DISTINCT CASE WHEN EXISTS (
                            SELECT 1 FROM paket_dokumentasi pdk
                            WHERE pdk.paket_id = pd.paket_id
                              AND (
                                  pdk.distribusi_id = pd.id
                                  OR (pdk.distribusi_id IS NULL AND pdk.item_id = pd.item_id)
                                  OR (pdk.distribusi_id IS NULL AND pdk.item_id IS NULL)
                              )
                        ) THEN pd.id END) as dok_done,
                        COUNT(DISTINCT pd.nama_tujuan) as total_bast,
                        COUNT(DISTINCT CASE WHEN EXISTS (
                            SELECT 1 FROM paket_dokumen pdoc
                            WHERE pdoc.paket_id = pd.paket_id
                            AND (pdoc.nama_dokumen LIKE CONCAT('%', pd.nama_tujuan, '%') OR pdoc.nama_file_asli LIKE CONCAT('%', pd.nama_tujuan, '%'))
                        ) THEN pd.nama_tujuan END) as bast_done
                    FROM paket_distribusi pd
                    GROUP BY pd.paket_id
                ) p_prog ON pk.id = p_prog.paket_id
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

        // Filter by tag
        if (!empty($filters['tag'])) {
            $sql .= " AND (pk.tags LIKE ? OR pk.tags LIKE ? OR pk.tags LIKE ? OR pk.tags = ?)";
            $t = trim($filters['tag']);
            $params[] = $t . ',%';
            $params[] = '%, ' . $t . ',%';
            $params[] = '%,' . $t;
            $params[] = $t;
        }

        // Filter by tahun_anggaran
        if (!empty($filters['tahun'])) {
            $sql .= " AND pk.tahun_anggaran = ?";
            $params[] = (int)$filters['tahun'];
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
     * Alias untuk getById
     */
    public function getByIdOrKode($idOrKode) {
        return $this->getById($idOrKode);
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

            $tipePotongan = in_array($data['tipe_potongan'] ?? '', ['kontrak', 'dpp']) ? $data['tipe_potongan'] : 'dpp';
            $dpp = calculateDPP($acuan);
            $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
            $pasal = calculatePasal($basis, $persenPasal);
            $admin = calculateAdmin($basis, $persenAdmin);
            $totalPasal = $pasal + $admin;
            
            $setorAwal = (isset($data['setor_awal']) && $data['setor_awal'] !== '' && $data['setor_awal'] !== null) ? (float)$data['setor_awal'] : null;
            $selisih = $setorAwal !== null ? round($setorAwal - $totalPasal) : null;
            $rab = !empty($data['rab']) ? (float)$data['rab'] : 0;
            
            // Inisiasi 5 digit kode huruf & angka acak
            $kodePaket = !empty($data['kode_paket']) ? strtoupper($data['kode_paket']) : $this->generateUniqueKode();

            // Insert paket
            $stmt = $this->db->prepare("
                INSERT INTO paket_kegiatan (
                    kode_paket, nama_paket, nomor_sp, kab_kota, pagu, pagu_nego, dpp, pasal_13, admin_2, total_pasal, tipe_potongan,
                    persen_pasal, persen_admin,
                    setor_awal, selisih, rab, status, progres_barang,
                    perusahaan_id, group_id, tahun_anggaran, tags, skema_id, perusahaan, penanggung_jawab
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                $tipePotongan,
                $persenPasal,
                $persenAdmin,
                $setorAwal,
                $selisih,
                $rab,
                $data['status'] ?? 'Proses',
                $data['progres_barang'] ?? 'Belum',
                $data['perusahaan_id'] ?? null,
                !empty($data['group_id']) ? (int)$data['group_id'] : null,
                !empty($data['tahun_anggaran']) ? (int)$data['tahun_anggaran'] : (int)date('Y'),
                !empty($data['tags']) ? trim($data['tags']) : null,
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

            $tipePotongan = in_array($data['tipe_potongan'] ?? '', ['kontrak', 'dpp']) ? $data['tipe_potongan'] : 'dpp';
            $dpp = calculateDPP($acuan);
            $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
            $pasal = calculatePasal($basis, $persenPasal);
            $admin = calculateAdmin($basis, $persenAdmin);
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
                SET nama_paket = ?, nomor_sp = ?, kab_kota = ?, pagu = ?, pagu_nego = ?, dpp = ?, pasal_13 = ?, admin_2 = ?, total_pasal = ?, tipe_potongan = ?,
                    persen_pasal = ?, persen_admin = ?,
                    setor_awal = ?, selisih = ?, rab = ?, status = ?, progres_barang = ?,
                    perusahaan_id = ?, group_id = ?, tahun_anggaran = ?, tags = ?, skema_id = ?, perusahaan = ?, penanggung_jawab = ?
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
                $tipePotongan,
                $persenPasal,
                $persenAdmin,
                $setorAwal,
                $selisih,
                $rab,
                $data['status'] ?? 'Proses',
                $data['progres_barang'] ?? 'Belum',
                $data['perusahaan_id'] ?? null,
                !empty($data['group_id']) ? (int)$data['group_id'] : null,
                !empty($data['tahun_anggaran']) ? (int)$data['tahun_anggaran'] : (int)date('Y'),
                !empty($data['tags']) ? trim($data['tags']) : null,
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
     * Update khusus skema potongan paket kegiatan (Popup / Modal Mandiri)
     */
    public function updatePotongan($idOrKode, $data) {
        $existing = $this->getById($idOrKode);
        if (!$existing) throw new Exception("Paket tidak ditemukan");

        $pagu = (float)($existing['pagu'] ?? 0);
        $paguNego = (!empty($existing['pagu_nego']) && (float)$existing['pagu_nego'] > 0) ? (float)$existing['pagu_nego'] : null;
        $acuan = $paguNego !== null ? $paguNego : $pagu;

        $persenPasal = isset($data['persen_pasal']) && $data['persen_pasal'] !== '' ? (float)$data['persen_pasal'] : 13.0;
        $persenAdmin = isset($data['persen_admin']) && $data['persen_admin'] !== '' ? (float)$data['persen_admin'] : 2.0;
        $tipePotongan = in_array($data['tipe_potongan'] ?? '', ['kontrak', 'dpp']) ? $data['tipe_potongan'] : 'dpp';

        $dpp = calculateDPP($acuan);
        $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
        $pasal = calculatePasal($basis, $persenPasal);
        $admin = calculateAdmin($basis, $persenAdmin);
        $totalPasal = $pasal + $admin;

        $stmt = $this->db->prepare("
            UPDATE paket_kegiatan SET
                skema_id = ?,
                tipe_potongan = ?,
                persen_pasal = ?,
                persen_admin = ?,
                dpp = ?,
                pasal_13 = ?,
                admin_2 = ?,
                total_pasal = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            !empty($data['skema_id']) ? (int)$data['skema_id'] : null,
            $tipePotongan,
            $persenPasal,
            $persenAdmin,
            $dpp,
            $pasal,
            $admin,
            $totalPasal,
            (int)$existing['id']
        ]);
    }

    /**
     * Update status pencairan paket kegiatan (Belum, Proses, Selesai)
     */
    public function updateStatus($idOrKode, $status) {
        $existing = $this->getById($idOrKode);
        if (!$existing) {
            throw new Exception("Paket kegiatan tidak ditemukan.");
        }
        $validStatuses = ['Belum', 'Proses', 'Selesai'];
        $norm = ucfirst(strtolower(trim($status)));
        if (!in_array($norm, $validStatuses)) {
            $norm = 'Proses';
        }
        $stmt = $this->db->prepare("UPDATE paket_kegiatan SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$norm, (int)$existing['id']]);
    }

    /**
     * Sinkronisasi pagu dan kalkulasi potongan paket dari total paket_items (Tab 2 Komponen Item)
     */
    public function syncPaguFromItems($paketId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_harga), 0) as grand_total, COUNT(*) as total_item 
            FROM paket_items 
            WHERE paket_id = ?
        ");
        $stmt->execute([(int)$paketId]);
        $itemSum = $stmt->fetch();
        
        if ($itemSum && (int)$itemSum['total_item'] > 0) {
            $grandTotal = (float)$itemSum['grand_total'];
            $existing = $this->getById($paketId);
            if (!$existing) return false;

            $hasNego = (!empty($existing['pagu_nego']) && (float)$existing['pagu_nego'] > 0);
            $acuan = $hasNego ? (float)$existing['pagu_nego'] : $grandTotal;
            $tipePotongan = $existing['tipe_potongan'] ?? 'dpp';
            $persenPasal = isset($existing['persen_pasal']) && $existing['persen_pasal'] !== '' ? (float)$existing['persen_pasal'] : 13.0;
            $persenAdmin = isset($existing['persen_admin']) && $existing['persen_admin'] !== '' ? (float)$existing['persen_admin'] : 2.0;

            $dpp = calculateDPP($acuan);
            $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
            $pasal = calculatePasal($basis, $persenPasal);
            $admin = calculateAdmin($basis, $persenAdmin);
            $totalPasal = $pasal + $admin;

            $setorAwal = (isset($existing['setor_awal']) && $existing['setor_awal'] !== '' && $existing['setor_awal'] !== null) ? (float)$existing['setor_awal'] : null;
            $selisih = $setorAwal !== null ? round($setorAwal - $totalPasal) : null;

            $stmtUpdate = $this->db->prepare("
                UPDATE paket_kegiatan SET
                    pagu = ?,
                    dpp = ?,
                    pasal_13 = ?,
                    admin_2 = ?,
                    total_pasal = ?,
                    selisih = ?
                WHERE id = ?
            ");
            return $stmtUpdate->execute([
                $grandTotal,
                $dpp,
                $pasal,
                $admin,
                $totalPasal,
                $selisih,
                (int)$existing['id']
            ]);
        }
        return false;
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

            // 1. Hapus foto dokumentasi & file fisiknya (full + thumbnail)
            require_once __DIR__ . '/../helpers/Uploader.php';
            $stmtFotos = $this->db->prepare("SELECT file_path, thumb_path FROM paket_dokumentasi WHERE paket_id = ?");
            $stmtFotos->execute([$paketId]);
            $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fotos as $foto) {
                Uploader::deleteDocumentationImage($foto['file_path'] ?? null, $foto['thumb_path'] ?? null);
            }
            $this->db->prepare("DELETE FROM paket_dokumentasi WHERE paket_id = ?")->execute([$paketId]);

            // 2. Hapus seluruh alokasi distribusi
            $stmtDist = $this->db->prepare("DELETE FROM paket_distribusi WHERE paket_id = ?");
            $stmtDist->execute([$paketId]);

            // 3. Hapus seluruh item barang spesifikasi
            $stmtItem = $this->db->prepare("DELETE FROM paket_items WHERE paket_id = ?");
            $stmtItem->execute([$paketId]);

            // 4. Ambil berkas fisik dokumen untuk dihapus dari disk
            $stmtDocs = $this->db->prepare("SELECT file_path FROM paket_dokumen WHERE paket_id = ?");
            $stmtDocs->execute([$paketId]);
            $docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

            foreach ($docs as $doc) {
                if (!empty($doc['file_path'])) {
                    Uploader::deleteFile($doc['file_path']);
                }
            }

            // Hapus data dokumen di database
            $stmtDelDocs = $this->db->prepare("DELETE FROM paket_dokumen WHERE paket_id = ?");
            $stmtDelDocs->execute([$paketId]);

            // 5. Hapus paket kegiatan
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

    /**
     * Dapatkan semua tag unik dari paket kegiatan milik perusahaan tertentu beserta jumlahnya
     */
    public function getAllTags($perusahaanId) {
        $stmt = $this->db->prepare("SELECT tags FROM paket_kegiatan WHERE perusahaan_id = ? AND tags IS NOT NULL AND tags != ''");
        $stmt->execute([(int)$perusahaanId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tagCounts = [];
        foreach ($rows as $r) {
            $parts = explode(',', $r['tags']);
            foreach ($parts as $p) {
                $tag = trim($p);
                if ($tag !== '') {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }
        
        ksort($tagCounts);
        $result = [];
        foreach ($tagCounts as $tag => $count) {
            $result[] = ['tag' => $tag, 'count' => $count];
        }
        return $result;
    }
}
