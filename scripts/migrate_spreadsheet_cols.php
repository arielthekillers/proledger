<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Running migration for spreadsheet columns...\n";

// Check existing columns
$stmt = $db->query("SHOW COLUMNS FROM paket_kegiatan");
$existingCols = $stmt->fetchAll(PDO::FETCH_COLUMN);

$columnsToAdd = [
    'penanggung_jawab' => "VARCHAR(100) NULL DEFAULT NULL AFTER perusahaan",
    'progres_barang' => "VARCHAR(50) NOT NULL DEFAULT 'Belum' AFTER status",
    'setor_awal' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER total_pasal",
    'selisih' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER setor_awal"
];

foreach ($columnsToAdd as $col => $definition) {
    if (!in_array($col, $existingCols)) {
        $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN `$col` $definition");
        echo "Added column: $col\n";
    } else {
        echo "Column already exists: $col\n";
    }
}

// Modify status enum if needed to include 'Proses', 'Cair' or make it varchar(50)
$db->exec("ALTER TABLE paket_kegiatan MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'Proses'");
echo "Updated status column type to VARCHAR(50)\n";

// Update existing records: if setor_awal is not null, calculate selisih
$db->exec("UPDATE paket_kegiatan SET selisih = setor_awal - total_pasal WHERE setor_awal IS NOT NULL");

// If status was 'Kontrak' or empty, default to 'Proses'
$db->exec("UPDATE paket_kegiatan SET status = 'Proses' WHERE status IS NULL OR status = '' OR status = 'Kontrak'");

echo "Migration completed successfully!\n";
