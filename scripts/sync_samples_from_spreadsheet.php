<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

$db = Database::getInstance()->getConnection();

$samples = [
    [
        'nama_paket' => 'Belanja Bahan-Isi Tabung Pemadam Kebakaran',
        'perusahaan' => 'CV. PIJAR TIMUR',
        'penanggung_jawab' => 'RAHMAN',
        'progres_barang' => 'DISTRIBUSI',
        'pagu' => 15584400,
        'pagu_nego' => null,
        'setor_awal' => null,
        'rab' => 10000000,
        'status' => 'CAIR'
    ],
    [
        'nama_paket' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Perlengkapan Dinas',
        'perusahaan' => 'CV. KARSA KREASI BERDIKARI',
        'penanggung_jawab' => 'RIFKI',
        'progres_barang' => 'BELANJA',
        'pagu' => 51909150,
        'pagu_nego' => null,
        'setor_awal' => 6499657,
        'rab' => 35000000,
        'status' => 'PROSES'
    ],
    [
        'nama_paket' => 'Belanja Alat/Bahan untuk Kegiatan Kantor Perlengkapan Pendukung Olahraga',
        'perusahaan' => 'CV. KARSA KREASI BERDIKARI',
        'penanggung_jawab' => 'RIFKI',
        'progres_barang' => 'BELANJA',
        'pagu' => 22960350,
        'pagu_nego' => null,
        'setor_awal' => 2792643,
        'rab' => 15000000,
        'status' => 'CAIR'
    ],
    [
        'nama_paket' => 'Belanja Obat-Obatan-Obat',
        'perusahaan' => 'PT. PUTRA KALTIM BERSINERGI',
        'penanggung_jawab' => 'ASHARI',
        'progres_barang' => 'DISTRIBUSI',
        'pagu' => 28038600,
        'pagu_nego' => null,
        'setor_awal' => 3589039,
        'rab' => 18000000,
        'status' => 'PROSES'
    ],
    [
        'nama_paket' => 'Belanja Natura dan Pakan-Natura',
        'perusahaan' => 'CV. KAYA PACE',
        'penanggung_jawab' => 'RIFKI',
        'progres_barang' => 'DISTRIBUSI',
        'pagu' => 4750800,
        'pagu_nego' => null,
        'setor_awal' => 558817,
        'rab' => 3000000,
        'status' => 'PROSES'
    ],
    [
        'nama_paket' => 'Belanja Peralatan dan Mesin-Alat Kantor dan Rumah Tangga-Alat Rumah Tangga-Mebel',
        'perusahaan' => 'CV. ARYA TIKVAH',
        'penanggung_jawab' => 'DARMAN',
        'progres_barang' => 'DISTRIBUSI',
        'pagu' => 14185800,
        'pagu_nego' => null,
        'setor_awal' => 1764482,
        'rab' => 9000000,
        'status' => 'CAIR'
    ]
];

foreach ($samples as $s) {
    // Check or create perusahaan
    $stmtP = $db->prepare("SELECT id FROM perusahaan WHERE nama = ?");
    $stmtP->execute([$s['perusahaan']]);
    $pId = $stmtP->fetchColumn();
    if (!$pId) {
        $stmtInsP = $db->prepare("INSERT INTO perusahaan (nama, email, telepon, alamat) VALUES (?, ?, ?, ?)");
        $stmtInsP->execute([$s['perusahaan'], strtolower(str_replace([' ', '.'], '', $s['perusahaan'])) . '@example.com', '08123456789', 'Alamat Perusahaan']);
        $pId = $db->lastInsertId();
    }

    // Check if paket exists
    $stmtChk = $db->prepare("SELECT id FROM paket_kegiatan WHERE nama_paket = ?");
    $stmtChk->execute([$s['nama_paket']]);
    $existingId = $stmtChk->fetchColumn();

    $dpp = calculateDPP($s['pagu']);
    $pasal13 = calculatePasal13($dpp);
    $admin2 = calculateAdmin2($dpp);
    $totalPasal = $pasal13 + $admin2;
    $selisih = $s['setor_awal'] !== null ? ($s['setor_awal'] - $totalPasal) : null;

    if ($existingId) {
        $stmtUpd = $db->prepare("
            UPDATE paket_kegiatan 
            SET pagu = ?, dpp = ?, pasal_13 = ?, admin_2 = ?, total_pasal = ?,
                setor_awal = ?, selisih = ?, penanggung_jawab = ?, progres_barang = ?,
                status = ?, perusahaan_id = ?, perusahaan = ?
            WHERE id = ?
        ");
        $stmtUpd->execute([
            $s['pagu'], $dpp, $pasal13, $admin2, $totalPasal,
            $s['setor_awal'], $selisih, $s['penanggung_jawab'], $s['progres_barang'],
            $s['status'], $pId, $s['perusahaan'], $existingId
        ]);
        echo "Updated: {$s['nama_paket']}\n";
    } else {
        $stmtIns = $db->prepare("
            INSERT INTO paket_kegiatan (
                nama_paket, pagu, pagu_nego, dpp, pasal_13, admin_2, total_pasal,
                setor_awal, selisih, rab, jumlah_pemodal, status, progres_barang,
                perusahaan_id, perusahaan, penanggung_jawab
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?)
        ");
        $stmtIns->execute([
            $s['nama_paket'], $s['pagu'], null, $dpp, $pasal13, $admin2, $totalPasal,
            $s['setor_awal'], $selisih, $s['rab'], $s['status'], $s['progres_barang'],
            $pId, $s['perusahaan'], $s['penanggung_jawab']
        ]);
        echo "Inserted: {$s['nama_paket']}\n";
    }
}

echo "Sample data sync complete!\n";
