<?php
/**
 * Migration Script: Create paket_group table and add group_id to paket_kegiatan
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database successfully.\n";

    // 1. Create table paket_group
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS paket_group (
            id INT AUTO_INCREMENT PRIMARY KEY,
            perusahaan_id INT NULL,
            nama_group VARCHAR(255) NOT NULL,
            kode_group VARCHAR(50) NULL,
            deskripsi TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_perusahaan (perusahaan_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $db->exec($createTableSql);
    echo "✓ Table 'paket_group' verified / created successfully.\n";

    // 2. Check if group_id exists in paket_kegiatan
    $cols = $db->query("SHOW COLUMNS FROM paket_kegiatan LIKE 'group_id'")->fetchAll();
    if (empty($cols)) {
        $alterSql = "ALTER TABLE paket_kegiatan ADD COLUMN group_id INT NULL AFTER perusahaan_id, ADD INDEX idx_group_id (group_id)";
        $db->exec($alterSql);
        echo "✓ Column 'group_id' added to table 'paket_kegiatan'.\n";
    } else {
        echo "✓ Column 'group_id' already exists in 'paket_kegiatan'.\n";
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
