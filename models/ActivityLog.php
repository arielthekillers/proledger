<?php
/**
 * ProLedger - ActivityLog Model
 * Mencatat log aktivitas penting di sistem untuk audit trail administrator
 */

class ActivityLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                user_name VARCHAR(150) NULL,
                user_role VARCHAR(30) NULL,
                action VARCHAR(50) NOT NULL,
                module VARCHAR(50) NOT NULL,
                target_id VARCHAR(64) NULL,
                description TEXT NULL,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_act_module (module),
                INDEX idx_act_action (action),
                INDEX idx_act_user (user_id),
                INDEX idx_act_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Rekam aktivitas secara aman tanpa mengganggu alur utama aplikasi
     */
    public static function record($action, $module, $description = '', $targetId = null, $userId = null, $userName = null, $userRole = null) {
        try {
            $instance = new self();
            $uid = $userId ?? ($_SESSION['admin_id'] ?? null);
            $uname = $userName ?? ($_SESSION['admin_name'] ?? 'Guest');
            $urole = $userRole ?? ($_SESSION['admin_role'] ?? 'guest');
            if (!empty($_SESSION['impersonator_admin'])) {
                $uname .= ' [via Admin: ' . ($_SESSION['impersonator_admin']['name'] ?? 'Admin') . ']';
            }
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }

            $stmt = $instance->db->prepare("
                INSERT INTO activity_logs (user_id, user_name, user_role, action, module, target_id, description, ip_address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $uid ? (int)$uid : null,
                mb_substr((string)$uname, 0, 150),
                mb_substr((string)$urole, 0, 30),
                mb_substr((string)$action, 0, 50),
                mb_substr((string)$module, 0, 50),
                $targetId !== null ? mb_substr((string)$targetId, 0, 64) : null,
                $description,
                mb_substr((string)$ip, 0, 45)
            ]);
        } catch (Throwable $e) {
            // Jangan gagalkan operasi utama jika logging bermasalah
        }
    }

    /**
     * Ambil daftar log dengan filter
     */
    public function getLogs($filter = [], $limit = 100, $offset = 0) {
        $where = [];
        $params = [];

        if (!empty($filter['module'])) {
            $where[] = "module = ?";
            $params[] = $filter['module'];
        }
        if (!empty($filter['action'])) {
            $where[] = "action = ?";
            $params[] = $filter['action'];
        }
        if (!empty($filter['search'])) {
            $where[] = "(user_name LIKE ? OR description LIKE ? OR target_id LIKE ? OR ip_address LIKE ?)";
            $term = '%' . $filter['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql = "SELECT * FROM activity_logs";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY id DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Daftar modul unik untuk dropdown filter
     */
    public function getModules() {
        return $this->db->query("SELECT DISTINCT module FROM activity_logs ORDER BY module ASC")->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Hapus log yang lebih lama dari X hari
     */
    public function clearOlderThan($days = 90) {
        $stmt = $this->db->prepare("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([(int)$days]);
        return $stmt->rowCount();
    }
}
