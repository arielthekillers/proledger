<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Creating table paket_items if not exists...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS paket_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paket_id INT NOT NULL,
        komponen VARCHAR(255) NOT NULL,
        spesifikasi TEXT NULL,
        satuan VARCHAR(50) DEFAULT 'Unit',
        jumlah DECIMAL(12,2) NOT NULL DEFAULT 1,
        harga_satuan DECIMAL(15,2) NOT NULL DEFAULT 0,
        total_harga DECIMAL(15,2) NOT NULL DEFAULT 0,
        link_asli TEXT NULL,
        link_pembanding_1 TEXT NULL,
        link_pembanding_2 TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_paket_id (paket_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table paket_items created successfully.\n";

// Check if paket 62 has items, if not seed the sample from screenshot
$stmt = $db->prepare("SELECT COUNT(*) FROM paket_items WHERE paket_id = 62");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    echo "Seeding sample items for Paket 62 from spreadsheet screenshot...\n";
    $insert = $db->prepare("
        INSERT INTO paket_items (
            paket_id, komponen, spesifikasi, satuan, jumlah, harga_satuan, total_harga, 
            link_asli, link_pembanding_1, link_pembanding_2
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $items = [
        [
            62,
            'Tenda Lipat',
            'Uk. 3x3 (setara), Rangka besi hollow, Tanpa dinding',
            'Unit',
            4,
            1689905,
            6759620,
            'https://katalog.inaproc.id/pijar-timur/tenda-lipat-3x3',
            'https://katalog.inaproc.id/berkah-pendi/tenda-kerucut-3x3-meter',
            'https://katalog.inaproc.id/asta-karya/tenda-kerucut-3x3'
        ],
        [
            62,
            'Tenda Camping',
            'Double layer dan bahan waterproof , Kapasitas 4-5 Orang',
            'Unit',
            4,
            3167494,
            12669976,
            'https://katalog.inaproc.id/pijar-timur/tenda-camping-4-5-orang',
            'https://katalog.inaproc.id/solusi-klik/tenda-camping-rosy-4-5-orang-adventure',
            'https://katalog.inaproc.id/solusi-klik-tanjung-pura/tenda-camping-rosy-4-5-orang-adventure'
        ],
        [
            62,
            'Tenda 4x6',
            'Tenda 4x6',
            'Buah',
            6,
            5302453,
            31814718,
            'https://katalog.inaproc.id/pijar-timur/tenda-regu-4x6',
            'https://katalog.inaproc.id/zonasurvey-indonesia/tenda-4x6-meter',
            'https://katalog.inaproc.id/bhinneka-sejahtera/tenda-siaga-ukuran-4x6'
        ]
    ];

    foreach ($items as $item) {
        $insert->execute($item);
    }
    echo "Sample items seeded successfully.\n";
}

echo "Migration finished!\n";
