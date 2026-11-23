<?php
/**
 * ProLedger - Kolaborasi Model
 * Mengelola Ruang Kolaborasi, Anggota/Undangan, Link Group Paket,
 * serta agregasi progres & nilai kontrak (tanpa data privat HPP/Profit/Belanja).
 */

class Kolaborasi {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureSchema();
    }

    /**
     * Pastikan tabel kolaborasi tersedia
     */
    public function ensureSchema(): void {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS kolaborasi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                owner_id INT NOT NULL,
                nama VARCHAR(255) NOT NULL,
                instansi VARCHAR(255) NULL,
                tahun_anggaran INT NOT NULL DEFAULT 2026,
                deskripsi TEXT NULL,
                invite_code VARCHAR(32) NOT NULL UNIQUE,
                share_token VARCHAR(64) NOT NULL UNIQUE,
                share_enabled TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS kolaborasi_anggota (
                id INT AUTO_INCREMENT PRIMARY KEY,
                kolaborasi_id INT NOT NULL,
                user_id INT NOT NULL,
                role ENUM('owner','member') NOT NULL DEFAULT 'member',
                status ENUM('pending','joined','declined') NOT NULL DEFAULT 'pending',
                invited_by INT NULL,
                joined_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_kolab_user (kolaborasi_id, user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS kolaborasi_groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                kolaborasi_id INT NOT NULL,
                group_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_kolab_group (kolaborasi_id, group_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Generate kode undangan unik (misal: KOLAB-A8F2K)
     */
    private function generateInviteCode(): string {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = 'KOLAB-';
            for ($i = 0; $i < 5; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $stmt = $this->db->prepare("SELECT id FROM kolaborasi WHERE invite_code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetch());
        return $code;
    }

    /**
     * Generate token read-only publik
     */
    private function generateShareToken(): string {
        return bin2hex(random_bytes(16));
    }

    /**
     * Buat ruang kolaborasi baru dan daftarkan pembuat sebagai owner
     */
    public function create(array $data): int {
        $inviteCode = $this->generateInviteCode();
        $shareToken = $this->generateShareToken();

        $stmt = $this->db->prepare("
            INSERT INTO kolaborasi (owner_id, nama, instansi, tahun_anggaran, deskripsi, invite_code, share_token, share_enabled)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            (int)$data['owner_id'],
            $data['nama'],
            $data['instansi'] ?: null,
            (int)($data['tahun_anggaran'] ?? date('Y')),
            $data['deskripsi'] ?: null,
            $inviteCode,
            $shareToken
        ]);
        $kolabId = (int)$this->db->lastInsertId();

        $stmtMember = $this->db->prepare("
            INSERT INTO kolaborasi_anggota (kolaborasi_id, user_id, role, status, invited_by, joined_at)
            VALUES (?, ?, 'owner', 'joined', ?, NOW())
        ");
        $stmtMember->execute([$kolabId, (int)$data['owner_id'], (int)$data['owner_id']]);

        return $kolabId;
    }

    /**
     * Update info ruang kolaborasi
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE kolaborasi
            SET nama = ?, instansi = ?, tahun_anggaran = ?, deskripsi = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nama'],
            $data['instansi'] ?: null,
            (int)($data['tahun_anggaran'] ?? date('Y')),
            $data['deskripsi'] ?: null,
            $id
        ]);
    }

    /**
     * Hapus ruang kolaborasi beserta anggota & relasi group
     */
    public function delete(int $id): bool {
        $this->db->prepare("DELETE FROM kolaborasi_groups WHERE kolaborasi_id = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM kolaborasi_anggota WHERE kolaborasi_id = ?")->execute([$id]);
        return $this->db->prepare("DELETE FROM kolaborasi WHERE id = ?")->execute([$id]);
    }

    /**
     * Toggle atau regenerate link monitoring read-only
     */
    public function toggleShare(int $id, int $enabled): bool {
        $stmt = $this->db->prepare("UPDATE kolaborasi SET share_enabled = ? WHERE id = ?");
        return $stmt->execute([$enabled ? 1 : 0, $id]);
    }

    public function regenerateShareToken(int $id): string {
        $token = $this->generateShareToken();
        $stmt = $this->db->prepare("UPDATE kolaborasi SET share_token = ? WHERE id = ?");
        $stmt->execute([$token, $id]);
        return $token;
    }

    /**
     * Ambil detail kolaborasi by ID
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("
            SELECT k.*, a.name as owner_name, a.email as owner_email
            FROM kolaborasi k
            LEFT JOIN admins a ON k.owner_id = a.id
            WHERE k.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ambil detail kolaborasi by Share Token (untuk link publik read-only)
     */
    public function getByShareToken(string $token) {
        $stmt = $this->db->prepare("
            SELECT k.*, a.name as owner_name
            FROM kolaborasi k
            LEFT JOIN admins a ON k.owner_id = a.id
            WHERE k.share_token = ? AND k.share_enabled = 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Ambil detail kolaborasi by Invite Code
     */
    public function getByInviteCode(string $code) {
        $stmt = $this->db->prepare("SELECT * FROM kolaborasi WHERE invite_code = ?");
        $stmt->execute([strtoupper(trim($code))]);
        return $stmt->fetch();
    }

    /**
     * Daftar kolaborasi milik/diikuti user (beserta ringkasan jumlah anggota & paket)
     */
    public function getForUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT k.*, ka.role as my_role, ka.status as my_status,
                   a.name as owner_name,
                   (SELECT COUNT(*) FROM kolaborasi_anggota ka2 WHERE ka2.kolaborasi_id = k.id AND ka2.status = 'joined') as total_anggota,
                   (SELECT COUNT(*) FROM kolaborasi_groups kg WHERE kg.kolaborasi_id = k.id) as total_groups,
                   (SELECT COUNT(*) FROM paket_kegiatan pk
                    INNER JOIN kolaborasi_groups kg2 ON pk.group_id = kg2.group_id
                    WHERE kg2.kolaborasi_id = k.id) as total_paket
            FROM kolaborasi k
            INNER JOIN kolaborasi_anggota ka ON k.id = ka.kolaborasi_id AND ka.user_id = ?
            LEFT JOIN admins a ON k.owner_id = a.id
            WHERE ka.status IN ('joined', 'pending')
            ORDER BY FIELD(ka.status, 'pending', 'joined'), k.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Cek keanggotaan user pada kolaborasi
     */
    public function getMembership(int $kolabId, int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM kolaborasi_anggota WHERE kolaborasi_id = ? AND user_id = ?");
        $stmt->execute([$kolabId, $userId]);
        return $stmt->fetch();
    }

    /**
     * Undang user via email
     */
    public function inviteByEmail(int $kolabId, string $email, int $invitedBy): array {
        $stmtUser = $this->db->prepare("SELECT id, name, email, role, status FROM admins WHERE email = ? LIMIT 1");
        $stmtUser->execute([trim($email)]);
        $user = $stmtUser->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Pengguna dengan email tersebut tidak ditemukan.'];
        }
        if ($user['role'] === 'admin') {
            return ['success' => false, 'message' => 'Akun administrator sistem tidak dapat diundang ke kolaborasi.'];
        }

        $existing = $this->getMembership($kolabId, (int)$user['id']);
        if ($existing) {
            if ($existing['status'] === 'joined') {
                return ['success' => false, 'message' => $user['name'] . ' sudah bergabung di kolaborasi ini.'];
            }
            if ($existing['status'] === 'pending') {
                return ['success' => false, 'message' => 'Undangan untuk ' . $user['name'] . ' sudah dikirim dan menunggu konfirmasi.'];
            }
            // Jika sebelumnya declined, kirim ulang undangan
            $stmtUp = $this->db->prepare("UPDATE kolaborasi_anggota SET status = 'pending', invited_by = ? WHERE id = ?");
            $stmtUp->execute([$invitedBy, $existing['id']]);
            return ['success' => true, 'message' => 'Undangan berhasil dikirim ulang ke ' . $user['name'] . '.'];
        }

        $stmtIns = $this->db->prepare("
            INSERT INTO kolaborasi_anggota (kolaborasi_id, user_id, role, status, invited_by)
            VALUES (?, ?, 'member', 'pending', ?)
        ");
        $stmtIns->execute([$kolabId, (int)$user['id'], $invitedBy]);

        return ['success' => true, 'message' => 'Undangan berhasil dikirim ke ' . $user['name'] . '.'];
    }

    /**
     * Gabung menggunakan kode undangan
     */
    public function joinByCode(string $code, int $userId): array {
        $kolab = $this->getByInviteCode($code);
        if (!$kolab) {
            return ['success' => false, 'message' => 'Kode undangan tidak valid atau tidak ditemukan.'];
        }

        $existing = $this->getMembership((int)$kolab['id'], $userId);
        if ($existing) {
            if ($existing['status'] === 'joined') {
                return ['success' => true, 'kolab_id' => (int)$kolab['id'], 'message' => 'Anda sudah tergabung di kolaborasi ini.'];
            }
            $this->db->prepare("UPDATE kolaborasi_anggota SET status = 'joined', joined_at = NOW() WHERE id = ?")
                     ->execute([$existing['id']]);
            return ['success' => true, 'kolab_id' => (int)$kolab['id'], 'message' => 'Berhasil bergabung ke kolaborasi ' . $kolab['nama'] . '!'];
        }

        $this->db->prepare("
            INSERT INTO kolaborasi_anggota (kolaborasi_id, user_id, role, status, invited_by, joined_at)
            VALUES (?, ?, 'member', 'joined', ?, NOW())
        ")->execute([(int)$kolab['id'], $userId, (int)$kolab['owner_id']]);

        return ['success' => true, 'kolab_id' => (int)$kolab['id'], 'message' => 'Berhasil bergabung ke kolaborasi ' . $kolab['nama'] . '!'];
    }

    /**
     * Respon undangan (accept / decline)
     */
    public function respondInvite(int $kolabId, int $userId, string $decision): bool {
        $status = ($decision === 'accept') ? 'joined' : 'declined';
        $joinedAt = ($decision === 'accept') ? date('Y-m-d H:i:s') : null;
        $stmt = $this->db->prepare("
            UPDATE kolaborasi_anggota
            SET status = ?, joined_at = COALESCE(?, joined_at)
            WHERE kolaborasi_id = ? AND user_id = ?
        ");
        return $stmt->execute([$status, $joinedAt, $kolabId, $userId]);
    }

    /**
     * Keluarkan anggota (oleh owner) atau keluar sendiri
     */
    public function removeMember(int $kolabId, int $targetUserId): bool {
        $this->db->prepare("DELETE FROM kolaborasi_groups WHERE kolaborasi_id = ? AND user_id = ?")
                 ->execute([$kolabId, $targetUserId]);
        return $this->db->prepare("DELETE FROM kolaborasi_anggota WHERE kolaborasi_id = ? AND user_id = ? AND role != 'owner'")
                        ->execute([$kolabId, $targetUserId]);
    }

    /**
     * Daftar anggota pada kolaborasi
     */
    public function getMembers(int $kolabId): array {
        $stmt = $this->db->prepare("
            SELECT ka.*, a.name, a.email,
                   (SELECT COUNT(*) FROM kolaborasi_groups kg WHERE kg.kolaborasi_id = ka.kolaborasi_id AND kg.user_id = ka.user_id) as linked_groups_count
            FROM kolaborasi_anggota ka
            INNER JOIN admins a ON ka.user_id = a.id
            WHERE ka.kolaborasi_id = ?
            ORDER BY FIELD(ka.role, 'owner', 'member'), FIELD(ka.status, 'joined', 'pending', 'declined'), a.name ASC
        ");
        $stmt->execute([$kolabId]);
        return $stmt->fetchAll();
    }

    /**
     * Hubungkan Group Paket milik user ke Kolaborasi
     */
    public function linkGroup(int $kolabId, int $groupId, int $userId): bool {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO kolaborasi_groups (kolaborasi_id, group_id, user_id)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$kolabId, $groupId, $userId]);
    }

    /**
     * Lepas Group Paket dari Kolaborasi
     */
    public function unlinkGroup(int $kolabId, int $groupId, int $userId, bool $isOwner = false): bool {
        if ($isOwner) {
            return $this->db->prepare("DELETE FROM kolaborasi_groups WHERE kolaborasi_id = ? AND group_id = ?")
                            ->execute([$kolabId, $groupId]);
        }
        return $this->db->prepare("DELETE FROM kolaborasi_groups WHERE kolaborasi_id = ? AND group_id = ? AND user_id = ?")
                        ->execute([$kolabId, $groupId, $userId]);
    }

    /**
     * Daftar group yang sudah di-share ke kolaborasi ini
     */
    public function getLinkedGroups(int $kolabId): array {
        $stmt = $this->db->prepare("
            SELECT kg.*, pg.nama_group, pg.kode_group, p.nama as perusahaan_nama, a.name as user_name
            FROM kolaborasi_groups kg
            INNER JOIN paket_group pg ON kg.group_id = pg.id
            LEFT JOIN perusahaan p ON pg.perusahaan_id = p.id
            LEFT JOIN admins a ON kg.user_id = a.id
            WHERE kg.kolaborasi_id = ?
            ORDER BY p.nama ASC, pg.nama_group ASC
        ");
        $stmt->execute([$kolabId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil seluruh paket & rekapitulasi aman untuk Dashboard Kolaborasi
     * STRICT PRIVACY: Tidak pernah mengambil/mengembalikan HPP, belanja toko, profit, atau potongan internal.
     */
    public function getDashboardData(int $kolabId): array {
        $stmt = $this->db->prepare("
            SELECT pk.id, pk.kode_paket, pk.nama_paket, pk.tahun_anggaran, pk.tags,
                   pk.pagu, pk.pagu_nego, pk.status, pk.group_id, pk.perusahaan_id, pk.created_at,
                   COALESCE(pi_sum.total_item_harga, 0) as total_item_harga,
                   COALESCE(pi_sum.total_item_qty, 0) as total_item_qty,
                   COALESCE(pi_sum.total_item_count, 0) as total_item_count,
                   COALESCE(p_prog.total_dist, 0) as total_dist,
                   COALESCE(p_prog.pesan_done, 0) as pesan_done,
                   COALESCE(p_prog.dist_done, 0) as dist_done,
                   COALESCE(p_prog.dok_done, 0) as dok_done,
                   COALESCE(p_prog.total_bast, 0) as total_bast,
                   COALESCE(p_prog.bast_done, 0) as bast_done,
                   p.nama as perusahaan_nama,
                   pg.nama_group, pg.kode_group,
                   a.name as pic_user_name
            FROM kolaborasi_groups kg
            INNER JOIN paket_group pg ON kg.group_id = pg.id
            INNER JOIN paket_kegiatan pk ON pk.group_id = pg.id
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            LEFT JOIN admins a ON kg.user_id = a.id
            LEFT JOIN (
                SELECT paket_id,
                       COUNT(*) as total_item_count,
                       SUM(jumlah) as total_item_qty,
                       SUM(total_harga) as total_item_harga
                FROM paket_items
                GROUP BY paket_id
            ) pi_sum ON pk.id = pi_sum.paket_id
            LEFT JOIN (
                SELECT 
                    pd.paket_id,
                    COUNT(DISTINCT pd.id) as total_dist,
                    COUNT(DISTINCT CASE WHEN pd.status_barang = 'Sudah Dipesan' THEN pd.id END) as pesan_done,
                    COUNT(DISTINCT CASE WHEN pd.status_distribusi IN ('Tiba di Tujuan', 'Tiba di Gudang') THEN pd.id END) as dist_done,
                    COUNT(DISTINCT pdk.distribusi_id) as dok_done,
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
            WHERE kg.kolaborasi_id = ?
            ORDER BY p.nama ASC, pk.created_at DESC
        ");
        $stmt->execute([$kolabId]);
        $rows = $stmt->fetchAll();

        $pakets = [];
        $companyStats = [];
        $summary = [
            'total_paket'     => 0,
            'total_kontrak'   => 0,
            'total_items'     => 0,
            'total_units'     => 0,
            'total_lokasi'    => 0,
            'paket_selesai'   => 0,
            'paket_proses'    => 0,
            'paket_belum'     => 0,
            'order_pct'       => 0,
            'dist_pct'        => 0,
            'dok_pct'         => 0,
            'bast_pct'        => 0,
            'total_perusahaan'=> 0,
        ];

        $sumOrder = 0; $sumDist = 0; $sumDok = 0; $sumBast = 0;

        foreach ($rows as $r) {
            $itemTotal = (float)($r['total_item_harga'] ?? 0);
            $effectivePagu = ($itemTotal > 0) ? $itemTotal : (float)($r['pagu'] ?? 0);
            $hasNego = (!empty($r['pagu_nego']) && (float)$r['pagu_nego'] > 0);
            $acuan = $hasNego ? (float)$r['pagu_nego'] : $effectivePagu;

            $totalDist = (int)$r['total_dist'];
            $totalBast = (int)$r['total_bast'];
            $orderPct = $totalDist > 0 ? (int)round(((int)$r['pesan_done'] / $totalDist) * 100) : 0;
            $distPct  = $totalDist > 0 ? (int)round(((int)$r['dist_done'] / $totalDist) * 100) : 0;
            $dokPct   = $totalDist > 0 ? (int)round(((int)$r['dok_done'] / $totalDist) * 100) : 0;
            $bastPct  = $totalBast > 0 ? (int)round(((int)$r['bast_done'] / $totalBast) * 100) : 0;

            $rawSt = strtolower(trim($r['status'] ?? 'proses'));
            if ($rawSt === 'cair' || $rawSt === 'selesai') {
                $normStatus = 'selesai';
                $summary['paket_selesai']++;
            } elseif ($rawSt === 'belum') {
                $normStatus = 'belum';
                $summary['paket_belum']++;
            } else {
                $normStatus = 'proses';
                $summary['paket_proses']++;
            }

            $compKey = (string)($r['perusahaan_nama'] ?: ('User: ' . ($r['pic_user_name'] ?? 'Lainnya')));
            if (!isset($companyStats[$compKey])) {
                $companyStats[$compKey] = [
                    'nama'          => $compKey,
                    'total_paket'   => 0,
                    'total_kontrak' => 0,
                    'selesai'       => 0,
                    'proses'        => 0,
                    'belum'         => 0,
                    'sum_order'     => 0,
                    'sum_dist'      => 0,
                    'sum_dok'       => 0,
                    'sum_bast'      => 0,
                ];
            }
            $companyStats[$compKey]['total_paket']++;
            $companyStats[$compKey]['total_kontrak'] += $acuan;
            $companyStats[$compKey][$normStatus]++;
            $companyStats[$compKey]['sum_order'] += $orderPct;
            $companyStats[$compKey]['sum_dist']  += $distPct;
            $companyStats[$compKey]['sum_dok']   += $dokPct;
            $companyStats[$compKey]['sum_bast']  += $bastPct;

            $summary['total_paket']++;
            $summary['total_kontrak'] += $acuan;
            $summary['total_items']   += (int)$r['total_item_count'];
            $summary['total_units']   += (float)$r['total_item_qty'];
            $summary['total_lokasi']  += $totalBast;

            $sumOrder += $orderPct;
            $sumDist  += $distPct;
            $sumDok   += $dokPct;
            $sumBast  += $bastPct;

            $pakets[] = [
                'id'              => (int)$r['id'],
                'kode_paket'      => $r['kode_paket'],
                'nama_paket'      => $r['nama_paket'],
                'tahun_anggaran'  => $r['tahun_anggaran'],
                'tags'            => $r['tags'],
                'status'          => $r['status'],
                'norm_status'     => $normStatus,
                'perusahaan_nama' => $compKey,
                'nama_group'      => $r['nama_group'],
                'acuan'           => $acuan,
                'item_count'      => (int)$r['total_item_count'],
                'item_qty'        => (float)$r['total_item_qty'],
                'total_lokasi'    => $totalBast,
                'bast_done'       => (int)$r['bast_done'],
                'order_pct'       => $orderPct,
                'dist_pct'        => $distPct,
                'dok_pct'         => $dokPct,
                'bast_pct'        => $bastPct,
            ];
        }

        if ($summary['total_paket'] > 0) {
            $summary['order_pct'] = (int)round($sumOrder / $summary['total_paket']);
            $summary['dist_pct']  = (int)round($sumDist / $summary['total_paket']);
            $summary['dok_pct']   = (int)round($sumDok / $summary['total_paket']);
            $summary['bast_pct']  = (int)round($sumBast / $summary['total_paket']);
        }

        foreach ($companyStats as &$cs) {
            $n = max(1, $cs['total_paket']);
            $cs['order_pct'] = (int)round($cs['sum_order'] / $n);
            $cs['dist_pct']  = (int)round($cs['sum_dist'] / $n);
            $cs['dok_pct']   = (int)round($cs['sum_dok'] / $n);
            $cs['bast_pct']  = (int)round($cs['sum_bast'] / $n);
            $cs['avg_pct']   = (int)round(($cs['order_pct'] + $cs['dist_pct'] + $cs['dok_pct'] + $cs['bast_pct']) / 4);
        }
        unset($cs);

        $summary['total_perusahaan'] = count($companyStats);

        return [
            'pakets'       => $pakets,
            'companyStats' => array_values($companyStats),
            'summary'      => $summary,
        ];
    }
}
