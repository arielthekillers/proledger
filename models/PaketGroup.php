<?php
/**
 * ProLedger - PaketGroup Model
 * Model untuk manajemen pengelompokan paket kegiatan (Group Proyek / Program)
 */

class PaketGroup {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Dapatkan semua group dengan agregasi statistik paket di dalamnya
     */
    public function getAll($perusahaanId = null) {
        $sql = "
            SELECT 
                pg.*,
                p.nama as perusahaan_nama,
                COUNT(pk.id) as total_paket,
                COALESCE(SUM(pk.pagu), 0) as total_pagu,
                COALESCE(SUM(CASE WHEN pk.pagu_nego > 0 THEN pk.pagu_nego ELSE pk.pagu END), 0) as total_pagu_nego,
                COALESCE(SUM(pk.dpp), 0) as total_dpp,
                COALESCE(SUM(pk.total_pasal), 0) as total_pasal,
                0 as total_pemodal,
                0 as total_realisasi,
                SUM(CASE WHEN pk.status = 'Selesai' THEN 1 ELSE 0 END) as total_selesai,
                SUM(CASE WHEN pk.status != 'Selesai' THEN 1 ELSE 0 END) as total_proses
            FROM paket_group pg
            LEFT JOIN perusahaan p ON pg.perusahaan_id = p.id
            LEFT JOIN paket_kegiatan pk ON pg.id = pk.group_id
        ";

        $params = [];
        if ($perusahaanId !== null) {
            $sql .= " WHERE (pg.perusahaan_id = ? OR pg.perusahaan_id IS NULL)";
            $params[] = $perusahaanId;
        }

        $sql .= " GROUP BY pg.id ORDER BY pg.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
        $stmt = $this->db->prepare("
            SELECT pk.*, p.nama as perusahaan_nama
            FROM paket_kegiatan pk
            LEFT JOIN perusahaan p ON pk.perusahaan_id = p.id
            WHERE pk.group_id = ?
            ORDER BY pk.created_at DESC
        ");
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan statistik roll-up keuangan group
     */
    public function getStats($groupId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(id) as total_paket,
                COALESCE(SUM(pagu), 0) as total_pagu,
                COALESCE(SUM(CASE WHEN pagu_nego > 0 THEN pagu_nego ELSE pagu END), 0) as total_pagu_nego,
                COALESCE(SUM(dpp), 0) as total_dpp,
                COALESCE(SUM(total_pasal), 0) as total_pasal,
                0 as total_pemodal,
                0 as total_realisasi,
                SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as paket_selesai,
                SUM(CASE WHEN status != 'Selesai' THEN 1 ELSE 0 END) as paket_proses
            FROM paket_kegiatan
            WHERE group_id = ?
        ");
        $stmt->execute([$groupId]);
        return $stmt->fetch();
    }

    /**
     * Buat group baru
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO paket_group (perusahaan_id, nama_group, kode_group, deskripsi)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['perusahaan_id'] ?? null,
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
     * Hapus group (lepaskan paket anggota agar group_id menjadi NULL)
     */
    public function delete($id) {
        // Set group_id = null pada paket di dalamnya
        $stmtDetach = $this->db->prepare("UPDATE paket_kegiatan SET group_id = NULL WHERE group_id = ?");
        $stmtDetach->execute([$id]);

        $stmt = $this->db->prepare("DELETE FROM paket_group WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
