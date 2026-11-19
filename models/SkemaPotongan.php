<?php
/**
 * ProLedger - SkemaPotongan Model
 * Mengelola skema persentase potongan (Pasal & Biaya Admin) yang dinamis dan reusable
 */

class SkemaPotongan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Dapatkan semua skema potongan (milik perusahaan aktif atau global default)
     */
    public function getAll($perusahaanId = null) {
        $sql = "SELECT * FROM skema_potongan";
        $params = [];

        if ($perusahaanId !== null) {
            $sql .= " WHERE (perusahaan_id = ? OR perusahaan_id IS NULL)";
            $params[] = $perusahaanId;
        }

        $sql .= " ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan detail skema berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM skema_potongan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Buat skema potongan baru
     */
    public function create($data) {
        $tipePotongan = in_array($data['tipe_potongan'] ?? '', ['kontrak', 'dpp']) ? $data['tipe_potongan'] : 'dpp';
        $stmt = $this->db->prepare("
            INSERT INTO skema_potongan (perusahaan_id, nama_skema, ada_pasal, persen_pasal, ada_admin, persen_admin, tipe_potongan)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['perusahaan_id'] ?? null,
            trim($data['nama_skema']),
            !empty($data['ada_pasal']) ? 1 : 0,
            (float)($data['persen_pasal'] ?? 0),
            !empty($data['ada_admin']) ? 1 : 0,
            (float)($data['persen_admin'] ?? 0),
            $tipePotongan
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Hapus skema potongan (hanya skema custom perusahaan)
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM skema_potongan WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
