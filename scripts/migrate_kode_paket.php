<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Checking column kode_paket in paket_kegiatan...\n";
$cols = $db->query("SHOW COLUMNS FROM paket_kegiatan")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('kode_paket', $cols)) {
    echo "Adding column kode_paket to paket_kegiatan...\n";
    $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN kode_paket VARCHAR(10) NULL UNIQUE AFTER id");
    echo "Column kode_paket added successfully.\n";
} else {
    echo "Column kode_paket already exists.\n";
}

// Function to generate unique 5-char code
function generateRandomCode(PDO $db) {
    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Alphanumeric without ambiguous characters (0, O, 1, I)
    $max = strlen($chars) - 1;
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM paket_kegiatan WHERE kode_paket = ?");
    
    do {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, $max)];
        }
        $checkStmt->execute([$code]);
        $exists = $checkStmt->fetchColumn() > 0;
    } while ($exists);
    
    return $code;
}

// Populate existing paket without kode_paket
$stmt = $db->query("SELECT id, nama_paket, kode_paket FROM paket_kegiatan WHERE kode_paket IS NULL OR kode_paket = ''");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($rows) . " paket needing kode_paket.\n";

$updateStmt = $db->prepare("UPDATE paket_kegiatan SET kode_paket = ? WHERE id = ?");
foreach ($rows as $row) {
    $code = generateRandomCode($db);
    $updateStmt->execute([$code, $row['id']]);
    echo "Paket ID {$row['id']} ('{$row['nama_paket']}') assigned kode: {$code}\n";
}

echo "Migration completed successfully!\n";
