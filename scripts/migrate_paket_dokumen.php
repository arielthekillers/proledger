<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS `paket_dokumen` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `paket_id` INT NOT NULL,
        `jenis_dokumen` VARCHAR(100) NOT NULL DEFAULT 'BAST',
        `nama_dokumen` VARCHAR(255) NOT NULL,
        `nomor_dokumen` VARCHAR(100) DEFAULT NULL,
        `tanggal_dokumen` DATE DEFAULT NULL,
        `nama_file_asli` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `file_size` INT NOT NULL DEFAULT 0,
        `file_ext` VARCHAR(20) NOT NULL DEFAULT '',
        `mime_type` VARCHAR(100) DEFAULT NULL,
        `keterangan` TEXT DEFAULT NULL,
        `uploaded_by` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_paket_id` (`paket_id`),
        INDEX `idx_jenis_dokumen` (`jenis_dokumen`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table paket_dokumen checked/created successfully.\n";

    // Ensure upload directory exists
    $uploadDir = __DIR__ . '/../public/uploads/dokumen';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "Created directory: {$uploadDir}\n";
    } else {
        echo "Upload directory exists: {$uploadDir}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
