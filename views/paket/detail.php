<?php
$role = $_SESSION['admin_role'] ?? 'user';
$items = $items ?? [];
$itemSummary = $itemSummary ?? ['total_item' => 0, 'total_qty' => 0, 'grand_total' => 0];

$effectivePagu = (!empty($itemSummary['grand_total']) && (float)$itemSummary['grand_total'] > 0) 
    ? (float)$itemSummary['grand_total'] 
    : (float)$paket['pagu'];

$hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
$acuan = $hasNego ? (float)$paket['pagu_nego'] : $effectivePagu;
$dpp = !empty($paket['dpp']) ? (float)$paket['dpp'] : calculateDPP($acuan);

$persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
$persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
$totalPersen = $persenPasal + $persenAdmin;

$tipePotongan = $paket['tipe_potongan'] ?? 'dpp';
$basisPotongan = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
$basisLabel = ($tipePotongan === 'kontrak') ? 'Nilai Kontrak' : 'DPP';

$pasalVal = !empty($paket['pasal_13']) ? (float)$paket['pasal_13'] : calculatePasal($basisPotongan, $persenPasal);
$adminVal = isset($paket['admin_2']) ? (float)$paket['admin_2'] : calculateAdmin($basisPotongan, $persenAdmin);
$totalPasal = !empty($paket['total_pasal']) ? (float)$paket['total_pasal'] : ($pasalVal + $adminVal);

$distribusiList = $distribusiList ?? [];
$distribusiSummary = $distribusiSummary ?? ['total_baris' => 0, 'total_tujuan' => 0, 'total_qty_dialokasikan' => 0, 'total_diterima' => 0, 'total_dikirim' => 0, 'total_belum_kirim' => 0];
$groupedDistribusi = $groupedDistribusi ?? [];
$dokumenList = $dokumenList ?? [];
$lokasiList = $lokasiList ?? [];

$activeTab = $activeTab ?? ($_GET['tab'] ?? 'info');
$validTabs = ['info', 'items', 'pemesanan', 'distribusi', 'dokumentasi', 'dokumen', 'profit'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'info';
}
$groups = $groups ?? [];
$skemas = $skemas ?? [];
?>

<!-- Master Lokasi Datalist untuk Autocomplete Cepat -->
<datalist id="list_master_lokasi">
    <?php foreach ($lokasiList as $lok): ?>
        <option value="<?= htmlspecialchars($lok['nama_lokasi']) ?>"
                data-id="<?= $lok['id'] ?>"
                data-kategori="<?= htmlspecialchars($lok['kategori']) ?>"
                data-pic="<?= htmlspecialchars($lok['nama_pic'] ?? '') ?>"
                data-kontak="<?= htmlspecialchars($lok['no_kontak'] ?? '') ?>"
                data-alamat="<?= htmlspecialchars($lok['alamat_lengkap'] ?? '') ?>"
                data-kabkota="<?= htmlspecialchars($lok['kab_kota'] ?? '') ?>">
            <?= htmlspecialchars($lok['nama_lokasi']) ?> (<?= htmlspecialchars($lok['kab_kota'] ?: $lok['kategori']) ?>)
        </option>
    <?php endforeach; ?>
</datalist>

<style>
/* Pastikan konten tab selalu tampil saat dimuat dalam popup */
div[id^="tabContent_"] {
    display: block !important;
}
</style>

<!-- ========================================== -->
<!-- MODUL KONTEN POPUP (LAZY-LOADED)           -->
<!-- ========================================== -->
<div class="w-full">
    <?php 
    $tabMap = [
        'info' => 'tab_info.php',
        'items' => 'tab_items.php',
        'pemesanan' => 'tab_pemesanan.php',
        'distribusi' => 'tab_distribusi.php',
        'dokumentasi' => 'tab_dokumentasi.php',
        'dokumen' => 'tab_dokumen.php',
        'profit' => 'tab_profit.php'
    ];
    $currentTabFile = $tabMap[$activeTab] ?? 'tab_info.php';
    require __DIR__ . '/partials/' . $currentTabFile;
    ?>
</div>

<!-- ========================================== -->
<!-- MODALS & POPUPS                            -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/modals.php'; ?>

<!-- ========================================== -->
<!-- CLIENT-SIDE SCRIPTS                        -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/scripts.php'; ?>
