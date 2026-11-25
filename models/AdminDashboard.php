<?php
/**
 * ProLedger - AdminDashboard Model
 * Agregasi statistik global, direktori perusahaan/paket, pengawasan kolaborasi, dan storage
 */

class AdminDashboard {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Statistik ringkasan seluruh sistem untuk dashboard admin
     */
    public function getSystemStats() {
        $stats = [
            'users_total'      => 0,
            'users_active'     => 0,
            'users_pending'    => 0,
            'users_inactive'   => 0,
            'perusahaan_total' => 0,
            'paket_total'      => 0,
            'paket_belum'      => 0,
            'paket_proses'     => 0,
            'paket_selesai'    => 0,
            'total_kontrak'    => 0,
            'total_setor_pasal'=> 0,
            'group_total'      => 0,
            'delegasi_total'   => 0,
            'kolab_total'      => 0,
            'kolab_public'     => 0,
            'monthly_labels'   => [],
            'monthly_paket'    => [],
            'monthly_kontrak'  => [],
        ];

        $uRow = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_cnt,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_cnt,
                SUM(CASE WHEN status IN ('inactive', 'rejected') THEN 1 ELSE 0 END) AS inactive_cnt
            FROM users
        ")->fetch();
        if ($uRow) {
            $stats['users_total']    = (int)$uRow['total'];
            $stats['users_active']   = (int)$uRow['active_cnt'];
            $stats['users_pending']  = (int)$uRow['pending_cnt'];
            $stats['users_inactive'] = (int)$uRow['inactive_cnt'];
        }

        $stats['perusahaan_total'] = (int)$this->db->query("SELECT COUNT(*) FROM perusahaan")->fetchColumn();

        $pRow = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN LOWER(COALESCE(status, 'belum')) IN ('belum', 'pending', 'draft', '') THEN 1 ELSE 0 END) AS belum_cnt,
                SUM(CASE WHEN LOWER(COALESCE(status, '')) IN ('proses', 'on_progress', 'berjalan') THEN 1 ELSE 0 END) AS proses_cnt,
                SUM(CASE WHEN LOWER(COALESCE(status, '')) IN ('selesai', 'cair', 'lunas') THEN 1 ELSE 0 END) AS selesai_cnt,
                COALESCE(SUM(CASE WHEN COALESCE(pagu_nego, 0) > 0 THEN pagu_nego ELSE COALESCE(pagu, 0) END), 0) AS total_kontrak
            FROM paket_kegiatan
        ")->fetch();
        if ($pRow) {
            $stats['paket_total']   = (int)$pRow['total'];
            $stats['paket_belum']   = (int)$pRow['belum_cnt'];
            $stats['paket_proses']  = (int)$pRow['proses_cnt'];
            $stats['paket_selesai'] = (int)$pRow['selesai_cnt'];
            $stats['total_kontrak'] = (float)$pRow['total_kontrak'];
        }

        try {
            $stats['total_setor_pasal'] = (float)$this->db->query("SELECT COALESCE(SUM(nominal), 0) FROM paket_setoran_pasal")->fetchColumn();
        } catch (Throwable $e) {}

        try {
            $stats['group_total'] = (int)$this->db->query("SELECT COUNT(*) FROM paket_group")->fetchColumn();
        } catch (Throwable $e) {}

        try {
            $stats['delegasi_total'] = (int)$this->db->query("SELECT COUNT(*) FROM group_delegasi")->fetchColumn();
        } catch (Throwable $e) {}

        try {
            $kRow = $this->db->query("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN share_enabled = 1 THEN 1 ELSE 0 END) AS pub_cnt
                FROM kolaborasi
            ")->fetch();
            if ($kRow) {
                $stats['kolab_total']  = (int)$kRow['total'];
                $stats['kolab_public'] = (int)$kRow['pub_cnt'];
            }
        } catch (Throwable $e) {}

        // Tren 6 bulan terakhir
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("-{$i} months"));
            $months[$ym] = ['label' => date('M Y', strtotime($ym . '-01')), 'paket' => 0, 'kontrak' => 0];
        }
        $mRows = $this->db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
                   COUNT(*) AS cnt,
                   COALESCE(SUM(CASE WHEN COALESCE(pagu_nego, 0) > 0 THEN pagu_nego ELSE COALESCE(pagu, 0) END), 0) AS nilai
            FROM paket_kegiatan
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY ym
            ORDER BY ym ASC
        ")->fetchAll();
        foreach ($mRows as $mr) {
            if (isset($months[$mr['ym']])) {
                $months[$mr['ym']]['paket']   = (int)$mr['cnt'];
                $months[$mr['ym']]['kontrak'] = round((float)$mr['nilai']);
            }
        }
        foreach ($months as $m) {
            $stats['monthly_labels'][]  = $m['label'];
            $stats['monthly_paket'][]   = $m['paket'];
            $stats['monthly_kontrak'][] = $m['kontrak'];
        }

        return $stats;
    }

    /**
     * Daftar user pending terbaru
     */
    public function getPendingUsers($limit = 6) {
        $stmt = $this->db->prepare("SELECT id, name, email, phone, created_at FROM users WHERE status = 'pending' ORDER BY created_at DESC LIMIT " . (int)$limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Direktori seluruh perusahaan beserta pemilik & ringkasan paket
     */
    public function getPerusahaanList($search = '') {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE p.nama LIKE ? OR p.nama_direktur LIKE ? OR u.name LIKE ? OR u.email LIKE ?";
            $t = '%' . $search . '%';
            $params = [$t, $t, $t, $t];
        }

        $sql = "
            SELECT p.*,
                   u.name AS owner_name,
                   u.email AS owner_email,
                   u.status AS owner_status,
                   COUNT(pk.id) AS total_paket,
                   COALESCE(SUM(CASE WHEN COALESCE(pk.pagu_nego, 0) > 0 THEN pk.pagu_nego ELSE COALESCE(pk.pagu, 0) END), 0) AS total_kontrak
            FROM perusahaan p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN paket_kegiatan pk ON pk.perusahaan_id = p.id
            {$where}
            GROUP BY p.id
            ORDER BY total_paket DESC, p.nama ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Daftar user aktif untuk assign pemilik perusahaan
     */
    public function getActiveUsersForAssign() {
        return $this->db->query("SELECT id, name, email FROM users WHERE role != 'admin' AND status = 'active' ORDER BY name ASC")->fetchAll();
    }

    /**
     * Hubungkan perusahaan ke pemilik akun tertentu
     */
    public function assignPerusahaanOwner($perusahaanId, $userId) {
        $stmt = $this->db->prepare("UPDATE perusahaan SET user_id = ? WHERE id = ?");
        return $stmt->execute([(int)$userId, (int)$perusahaanId]);
    }

    /**
     * Hapus perusahaan kosong (0 paket) tanpa pemilik
     */
    public function deleteOrphanPerusahaan($perusahaanId) {
        $pid = (int)$perusahaanId;
        $cnt = (int)$this->db->query("SELECT COUNT(*) FROM paket_kegiatan WHERE perusahaan_id = {$pid}")->fetchColumn();
        if ($cnt > 0) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM perusahaan WHERE id = ?");
        return $stmt->execute([$pid]);
    }

    /**
     * Pencarian paket global lintas perusahaan
     */
    public function getGlobalPakets($search = '', $status = '', $perusahaanId = 0, $limit = 150) {
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(pk.kode_paket LIKE ? OR pk.nama_paket LIKE ? OR pk.tags LIKE ? OR p.nama LIKE ? OR u.name LIKE ?)";
            $t = '%' . $search . '%';
            array_push($params, $t, $t, $t, $t, $t);
        }
        if ($perusahaanId > 0) {
            $where[] = "pk.perusahaan_id = ?";
            $params[] = (int)$perusahaanId;
        }
        if ($status === 'belum') {
            $where[] = "LOWER(COALESCE(pk.status, 'belum')) IN ('belum', 'pending', 'draft', '')";
        } elseif ($status === 'proses') {
            $where[] = "LOWER(COALESCE(pk.status, '')) IN ('proses', 'on_progress', 'berjalan')";
        } elseif ($status === 'selesai') {
            $where[] = "LOWER(COALESCE(pk.status, '')) IN ('selesai', 'cair', 'lunas')";
        }

        $whereSql = !empty($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "
            SELECT pk.*,
                   p.nama AS perusahaan_nama,
                   p.user_id AS owner_user_id,
                   u.name AS owner_name,
                   pg.nama_group,
                   (CASE WHEN COALESCE(pk.pagu_nego, 0) > 0 THEN pk.pagu_nego ELSE COALESCE(pk.pagu, 0) END) AS nilai_kontrak
            FROM paket_kegiatan pk
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN paket_group pg ON pk.group_id = pg.id
            {$whereSql}
            ORDER BY pk.id DESC
            LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Daftar seluruh ruang kolaborasi
     */
    public function getKolaborasiList($search = '') {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE k.nama LIKE ? OR k.instansi LIKE ? OR u.name LIKE ?";
            $t = '%' . $search . '%';
            $params = [$t, $t, $t];
        }

        $sql = "
            SELECT k.*,
                   u.name AS owner_name,
                   u.email AS owner_email,
                   (SELECT COUNT(*) FROM kolaborasi_anggota km WHERE km.kolaborasi_id = k.id AND km.status = 'joined') AS member_count,
                   (SELECT COUNT(*) FROM kolaborasi_groups kg WHERE kg.kolaborasi_id = k.id) AS group_count
            FROM kolaborasi k
            LEFT JOIN users u ON k.owner_id = u.id
            {$where}
            ORDER BY k.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Toggle paksa status link monitoring publik
     */
    public function toggleKolabPublicLink($kolabId, $enable) {
        $stmt = $this->db->prepare("UPDATE kolaborasi SET share_enabled = ? WHERE id = ?");
        return $stmt->execute([$enable ? 1 : 0, (int)$kolabId]);
    }

    /**
     * Statistik pemakaian storage folder public/uploads
     */
    public function getStorageStats() {
        $baseDir = PUBLIC_PATH . '/uploads';
        $folders = [];
        $totalBytes = 0;
        $totalFiles = 0;

        if (!is_dir($baseDir)) {
            return ['total_bytes' => 0, 'total_files' => 0, 'folders' => []];
        }

        $entries = scandir($baseDir);
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || $entry === '.htaccess') continue;
            $full = $baseDir . '/' . $entry;
            if (is_dir($full)) {
                $bytes = 0;
                $files = 0;
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full, FilesystemIterator::SKIP_DOTS));
                foreach ($it as $file) {
                    if ($file->isFile()) {
                        $bytes += $file->getSize();
                        $files++;
                    }
                }
                $folders[$entry] = ['name' => $entry, 'bytes' => $bytes, 'files' => $files];
                $totalBytes += $bytes;
                $totalFiles += $files;
            }
        }

        return ['total_bytes' => $totalBytes, 'total_files' => $totalFiles, 'folders' => $folders];
    }

    /**
     * Pindai file yatim (orphan files) di public/uploads yang tidak lagi dirujuk di database
     */
    public function findOrphanFiles() {
        $referenced = [];

        // 1. Perusahaan
        $rows = $this->db->query("SELECT logo, avatar, kopsurat, ttd, stempel_ttd FROM perusahaan")->fetchAll();
        foreach ($rows as $r) {
            foreach (['logo', 'avatar', 'kopsurat', 'ttd', 'stempel_ttd'] as $col) {
                if (!empty($r[$col])) {
                    $referenced[basename($r[$col])] = true;
                }
            }
        }

        // 2. Dokumentasi paket
        try {
            $rows = $this->db->query("SELECT file_path, thumb_path FROM paket_dokumentasi")->fetchAll();
            foreach ($rows as $r) {
                if (!empty($r['file_path'])) $referenced[basename($r['file_path'])] = true;
                if (!empty($r['thumb_path'])) $referenced[basename($r['thumb_path'])] = true;
            }
        } catch (Throwable $e) {}

        // 3. Dokumen paket
        try {
            $rows = $this->db->query("SELECT file_path FROM paket_dokumen")->fetchAll();
            foreach ($rows as $r) {
                if (!empty($r['file_path'])) $referenced[basename($r['file_path'])] = true;
            }
        } catch (Throwable $e) {}

        $baseDirRaw = realpath(PUBLIC_PATH . '/uploads');
        $baseDir = $baseDirRaw ? str_replace('\\', '/', $baseDirRaw) : '';
        $orphans = [];
        if (!$baseDir || !is_dir($baseDir)) {
            return $orphans;
        }

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (!$file->isFile()) continue;
            $name = $file->getFilename();
            if ($name === '.htaccess' || $name === 'index.html' || $name === '.gitkeep') continue;
            if (!isset($referenced[$name])) {
                $fullNorm = str_replace('\\', '/', $file->getPathname());
                $relPath = ltrim(str_replace($baseDir . '/', '', $fullNorm), '/');
                $orphans[] = [
                    'rel_path' => $relPath,
                    'size'     => $file->getSize(),
                    'mtime'    => $file->getMTime(),
                ];
            }
        }

        return $orphans;
    }

    /**
     * Hapus file orphan secara aman (hanya di dalam PUBLIC_PATH/uploads)
     */
    public function deleteOrphanFiles(array $relPaths) {
        $baseDir = realpath(PUBLIC_PATH . '/uploads');
        if (!$baseDir) return 0;

        $deleted = 0;
        foreach ($relPaths as $rel) {
            $rel = ltrim(str_replace(['..', '\\'], ['', '/'], $rel), '/');
            $target = realpath($baseDir . '/' . $rel);
            if ($target && strpos($target, $baseDir) === 0 && is_file($target) && basename($target) !== '.htaccess') {
                if (@unlink($target)) {
                    $deleted++;
                }
            }
        }
        return $deleted;
    }
}
