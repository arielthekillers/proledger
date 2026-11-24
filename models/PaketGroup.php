<?php
/**
 * ProLedger - PaketGroup Model
 * Model untuk manajemen pengelompokan paket kegiatan (Group Proyek / Program)
 * dengan kalkulasi roll-up finansial & progres dinamis.
 */

class PaketGroup {
    private $db;
    private static $schemaChecked = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureUserIdColumn();
    }

    /**
     * Pastikan kolom user_id tersedia pada paket_group & backfill data lama secara aman
     */
    private function ensureUserIdColumn() {
        if (self::$schemaChecked) return;
        self::$schemaChecked = true;
        try {
            $col = $this->db->query("SHOW COLUMNS FROM paket_group LIKE 'user_id'")->fetch();
            if (!$col) {
                $this->db->exec("ALTER TABLE paket_group ADD COLUMN user_id INT(11) NULL AFTER perusahaan_id, ADD INDEX idx_pg_user (user_id)");
                $this->db->exec("UPDATE paket_group pg INNER JOIN perusahaan p ON pg.perusahaan_id = p.id SET pg.user_id = p.user_id WHERE pg.user_id IS NULL");
                $this->db->exec("UPDATE paket_group pg INNER JOIN kolaborasi_groups kg ON kg.group_id = pg.id SET pg.user_id = kg.user_id WHERE pg.user_id IS NULL");
                $this->db->exec("UPDATE paket_group pg INNER JOIN paket_kegiatan pk ON pk.group_id = pg.id INNER JOIN perusahaan p ON pk.perusahaan_id = p.id SET pg.user_id = p.user_id WHERE pg.user_id IS NULL");
            }
        } catch (Throwable $e) {
            // Abaikan jika gagal migrasi otomatis (misal permission terbatas)
        }
    }

    /**
     * Cek apakah user berhak mengakses / memakai group tertentu
     */
    public function userCanAccess(array $group, int $userId, array $myCompIds, string $role = 'user'): bool {
        if ($role === 'admin') {
            return true;
        }
        if (!empty($group['user_id']) && (int)$group['user_id'] === $userId) {
            return true;
        }
        if (!empty($group['perusahaan_id']) && in_array((int)$group['perusahaan_id'], $myCompIds, true)) {
            return true;
        }
        return false;
    }

    /**
     * Hitung metrik finansial & progres sebuah paket secara konsisten
     */
    public static function calculatePaketMetrics(array $p): array {
        $itemTotal = (float)($p['total_item_harga'] ?? 0);
        $effectivePagu = ($itemTotal > 0) ? $itemTotal : (float)($p['pagu'] ?? 0);
        $hasNego = (!empty($p['pagu_nego']) && (float)$p['pagu_nego'] > 0);
        $acuan = $hasNego ? (float)$p['pagu_nego'] : $effectivePagu;

        $tipePotongan = $p['tipe_potongan'] ?? 'dpp';
        $dpp = calculateDPP($acuan);
        $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;

        $persenPasal = (isset($p['persen_pasal']) && $p['persen_pasal'] !== '') ? (float)$p['persen_pasal'] : 13.0;
        $persenAdmin = (isset($p['persen_admin']) && $p['persen_admin'] !== '') ? (float)$p['persen_admin'] : 2.0;

        $pasalVal = calculatePasal($basis, $persenPasal);
        $adminVal = calculateAdmin($basis, $persenAdmin);
        $totalPasal = $pasalVal + $adminVal;
        $nilaiBersih = $dpp - $totalPasal;
        $hpp = (float)($p['total_belanja'] ?? 0);
        $profit = $nilaiBersih - $hpp;
        $profitPct = ($acuan > 0) ? round(($profit / $acuan) * 100, 1) : 0;

        $totalDist = (int)($p['total_dist'] ?? 0);
        $totalBast = (int)($p['total_bast'] ?? 0);
        $orderPct = $totalDist > 0 ? (int)round(((int)($p['pesan_done'] ?? 0) / $totalDist) * 100) : 0;
        $distPct  = $totalDist > 0 ? (int)round(((int)($p['dist_done'] ?? 0) / $totalDist) * 100) : 0;
        $dokPct   = $totalDist > 0 ? (int)round(((int)($p['dok_done'] ?? 0) / $totalDist) * 100) : 0;
        $bastPct  = $totalBast > 0 ? (int)round(((int)($p['bast_done'] ?? 0) / $totalBast) * 100) : 0;

        $rawStatus = strtolower(trim($p['status'] ?? 'proses'));
        if ($rawStatus === 'cair' || $rawStatus === 'selesai') {
            $normStatus = 'selesai';
        } elseif ($rawStatus === 'belum') {
            $normStatus = 'belum';
        } else {
            $normStatus = 'proses';
        }

        return [
            'effective_pagu' => $effectivePagu,
            'acuan'          => $acuan,
            'dpp'            => $dpp,
            'persen_pasal'   => $persenPasal,
            'persen_admin'   => $persenAdmin,
            'total_persen'   => $persenPasal + $persenAdmin,
            'total_pasal'    => $totalPasal,
            'nilai_bersih'   => $nilaiBersih,
            'hpp'            => $hpp,
            'profit'         => $profit,
            'profit_pct'     => $profitPct,
            'item_count'     => (int)($p['total_item_count'] ?? 0),
            'item_qty'       => (float)($p['total_item_qty'] ?? 0),
            'order_pct'      => $orderPct,
            'dist_pct'       => $distPct,
            'dok_pct'        => $dokPct,
            'bast_pct'       => $bastPct,
            'norm_status'    => $normStatus,
        ];
    }

    /**
     * Hitung statistik roll-up dari daftar paket
     */
    public static function computeStatsFromList(array $paketList): array {
        $stats = [
            'total_paket'    => count($paketList),
            'total_pagu'     => 0,
            'total_acuan'    => 0,
            'total_dpp'      => 0,
            'total_pasal'    => 0,
            'total_bersih'   => 0,
            'total_hpp'      => 0,
            'total_profit'   => 0,
            'total_items'    => 0,
            'total_units'    => 0,
            'paket_selesai'  => 0,
            'paket_proses'   => 0,
            'paket_belum'    => 0,
            'order_pct'      => 0,
            'dist_pct'       => 0,
            'dok_pct'        => 0,
            'bast_pct'       => 0,
            'profit_pct'     => 0,
        ];

        $sumOrder = 0; $sumDist = 0; $sumDok = 0; $sumBast = 0;

        foreach ($paketList as $p) {
            $m = self::calculatePaketMetrics($p);
            $stats['total_pagu']   += (float)($p['pagu'] ?? 0);
            $stats['total_acuan']  += $m['acuan'];
            $stats['total_dpp']    += $m['dpp'];
            $stats['total_pasal']  += $m['total_pasal'];
            $stats['total_bersih'] += $m['nilai_bersih'];
            $stats['total_hpp']    += $m['hpp'];
            $stats['total_profit'] += $m['profit'];
            $stats['total_items']  += $m['item_count'];
            $stats['total_units']  += $m['item_qty'];

            if ($m['norm_status'] === 'selesai') {
                $stats['paket_selesai']++;
            } elseif ($m['norm_status'] === 'belum') {
                $stats['paket_belum']++;
            } else {
                $stats['paket_proses']++;
            }

            $sumOrder += $m['order_pct'];
            $sumDist  += $m['dist_pct'];
            $sumDok   += $m['dok_pct'];
            $sumBast  += $m['bast_pct'];
        }

        if ($stats['total_paket'] > 0) {
            $stats['order_pct'] = (int)round($sumOrder / $stats['total_paket']);
            $stats['dist_pct']  = (int)round($sumDist / $stats['total_paket']);
            $stats['dok_pct']   = (int)round($sumDok / $stats['total_paket']);
            $stats['bast_pct']  = (int)round($sumBast / $stats['total_paket']);
        }
        if ($stats['total_acuan'] > 0) {
            $stats['profit_pct'] = round(($stats['total_profit'] / $stats['total_acuan']) * 100, 1);
        }

        return $stats;
    }

    /**
     * Dapatkan semua group dengan agregasi statistik akurat dari paket di dalamnya
     * @return array
     */
    public function getAll($perusahaanId = null, $userId = null): array {
        $sql = "
            SELECT pg.*, p.nama as perusahaan_nama
            FROM paket_group pg
            LEFT JOIN perusahaan p ON pg.perusahaan_id = p.id
        ";

        $where = [];
        $params = [];
        if ($perusahaanId !== null) {
            if ($userId !== null) {
                $where[] = "((pg.perusahaan_id = ? AND (p.user_id = ? OR pg.user_id = ? OR p.user_id IS NULL)) OR (pg.perusahaan_id IS NULL AND pg.user_id = ?))";
                $params[] = (int)$perusahaanId;
                $params[] = (int)$userId;
                $params[] = (int)$userId;
                $params[] = (int)$userId;
            } else {
                $where[] = "pg.perusahaan_id = ?";
                $params[] = (int)$perusahaanId;
            }
        } elseif ($userId !== null) {
            $where[] = "(pg.user_id = ? OR p.user_id = ?)";
            $params[] = (int)$userId;
            $params[] = (int)$userId;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY pg.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $groups = $stmt->fetchAll();

        if (empty($groups)) {
            return [];
        }

        // Ambil semua paket anggota untuk group-group tersebut sekaligus
        $groupIds = array_column($groups, 'id');
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $paketWhere = "pk.group_id IN ($placeholders)";
        $paketParams = $groupIds;
        if ($userId !== null) {
            $paketWhere .= " AND (p.user_id = ? OR pk.perusahaan_id IS NULL)";
            $paketParams[] = (int)$userId;
        }
        $paketSql = $this->buildPaketSelectSql($paketWhere);
        $pStmt = $this->db->prepare($paketSql);
        $pStmt->execute($paketParams);
        $allPakets = $pStmt->fetchAll();

        $paketsByGroup = [];
        foreach ($allPakets as $p) {
            $paketsByGroup[$p['group_id']][] = $p;
        }

        foreach ($groups as &$g) {
            $list = $paketsByGroup[$g['id']] ?? [];
            $s = self::computeStatsFromList($list);
            $g['total_paket']    = $s['total_paket'];
            $g['total_pagu']     = $s['total_pagu'];
            $g['total_acuan']    = $s['total_acuan'];
            $g['total_pagu_nego']= $s['total_acuan'];
            $g['total_dpp']      = $s['total_dpp'];
            $g['total_pasal']    = $s['total_pasal'];
            $g['total_bersih']   = $s['total_bersih'];
            $g['total_hpp']      = $s['total_hpp'];
            $g['total_profit']   = $s['total_profit'];
            $g['profit_pct']     = $s['profit_pct'];
            $g['total_selesai']  = $s['paket_selesai'];
            $g['total_proses']   = $s['paket_proses'];
            $g['total_belum']    = $s['paket_belum'];
            $g['order_pct']      = $s['order_pct'];
            $g['dist_pct']       = $s['dist_pct'];
            $g['dok_pct']        = $s['dok_pct'];
            $g['bast_pct']       = $s['bast_pct'];
        }
        unset($g);

        return $groups;
    }

    /**
     * Query builder untuk mengambil daftar paket lengkap dengan summary item, belanja & progres
     */
    private function buildPaketSelectSql(string $whereClause): string {
        return "
            SELECT pk.*,
                   COALESCE(pi_sum.total_item_harga, 0) as total_item_harga,
                   COALESCE(pi_sum.total_item_qty, 0) as total_item_qty,
                   COALESCE(pi_sum.total_item_count, 0) as total_item_count,
                   COALESCE(pd_sum.total_belanja, 0) as total_belanja,
                   COALESCE(p_prog.total_dist, 0) as total_dist,
                   COALESCE(p_prog.pesan_done, 0) as pesan_done,
                   COALESCE(p_prog.dist_done, 0) as dist_done,
                   CASE WHEN COALESCE(pk.is_dok_selesai, 0) = 1 THEN COALESCE(p_prog.total_dist, 0) ELSE COALESCE(p_prog.dok_done, 0) END as dok_done,
                   COALESCE(p_prog.total_bast, 0) as total_bast,
                   COALESCE(p_prog.bast_done, 0) as bast_done,
                   p.nama as perusahaan_nama
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
                    COUNT(DISTINCT CASE WHEN pdk.distribusi_id IS NOT NULL THEN pdk.distribusi_id END) as dok_done,
                    COUNT(DISTINCT pd.nama_tujuan) as total_bast,
                    COUNT(DISTINCT CASE WHEN EXISTS (
                        SELECT 1 FROM paket_dokumen pdoc
                        WHERE pdoc.paket_id = pd.paket_id
                        AND (pdoc.nama_dokumen LIKE CONCAT('%', pd.nama_tujuan, '%') OR pdoc.nama_file_asli LIKE CONCAT('%', pd.nama_tujuan, '%'))
                    ) THEN pd.nama_tujuan END) as bast_done
                FROM paket_distribusi pd
                LEFT JOIN paket_dokumentasi pdk ON pd.id = pdk.distribusi_id
                GROUP BY pd.paket_id
            ) p_prog ON pk.id = p_prog.paket_id
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            WHERE $whereClause
            ORDER BY pk.created_at DESC
        ";
    }

    /**
     * Dapatkan detail group berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT pg.*, p.nama as perusahaan_nama 
            FROM paket_group pg
            LEFT JOIN perusahaan p ON pg.perusahaan_id = p.id
            WHERE pg.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Dapatkan daftar paket yang tergabung dalam group ini
     */
    public function getPaketList($groupId) {
        $sql = $this->buildPaketSelectSql("pk.group_id = ?");
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan statistik roll-up keuangan group
     */
    public function getStats($groupId) {
        $paketList = $this->getPaketList($groupId);
        return self::computeStatsFromList($paketList);
    }

    /**
     * Buat group baru
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO paket_group (perusahaan_id, user_id, nama_group, kode_group, deskripsi)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['perusahaan_id'] ?? null,
            $data['user_id'] ?? ($_SESSION['admin_id'] ?? null),
            $data['nama_group'],
            $data['kode_group'] ?? null,
            $data['deskripsi'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update group
     */
    public function update($id, $data) {
        $fields = ["nama_group = ?", "kode_group = ?", "deskripsi = ?"];
        $params = [
            $data['nama_group'],
            $data['kode_group'] ?? null,
            $data['deskripsi'] ?? null
        ];

        if (array_key_exists('perusahaan_id', $data)) {
            $fields[] = "perusahaan_id = ?";
            $params[] = $data['perusahaan_id'];
        }

        $params[] = $id;

        $stmt = $this->db->prepare("UPDATE paket_group SET " . implode(", ", $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    /**
     * Hapus group (lepaskan paket anggota agar group_id menjadi NULL & bersihkan relasi kolaborasi)
     */
    public function delete($id) {
        $stmtDetach = $this->db->prepare("UPDATE paket_kegiatan SET group_id = NULL WHERE group_id = ?");
        $stmtDetach->execute([$id]);

        try {
            $stmtKolab = $this->db->prepare("DELETE FROM kolaborasi_groups WHERE group_id = ?");
            $stmtKolab->execute([$id]);
        } catch (Throwable $e) {
            // Abaikan jika tabel kolaborasi_groups belum tersedia
        }

        $stmt = $this->db->prepare("DELETE FROM paket_group WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
