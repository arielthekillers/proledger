<?php
/**
 * ProLedger - AppSetting Model
 * Pengaturan global aplikasi (pengumuman, default PPN/TA, dll)
 */

class AppSetting {
    private $db;
    private static $cache = null;

    public static $defaults = [
        'announcement_enabled' => '0',
        'announcement_type'    => 'info',
        'announcement_title'   => '',
        'announcement_message' => '',
        'default_ppn_persen'   => '11',
        'default_tahun_anggaran' => '2026',
        'tahun_anggaran_list'  => '2024,2025,2026,2027',
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS app_settings (
                setting_key VARCHAR(80) PRIMARY KEY,
                setting_value TEXT NULL,
                updated_by INT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function getAll() {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $settings = self::$defaults;
        try {
            $rows = $this->db->query("SELECT setting_key, setting_value FROM app_settings")->fetchAll();
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) {
            // Fallback ke defaults
        }
        self::$cache = $settings;
        return $settings;
    }

    public static function get($key, $default = null) {
        try {
            $m = new self();
            $all = $m->getAll();
            return $all[$key] ?? ($default ?? (self::$defaults[$key] ?? null));
        } catch (Throwable $e) {
            return $default ?? (self::$defaults[$key] ?? null);
        }
    }

    public function setMany(array $pairs, $userId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO app_settings (setting_key, setting_value, updated_by)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_by = VALUES(updated_by)
        ");
        foreach ($pairs as $k => $v) {
            $stmt->execute([$k, (string)$v, $userId ? (int)$userId : null]);
        }
        self::$cache = null;
    }
}
