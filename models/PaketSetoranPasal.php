<?php
/**
 * ProLedger - PaketSetoranPasal Model
 * Mencatat riwayat setoran pasal (cicilan/pelunasan) per paket kegiatan
 */

class PaketSetoranPasal {
    private $db;
    private static $schemaChecked = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        if (self::$schemaChecked) return;
        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['_schema_ok_psp'])) {
            self::$schemaChecked = true;
            return;
        }
        self::$schemaChecked = true;
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS paket_setoran_pasal (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    paket_id INT(11) NOT NULL,
                    tanggal_setor DATE NOT NULL,
                    nominal DECIMAL(15,2) NOT NULL DEFAULT 0,
                    catatan VARCHAR(255) NULL,
                    created_by INT(11) NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_psp_paket (paket_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['_schema_ok_psp'] = true;
            }
        } catch (Throwable $e) {
            // Abaikan jika permission terbatas
        }
    }

    public function getByPaketId(int $paketId): array {
        $stmt = $this->db->prepare("
            SELECT psp.*, u.name as creator_name
            FROM paket_setoran_pasal psp
            LEFT JOIN users u ON psp.created_by = u.id
            WHERE psp.paket_id = ?
            ORDER BY psp.tanggal_setor DESC, psp.id DESC
        ");
        $stmt->execute([$paketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSumByPaketIds(array $paketIds): array {
        $ids = array_values(array_unique(array_filter(array_map('intval', $paketIds), fn($v) => $v > 0)));
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("
            SELECT paket_id, COALESCE(SUM(nominal), 0) as total_setor
            FROM paket_setoran_pasal
            WHERE paket_id IN ($placeholders)
            GROUP BY paket_id
        ");
        $stmt->execute($ids);
        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $map[(int)$r['paket_id']] = (float)$r['total_setor'];
        }
        return $map;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO paket_setoran_pasal (paket_id, tanggal_setor, nominal, catatan, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$data['paket_id'],
            !empty($data['tanggal_setor']) ? $data['tanggal_setor'] : date('Y-m-d'),
            (float)$data['nominal'],
            !empty($data['catatan']) ? trim($data['catatan']) : null,
            !empty($data['created_by']) ? (int)$data['created_by'] : null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $paketId): bool {
        $stmt = $this->db->prepare("DELETE FROM paket_setoran_pasal WHERE id = ? AND paket_id = ?");
        return $stmt->execute([$id, $paketId]);
    }
}
