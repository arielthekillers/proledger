<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();

// 1. Add kab_kota column to paket_kegiatan if not exists
$cols = $db->query("SHOW COLUMNS FROM paket_kegiatan LIKE 'kab_kota'")->fetchAll();
if (empty($cols)) {
    $db->exec("ALTER TABLE paket_kegiatan ADD COLUMN kab_kota VARCHAR(100) NULL AFTER nomor_sp");
    echo "Added kab_kota to paket_kegiatan.\n";
} else {
    echo "kab_kota already exists in paket_kegiatan.\n";
}

// 2. Add kabupaten_id column to lokasi_penerima if not exists
$colsLok = $db->query("SHOW COLUMNS FROM lokasi_penerima LIKE 'kabupaten_id'")->fetchAll();
if (empty($colsLok)) {
    $db->exec("ALTER TABLE lokasi_penerima ADD COLUMN kabupaten_id INT NULL AFTER kab_kota");
    echo "Added kabupaten_id to lokasi_penerima.\n";
} else {
    echo "kabupaten_id already exists in lokasi_penerima.\n";
}

// 3. Normalize kab_kota and link kabupaten_id in lokasi_penerima
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kota Bontang', kabupaten_id = 10 WHERE kab_kota LIKE '%Bontang%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Kutai Timur', kabupaten_id = 5 WHERE kab_kota LIKE '%Kutim%' OR kab_kota LIKE '%Kutai Timur%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kota Samarinda', kabupaten_id = 9 WHERE kab_kota LIKE '%Samarinda%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kota Balikpapan', kabupaten_id = 8 WHERE kab_kota LIKE '%Balikpapan%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Paser', kabupaten_id = 1 WHERE kab_kota LIKE '%Paser%' AND kab_kota NOT LIKE '%Penajam%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Penajam Paser Utara', kabupaten_id = 6 WHERE kab_kota LIKE '%Penajam%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Berau', kabupaten_id = 3 WHERE kab_kota LIKE '%Berau%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Kutai Kartanegara', kabupaten_id = 2 WHERE kab_kota LIKE '%Kartanegara%' OR kab_kota LIKE '%Kukar%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Kutai Barat', kabupaten_id = 4 WHERE kab_kota LIKE '%Kubar%' OR kab_kota LIKE '%Kutai Barat%'");
$db->exec("UPDATE lokasi_penerima SET kab_kota = 'Kabupaten Mahakam Ulu', kabupaten_id = 7 WHERE kab_kota LIKE '%Mahakam%' OR kab_kota LIKE '%Mahulu%'");
echo "Normalized lokasi_penerima kab_kota.\n";

// 4. Update test package #XW9J5 to 'Kota Bontang'
$db->exec("UPDATE paket_kegiatan SET kab_kota = 'Kota Bontang' WHERE kode_paket = 'XW9J5' OR id = 62");
echo "Updated XW9J5 kab_kota to Kota Bontang.\n";
