<?php
// Ensure config is loaded first to define DB constants
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Migration...\n";

    // 1. Create table perusahaan
    $sql = "CREATE TABLE IF NOT EXISTS perusahaan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        telepon VARCHAR(50),
        alamat TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    echo "Table 'perusahaan' created/checked.\n";

    // 2. Add perusahaan_id to paket_kegiatan if not exists
    $stmt = $db->query("SHOW COLUMNS FROM paket_kegiatan LIKE 'perusahaan_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN perusahaan_id INT AFTER nama_paket");
        echo "Column 'perusahaan_id' added to 'paket_kegiatan'.\n";
    } else {
        echo "Column 'perusahaan_id' already exists.\n";
    }

    // 3. Migrate existing data
    $stmt = $db->query("SELECT DISTINCT perusahaan FROM paket_kegiatan WHERE perusahaan IS NOT NULL AND perusahaan != ''");
    $companies = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($companies as $compName) {
        // Check if exists
        $stmt = $db->prepare("SELECT id FROM perusahaan WHERE nama = ?");
        $stmt->execute([$compName]);
        $existing = $stmt->fetch();

        if ($existing) {
            $id = $existing['id'];
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO perusahaan (nama) VALUES (?)");
            $stmt->execute([$compName]);
            $id = $db->lastInsertId();
            echo "Migrated company: $compName (ID: $id)\n";
        }

        // Update paket
        $stmt = $db->prepare("UPDATE paket_kegiatan SET perusahaan_id = ? WHERE perusahaan = ?");
        $stmt->execute([$id, $compName]);
    }

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
