<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "=== MIGRATION: MASTER PROVINSI & KABUPATEN/KOTA ===\n";

try {
    $db = Database::getInstance()->getConnection();

    // 1. Tabel master_provinsi
    $db->exec("
        CREATE TABLE IF NOT EXISTS master_provinsi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kode VARCHAR(10) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "- Tabel master_provinsi siap.\n";

    // 2. Tabel master_kabupaten
    $db->exec("
        CREATE TABLE IF NOT EXISTS master_kabupaten (
            id INT AUTO_INCREMENT PRIMARY KEY,
            provinsi_id INT NOT NULL,
            kode VARCHAR(10) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            tipe ENUM('Kabupaten', 'Kota') NOT NULL DEFAULT 'Kabupaten',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_provinsi (provinsi_id),
            CONSTRAINT fk_kabupaten_provinsi FOREIGN KEY (provinsi_id) 
                REFERENCES master_provinsi(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "- Tabel master_kabupaten siap.\n";

    // 3. View alias 'provinsi' dan 'kabupaten' agar fleksibel
    $db->exec("CREATE OR REPLACE VIEW provinsi AS SELECT * FROM master_provinsi");
    $db->exec("CREATE OR REPLACE VIEW kabupaten AS SELECT * FROM master_kabupaten");
    echo "- View alias 'provinsi' dan 'kabupaten' siap.\n";

    // 4. Seed Data: Kalimantan Timur
    $stmtProv = $db->prepare("
        INSERT INTO master_provinsi (kode, nama)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE nama = VALUES(nama)
    ");
    $stmtProv->execute(['64', 'Kalimantan Timur']);
    
    // Dapatkan ID provinsi Kaltim
    $stmtGetProv = $db->prepare("SELECT id FROM master_provinsi WHERE kode = ?");
    $stmtGetProv->execute(['64']);
    $kaltimId = $stmtGetProv->fetchColumn();

    echo "- Data Provinsi Kalimantan Timur ID: {$kaltimId}\n";

    // 5. Seed Data: 10 Kabupaten / Kota di Kalimantan Timur (Kode Resmi Kemendagri / BPS)
    $kabupatenList = [
        ['kode' => '64.01', 'nama' => 'Kabupaten Paser', 'tipe' => 'Kabupaten'],
        ['kode' => '64.02', 'nama' => 'Kabupaten Kutai Kartanegara', 'tipe' => 'Kabupaten'],
        ['kode' => '64.03', 'nama' => 'Kabupaten Berau', 'tipe' => 'Kabupaten'],
        ['kode' => '64.07', 'nama' => 'Kabupaten Kutai Barat', 'tipe' => 'Kabupaten'],
        ['kode' => '64.08', 'nama' => 'Kabupaten Kutai Timur', 'tipe' => 'Kabupaten'],
        ['kode' => '64.09', 'nama' => 'Kabupaten Penajam Paser Utara', 'tipe' => 'Kabupaten'],
        ['kode' => '64.11', 'nama' => 'Kabupaten Mahakam Ulu', 'tipe' => 'Kabupaten'],
        ['kode' => '64.71', 'nama' => 'Kota Balikpapan', 'tipe' => 'Kota'],
        ['kode' => '64.72', 'nama' => 'Kota Samarinda', 'tipe' => 'Kota'],
        ['kode' => '64.74', 'nama' => 'Kota Bontang', 'tipe' => 'Kota'],
    ];

    $stmtKab = $db->prepare("
        INSERT INTO master_kabupaten (provinsi_id, kode, nama, tipe)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            provinsi_id = VALUES(provinsi_id),
            nama = VALUES(nama),
            tipe = VALUES(tipe)
    ");

    foreach ($kabupatenList as $kab) {
        $stmtKab->execute([
            $kaltimId,
            $kab['kode'],
            $kab['nama'],
            $kab['tipe']
        ]);
        echo "  + [{$kab['kode']}] {$kab['nama']} ({$kab['tipe']})\n";
    }

    echo "\n=== MIGRATION SELESAI DENGAN SUKSES! ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
