<?php
$role = $_SESSION['admin_role'] ?? 'user';
$hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
$acuan = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
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

$items = $items ?? [];
$itemSummary = $itemSummary ?? ['total_item' => 0, 'total_qty' => 0, 'grand_total' => 0];
$distribusiList = $distribusiList ?? [];
$distribusiSummary = $distribusiSummary ?? ['total_baris' => 0, 'total_tujuan' => 0, 'total_qty_dialokasikan' => 0, 'total_diterima' => 0, 'total_dikirim' => 0, 'total_belum_kirim' => 0];
$groupedDistribusi = $groupedDistribusi ?? [];
$dokumenList = $dokumenList ?? [];
$lokasiList = $lokasiList ?? [];

$activeTab = $activeTab ?? ($_GET['tab'] ?? 'info');
$validTabs = ['info', 'items', 'distribusi', 'dokumen'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'info';
}
$groups = $groups ?? [];
$skemas = $skemas ?? [];

$backUrl = APP_URL . '/paket';
$backLabel = 'Kembali ke Paket';
if (isset($_GET['from']) && $_GET['from'] === 'perusahaan' && isset($_GET['source_id'])) {
    $backUrl = APP_URL . '/perusahaan/detail/' . $_GET['source_id'];
    $backLabel = 'Ke Perusahaan';
} elseif (isset($_GET['from']) && $_GET['from'] === 'group' && !empty($paket['group_id'])) {
    $backUrl = APP_URL . '/group/detail/' . $paket['group_id'];
    $backLabel = 'Ke Group';
}
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

<!-- ========================================== -->
<!-- 1. PAGE HEADER                             -->
<!-- ========================================== -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
    <div>
        <div class="flex items-center gap-2 text-slate-500 mb-1">
            <ion-icon name="cube-outline"></ion-icon>
            <span class="text-xs font-bold uppercase tracking-wider">Detail Paket Kegiatan</span>
            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-mono font-bold text-xs tracking-wider">
                #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?>
            </span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800"><?= htmlspecialchars($paket['nama_paket']) ?></h1>
        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
            <?php if (!empty($paket['perusahaan'])): ?>
                <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="text-slate-500 font-medium inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors text-xs">
                    <ion-icon name="business-outline"></ion-icon>
                    <?= htmlspecialchars($paket['perusahaan']) ?>
                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon>
                </a>
            <?php endif; ?>

            <?php if (!empty($paket['nama_group'])): ?>
                <span class="text-slate-300">&bull;</span>
                <a href="<?= APP_URL ?>/group/detail/<?= $paket['group_id'] ?>" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition-colors">
                    <ion-icon name="folder-outline"></ion-icon>
                    <span>Group: <?= htmlspecialchars($paket['nama_group']) ?></span>
                </a>
            <?php else: ?>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-400 italic">Paket Mandiri (Tanpa Group)</span>
            <?php endif; ?>

            <?php if (!empty($paket['nomor_sp'])): ?>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-600 font-mono inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md" title="Nomor Surat Pesanan / SPK">
                    <ion-icon name="document-text-outline" class="text-slate-400"></ion-icon>
                    <span>No. SP: <?= htmlspecialchars($paket['nomor_sp']) ?></span>
                </span>
            <?php endif; ?>

            <?php if (!empty($paket['kab_kota'])): ?>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-indigo-700 font-semibold inline-flex items-center gap-1 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full" title="Wilayah Kota / Kabupaten">
                    <ion-icon name="location-outline" class="text-indigo-500"></ion-icon>
                    <span><?= htmlspecialchars($paket['kab_kota']) ?></span>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="flex items-center gap-2.5">
        <a href="<?= $backUrl ?>" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5 text-xs">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span><?= $backLabel ?></span>
        </a>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <!-- Tombol Edit Paket Pop Up -->
            <button type="button" onclick="openDetailEditPaketModal()" 
                    class="px-4 py-2 bg-slate-800 text-white font-semibold rounded-xl hover:bg-slate-900 transition-all shadow-md shadow-slate-800/20 flex items-center gap-2 text-xs">
                <ion-icon name="create-outline" class="text-sm"></ion-icon>
                <span>Edit Paket</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. TAB NAVIGATION (4 TABS)                 -->
<!-- ========================================== -->
<script>
(function() {
    var validTabs = ['info', 'items', 'distribusi', 'dokumen'];
    var hash = (window.location.hash || '').replace('#', '');
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var active = (validTabs.indexOf(tabParam) !== -1) ? tabParam : ((validTabs.indexOf(hash) !== -1) ? hash : '<?= $activeTab ?>');
    if (active && validTabs.indexOf(active) !== -1) {
        var css = '';
        validTabs.forEach(function(t) {
            css += '#tabContent_' + t + ' { display: ' + (t === active ? 'block' : 'none') + ' !important; } ';
        });
        document.write('<style id="tab-preload-style">' + css + '</style>');
    }
})();
</script>

<div class="mb-5 border-b border-slate-200/90 bg-white px-2 rounded-t-xl">
    <nav class="flex space-x-2" aria-label="Tabs">
        <!-- Tab 1: Informasi Paket -->
        <button type="button" onclick="switchDetailTab('info')" id="tabBtn_info"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'info') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="information-circle-outline" class="text-base"></ion-icon>
            <span>Informasi Paket</span>
        </button>

        <!-- Tab 2: Item / Spesifikasi -->
        <button type="button" onclick="switchDetailTab('items')" id="tabBtn_items"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'items') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="list-outline" class="text-base"></ion-icon>
            <span>Item / Spesifikasi</span>
            <span id="tabBadge_itemsCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= !empty($items) ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' ?>">
                <?= count($items) ?>
            </span>
        </button>

        <!-- Tab 3: Distribusi -->
        <button type="button" onclick="switchDetailTab('distribusi')" id="tabBtn_distribusi"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'distribusi') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="navigate-outline" class="text-base"></ion-icon>
            <span>Distribusi</span>
            <span id="tabBadge_distribusiCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= !empty($distribusiList) ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' ?>">
                <?= count($distribusiList) ?>
            </span>
        </button>

        <!-- Tab 4: Dokumen BAST -->
        <button type="button" onclick="switchDetailTab('dokumen')" id="tabBtn_dokumen"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'dokumen') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="document-text-outline" class="text-base"></ion-icon>
            <span>Dokumen BAST</span>
            <span id="tabBadge_dokumenCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= !empty($groupedDistribusi) ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' ?>">
                <?= count($groupedDistribusi ?? []) ?>
            </span>
        </button>
    </nav>
</div>
<script>
(function() {
    var validTabs = ['info', 'items', 'distribusi', 'dokumen'];
    var hash = (window.location.hash || '').replace('#', '');
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var active = (validTabs.indexOf(tabParam) !== -1) ? tabParam : ((validTabs.indexOf(hash) !== -1) ? hash : '<?= $activeTab ?>');
    if (active && validTabs.indexOf(active) !== -1) {
        validTabs.forEach(function(t) {
            var btn = document.getElementById('tabBtn_' + t);
            if (btn) {
                if (t === active) {
                    btn.className = 'tab-btn py-3 px-4 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2 transition-all';
                } else {
                    btn.className = 'tab-btn py-3 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 flex items-center gap-2 transition-all';
                }
            }
        });
    }
})();
</script>

<!-- ========================================== -->
<!-- 3. TAB CONTENT PARTIALS                     -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/tab_info.php'; ?>
<?php require __DIR__ . '/partials/tab_items.php'; ?>
<?php require __DIR__ . '/partials/tab_distribusi.php'; ?>
<?php require __DIR__ . '/partials/tab_dokumen.php'; ?>

<!-- ========================================== -->
<!-- 4. MODALS & POPUPS                         -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/modals.php'; ?>

<!-- ========================================== -->
<!-- 5. CLIENT-SIDE SCRIPTS                     -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/scripts.php'; ?>
