<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Creating table skema_potongan...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS skema_potongan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        perusahaan_id INT NULL,
        nama_skema VARCHAR(100) NOT NULL,
        ada_pasal TINYINT(1) DEFAULT 1,
        persen_pasal DECIMAL(5,2) DEFAULT 13.00,
        ada_admin TINYINT(1) DEFAULT 1,
        persen_admin DECIMAL(5,2) DEFAULT 2.00,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_perusahaan (perusahaan_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if skema_id exists in paket_kegiatan
$cols = $db->query("SHOW COLUMNS FROM paket_kegiatan")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('skema_id', $cols)) {
    $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN skema_id INT NULL AFTER group_id");
    echo "Added skema_id column to paket_kegiatan.\n";
}

// Seed default schemas if empty
$count = $db->query("SELECT COUNT(*) FROM skema_potongan")->fetchColumn();
if ($count == 0) {
    $stmt = $db->prepare("
        INSERT INTO skema_potongan (perusahaan_id, nama_skema, ada_pasal, persen_pasal, ada_admin, persen_admin)
        VALUES 
        (NULL, 'Skema 1 (13% Pasal + 2% Admin)', 1, 13.00, 1, 2.00),
        (NULL, 'Skema 2 (20% Murni Tanpa Admin)', 1, 20.00, 0, 0.00),
        (NULL, 'Skema 3 (15% Murni Tanpa Admin)', 1, 15.00, 0, 0.00)
    ");
    $stmt->execute();
    echo "Seeded default skema potongan.\n";
}

$stmt = $db->query("SELECT * FROM skema_potongan");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
