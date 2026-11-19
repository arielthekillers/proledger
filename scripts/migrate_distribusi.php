<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Creating table lokasi_penerima...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS lokasi_penerima (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_lokasi VARCHAR(255) NOT NULL,
        kategori VARCHAR(100) DEFAULT 'Sekolah',
        nama_pic VARCHAR(255) NULL,
        no_kontak VARCHAR(100) NULL,
        alamat_lengkap TEXT NULL,
        kab_kota VARCHAR(100) NULL,
        no_rekening VARCHAR(100) NULL,
        nama_rekening VARCHAR(255) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_nama_lokasi (nama_lokasi),
        INDEX idx_kategori (kategori)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table lokasi_penerima created successfully.\n";

echo "Creating table paket_distribusi...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS paket_distribusi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paket_id INT NOT NULL,
        item_id INT NOT NULL,
        lokasi_id INT NULL,
        nama_tujuan VARCHAR(255) NOT NULL,
        kategori VARCHAR(100) DEFAULT 'Sekolah',
        jumlah DECIMAL(12,2) NOT NULL DEFAULT 1,
        nama_pic VARCHAR(255) NULL,
        no_kontak VARCHAR(100) NULL,
        alamat TEXT NULL,
        kab_kota VARCHAR(100) NULL,
        status VARCHAR(50) DEFAULT 'Belum Dikirim',
        tanggal_kirim DATE NULL,
        tanggal_terima DATE NULL,
        no_resi VARCHAR(100) NULL,
        catatan TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_distribusi_paket (paket_id),
        INDEX idx_distribusi_item (item_id),
        INDEX idx_distribusi_lokasi (lokasi_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table paket_distribusi created successfully.\n";

// Seed master lokasi_penerima from screenshot Image 2 and Image 1
$checkLokasi = $db->query("SELECT COUNT(*) FROM lokasi_penerima")->fetchColumn();
if ($checkLokasi == 0) {
    echo "Seeding master lokasi penerima (Sekolah & Instansi)...\n";
    $insLokasi = $db->prepare("
        INSERT INTO lokasi_penerima (nama_lokasi, kategori, nama_pic, no_kontak, alamat_lengkap, kab_kota)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $masterSekolah = [
        ['SMKN 1 BONTANG', 'Sekolah', 'Muhammad Sultan, S.Ag., M.Pd.I', '081347071619', 'Jl. Ciptomangunkusumo No. 2, Kel. Gn Elai Kec. Bontang Utara', 'Kota Bontang'],
        ['SMKN 2 BONTANG', 'Sekolah', 'Nurwahidah, S.Pd, M.Pd', '081258348205', 'Jl. Cumi - Cumi No. 1 Kel. Tanjung Laut Indah Kec. Bontang Selatan', 'Kota Bontang'],
        ['SMKN 3 BONTANG', 'Sekolah', 'Arif Munawar, S.Pd.I., M.Pd', '081250603288', 'Jl. Palembang RT 16 Kel. Telihan Kec. Bontang Barat', 'Kota Bontang'],
        ['SMKN 4 BONTANG', 'Sekolah', 'Muhammad Yunani, S.Pd', '085252262833', 'Jalan Karya Bakti Kelurahan Bontang Lestari Kecamatan Bontang', 'Kota Bontang'],
        ['SMKN 1 SANGATTA UTARA', 'Sekolah', 'Abu Bakar, S.Pd., M.M', '081255026665', 'Jl. Majay Sangatta Utara Kec. Sangatta Utara', 'Kab. Kutim'],
        ['SMKN 2 SANGATTA UTARA', 'Sekolah', 'Mustari, S.Pd., M.Pd', '082151189131', 'Jl. Padat Karya RT. 32 Sangatta Utara, Kec. Sangatta Utara', 'Kab. Kutim'],
        ['SMKN 1 KARANGAN', 'Sekolah', 'Ibus I., S.Sos.I., M.Pd', '085250994556', 'Jl. Ki Hajar Dewantara km.4 Desa Karangan Seberang', 'Kab. Kutim'],
        ['SMKN 1 BENGALON', 'Sekolah', 'A. Asriani, S.Pd., M.Pd', '085252390787', 'Jl. Sulawesi Desa Sepaso Barat Kec. Bengalon', 'Kab. Kutim'],
        ['SMKN 2 BENGALON', 'Sekolah', 'Patminingsih, S.Pd', '085250034350', 'Jl. Poros Sangatta -Ma. Wahau Km 106 Desa Tepian Indah', 'Kab. Kutim'],
        ['SMKN 1 MUARA BENGKAL', 'Sekolah', 'ADITYA PRATAMA PUTRA, M. Pd', '082151708573', 'Jl. Abdul Haib, RT.8, Desa Muara Bengkal Ulu', 'Kab. Kutim'],
        ['SMKN 1 RANTAU PULUNG', 'Sekolah', 'Dr. Khusnul Wardan, M. Pd', '085250058164', 'Jl. Pendidikan Rt 05 Rw 02 Ds. Margomulyo', 'Kab. Kutim'],
        ['SMKN 1 KALIORANG', 'Sekolah', 'Andi Hasniah Tenri Ola, S.Si., M.Si', '082190295817', 'Jalan Ery Soepardjan RT 12 / RW 01 Desa Bukit Makmur', 'Kab. Kutim'],
        ['SMKN 1 SANGKULIRANG', 'Sekolah', 'Panji Ongko Pramono, S.Pd., M.Pd', '085393304366', 'Jl. Mulawarman Desa RT III Desa Kerayaan Bual-Bual', 'Kab. Kutim'],
        ['SMKN 2 SANGKULIRANG', 'Sekolah', 'Maryatul Isnaaniah, S.P', '081346530146', 'Jl. Ahmad Yani RT 4, Desa Mandu Pantai Sejahtera', 'Kab. Kutim'],
        ['SMKN 1 TELEN', 'Sekolah', 'Eko Hadi Purwanto, S. Hut, M. M, M. Pd.', '081215138666', 'Jl. Poros Desa Muara Pantun Kecamatan Telen', 'Kab. Kutim'],
        ['SMKN 1 MUARA WAHAU', 'Sekolah', 'Avendi Perangin Angin, S.Pd., M.Pd', '082273369455', 'Jl. poros Muara Wahau-Sangatta, Desa karya bakti', 'Kab. Kutim'],
        ['SMKN 1 KONGBENG', 'Sekolah', 'Bambang Setiono, S.Pd.', '082353936330', 'Jalan Hamengkubuwono Desa Marga Mulia Kec. Kongbeng', 'Kab. Kutim'],
        // Destination schools from Image 1
        ['SMAN 2 BONTANG', 'Sekolah', 'Kepala Sekolah SMAN 2 Bontang', '081234567890', 'Bontang, Kalimantan Timur', 'Kota Bontang'],
        ['SMAN 1 SANDARAN', 'Sekolah', 'Kepala Sekolah SMAN 1 Sandaran', '081234567891', 'Kecamatan Sandaran, Kutai Timur', 'Kab. Kutim'],
        ['SMAN 1 LONG MESANGAT', 'Sekolah', 'Kepala Sekolah SMAN 1 Long Mesangat', '081234567892', 'Kecamatan Long Mesangat, Kutai Timur', 'Kab. Kutim'],
        ['SMAN 1 MUARA ANCALONG', 'Sekolah', 'Kepala Sekolah SMAN 1 Muara Ancalong', '081234567893', 'Kecamatan Muara Ancalong, Kutai Timur', 'Kab. Kutim'],
        ['SMAN 1 KARANGAN', 'Sekolah', 'Kepala Sekolah SMAN 1 Karangan', '081234567894', 'Kecamatan Karangan, Kutai Timur', 'Kab. Kutim'],
        // Instansi Tujuan Umum
        ['Kantor Dinas Pendidikan & Kebudayaan', 'Instansi / Dinas', 'Kepala Dinas / Pengurus Barang', '081155001122', 'Gedung Kantor Dinas Pendidikan', 'Kota Samarinda']
    ];

    foreach ($masterSekolah as $m) {
        $insLokasi->execute($m);
    }
    echo "Master lokasi seeded (" . count($masterSekolah) . " lokasi).\n";
}

// Seed sample distribution for Paket 62 from Image 1
$checkDist = $db->prepare("SELECT COUNT(*) FROM paket_distribusi WHERE paket_id = 62");
$checkDist->execute();
if ($checkDist->fetchColumn() == 0) {
    echo "Seeding initial distribution for Paket 62 from Image 1...\n";
    // Get item IDs of Paket 62
    $getItems = $db->prepare("SELECT id, komponen FROM paket_items WHERE paket_id = 62 ORDER BY id ASC");
    $getItems->execute();
    $itemsMap = [];
    while ($row = $getItems->fetch(PDO::FETCH_ASSOC)) {
        $itemsMap[$row['komponen']] = $row['id'];
    }

    $insDist = $db->prepare("
        INSERT INTO paket_distribusi (paket_id, item_id, nama_tujuan, kategori, jumlah, kab_kota, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $distData = [];
    if (isset($itemsMap['Tenda Lipat'])) {
        $distData[] = [62, $itemsMap['Tenda Lipat'], 'SMAN 2 BONTANG', 'Sekolah', 4, 'Kota Bontang', 'Belum Dikirim'];
    }
    if (isset($itemsMap['Tenda Camping'])) {
        $distData[] = [62, $itemsMap['Tenda Camping'], 'SMAN 2 BONTANG', 'Sekolah', 4, 'Kota Bontang', 'Belum Dikirim'];
    }
    if (isset($itemsMap['Tenda 4x6'])) {
        $distData[] = [62, $itemsMap['Tenda 4x6'], 'SMAN 1 SANDARAN', 'Sekolah', 2, 'Kab. Kutim', 'Belum Dikirim'];
        $distData[] = [62, $itemsMap['Tenda 4x6'], 'SMAN 1 LONG MESANGAT', 'Sekolah', 1, 'Kab. Kutim', 'Belum Dikirim'];
        $distData[] = [62, $itemsMap['Tenda 4x6'], 'SMAN 1 MUARA ANCALONG', 'Sekolah', 1, 'Kab. Kutim', 'Belum Dikirim'];
        $distData[] = [62, $itemsMap['Tenda 4x6'], 'SMAN 1 KARANGAN', 'Sekolah', 2, 'Kab. Kutim', 'Belum Dikirim'];
    }

    foreach ($distData as $d) {
        $insDist->execute($d);
    }
    echo "Initial distribution for Paket 62 seeded successfully (" . count($distData) . " alokasi).\n";
}

echo "Migration done!\n";
