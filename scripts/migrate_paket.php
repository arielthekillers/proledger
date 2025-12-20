<?php
// Load config
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to " . DB_NAME . "\n";

    // 1. Map old values
    echo "Mapping old status values...\n";
    $db->exec("UPDATE paket_kegiatan SET status = 'Kontrak' WHERE status = 'aktif'");
    $db->exec("UPDATE paket_kegiatan SET status = 'Selesai' WHERE status = 'selesai'");

    // 2. Add 'perusahaan' column
    echo "Adding perusahaan column...\n";
    try {
        $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN perusahaan VARCHAR(255) NULL AFTER nama_paket");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Column 'perusahaan' already exists.\n";
        } else {
            throw $e;
        }
    }

    // 3. Drop date columns
    echo "Dropping date columns...\n";
    try {
        $db->exec("ALTER TABLE paket_kegiatan DROP COLUMN tanggal_mulai");
    } catch (PDOException $e) {
        echo "Column 'tanggal_mulai' likely already dropped.\n";
    }
    try {
        $db->exec("ALTER TABLE paket_kegiatan DROP COLUMN tanggal_selesai");
    } catch (PDOException $e) {
        echo "Column 'tanggal_selesai' likely already dropped.\n";
    }

    // 4. Modify 'status' enum
    echo "Modifying status enum...\n";
    $db->exec("ALTER TABLE paket_kegiatan MODIFY COLUMN status ENUM('Kontrak', 'Pemesanan', 'Distribusi', 'Selesai') DEFAULT 'Kontrak'");

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
