<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== Running Logistics & BAST Migration ===\n";

    // 1. Table perusahaan: nama_direktur, jabatan_direktur, stempel_ttd
    $cols = $db->query("SHOW COLUMNS FROM `perusahaan`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('nama_direktur', $cols)) {
        $db->exec("ALTER TABLE `perusahaan` ADD COLUMN `nama_direktur` VARCHAR(255) NULL AFTER `nama`");
        echo "Added perusahaan.nama_direktur\n";
    }
    if (!in_array('jabatan_direktur', $cols)) {
        $db->exec("ALTER TABLE `perusahaan` ADD COLUMN `jabatan_direktur` VARCHAR(100) DEFAULT 'Direktur' AFTER `nama_direktur`");
        echo "Added perusahaan.jabatan_direktur\n";
    }
    if (!in_array('stempel_ttd', $cols)) {
        $db->exec("ALTER TABLE `perusahaan` ADD COLUMN `stempel_ttd` VARCHAR(255) NULL AFTER `kopsurat`");
        echo "Added perusahaan.stempel_ttd\n";
    }

    // Set sample direktur for CV. KARSA KREASI BERDIKARI matching user's image
    $db->exec("UPDATE `perusahaan` SET `nama_direktur` = 'Muhammad Rifki', `jabatan_direktur` = 'Direktur' WHERE `nama` LIKE '%KARSA KREASI%' AND (`nama_direktur` IS NULL OR `nama_direktur` = '')");

    // 2. Table paket_kegiatan: nomor_sp (Surat Pesanan / SPK / Kontrak)
    $colsPaket = $db->query("SHOW COLUMNS FROM `paket_kegiatan`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('nomor_sp', $colsPaket)) {
        $db->exec("ALTER TABLE `paket_kegiatan` ADD COLUMN `nomor_sp` VARCHAR(100) NULL AFTER `nama_paket`");
        echo "Added paket_kegiatan.nomor_sp\n";
    }

    // Set sample nomor_sp for Paket 62 / XW9J5 matching user's image
    $db->exec("UPDATE `paket_kegiatan` SET `nomor_sp` = 'EP-01M1ZS80DH32WZR9NZ8JCWCA44' WHERE `id` = 62 OR `kode_paket` = 'XW9J5'");

    // 3. Table lokasi_penerima: jabatan_pic
    $colsLok = $db->query("SHOW COLUMNS FROM `lokasi_penerima`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('jabatan_pic', $colsLok)) {
        $db->exec("ALTER TABLE `lokasi_penerima` ADD COLUMN `jabatan_pic` VARCHAR(100) DEFAULT 'Kepala Sekolah' AFTER `nama_pic`");
        echo "Added lokasi_penerima.jabatan_pic\n";
    }

    // 4. Table paket_distribusi: rute_pengiriman, status_pemesanan, tanggal_pesan, link_pembelian, kurir_ekspedisi, status_penerimaan
    $colsDist = $db->query("SHOW COLUMNS FROM `paket_distribusi`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('rute_pengiriman', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `rute_pengiriman` VARCHAR(50) DEFAULT 'kantor' AFTER `jumlah`");
        echo "Added paket_distribusi.rute_pengiriman\n";
    }
    if (!in_array('status_pemesanan', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `status_pemesanan` VARCHAR(50) DEFAULT 'Belum Dipesan' AFTER `rute_pengiriman`");
        echo "Added paket_distribusi.status_pemesanan\n";
    }
    if (!in_array('tanggal_pesan', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `tanggal_pesan` DATE NULL AFTER `status_pemesanan`");
        echo "Added paket_distribusi.tanggal_pesan\n";
    }
    if (!in_array('link_pembelian', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `link_pembelian` TEXT NULL AFTER `tanggal_pesan`");
        echo "Added paket_distribusi.link_pembelian\n";
    }
    if (!in_array('kurir_ekspedisi', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `kurir_ekspedisi` VARCHAR(100) NULL AFTER `link_pembelian`");
        echo "Added paket_distribusi.kurir_ekspedisi\n";
    }
    if (!in_array('status_penerimaan', $colsDist)) {
        $db->exec("ALTER TABLE `paket_distribusi` ADD COLUMN `status_penerimaan` VARCHAR(50) DEFAULT 'Menunggu Barang' AFTER `status`");
        echo "Added paket_distribusi.status_penerimaan\n";
    }

    // Sync initial state for existing records
    $db->exec("UPDATE `paket_distribusi` SET 
        `rute_pengiriman` = 'kantor',
        `status_pemesanan` = IF(`status` = 'Diterima', 'Tiba di Transit', 'Belum Dipesan'),
        `status_penerimaan` = IF(`status` = 'Diterima', 'Diterima di Tujuan', 'Menunggu Barang')
        WHERE `status_penerimaan` IS NULL OR `status_penerimaan` = '' OR `status_penerimaan` = 'Menunggu Barang'");

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
