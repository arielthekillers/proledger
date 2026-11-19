<?php
/**
 * Migration: Add pagu_nego, dpp, pasal_13, admin_2, total_pasal to paket_kegiatan
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    $columnsToAdd = [
        'pagu_nego' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER pagu",
        'dpp' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER pagu_nego",
        'pasal_13' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER dpp",
        'admin_2' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER pasal_13",
        'total_pasal' => "DECIMAL(15,2) NULL DEFAULT NULL AFTER admin_2"
    ];

    foreach ($columnsToAdd as $col => $definition) {
        $check = $db->query("SHOW COLUMNS FROM paket_kegiatan LIKE '{$col}'")->fetchAll();
        if (empty($check)) {
            $sql = "ALTER TABLE paket_kegiatan ADD COLUMN {$col} {$definition}";
            $db->exec($sql);
            echo "✓ Added column '{$col}' to 'paket_kegiatan'.\n";
        } else {
            echo "✓ Column '{$col}' already exists in 'paket_kegiatan'.\n";
        }
    }

    // Populate existing records with DPP, Pasal 13%, Admin 2%, and Total Pasal
    $stmt = $db->query("SELECT id, pagu, pagu_nego FROM paket_kegiatan");
    $packets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $db->prepare("
        UPDATE paket_kegiatan 
        SET dpp = ?, pasal_13 = ?, admin_2 = ?, total_pasal = ? 
        WHERE id = ?
    ");

    foreach ($packets as $p) {
        $acuan = (!empty($p['pagu_nego']) && $p['pagu_nego'] > 0) ? (float)$p['pagu_nego'] : (float)$p['pagu'];
        $dpp = round($acuan / 1.11);
        $pasal13 = round($dpp * 0.13);
        $admin2 = round($dpp * 0.02);
        $totalPasal = $pasal13 + $admin2;

        $updateStmt->execute([$dpp, $pasal13, $admin2, $totalPasal, $p['id']]);
    }

    echo "✓ Existing records updated with DPP and Pasal calculations.\n";
    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
